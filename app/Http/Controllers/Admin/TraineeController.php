<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trainee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TraineeController extends Controller
{
    public function index(Request $request)
    {
        $query = Trainee::query();

        // Search by surname
        if ($request->has('search') && $request->search) {
            $query->where('surname', 'like', '%' . $request->search . '%');
        }

        $trainees = $query->orderBy('surname')->orderBy('first_name')->paginate(50);

        return view('admin.trainees.index', compact('trainees'));
    }

    public function show($id)
    {
        $trainee = Trainee::with(['payments', 'accessibleCourses', 'myResults.course'])
            ->findOrFail($id);
        
        // Add computed properties
        $trainee->has_payment = $trainee->hasCompletedPayment();
        $trainee->total_paid_amount = $trainee->getTotalPaid();
        $trainee->payment_progress = $trainee->getPaymentProgress();
        $trainee->remaining_balance = $trainee->getRemainingBalance();
        $trainee->package_type = $trainee->getCurrentPackageType();
        
        return view('admin.trainees.show', compact('trainee'));
    }

    public function edit($id)
    {
        $trainee = Trainee::findOrFail($id);
        return view('admin.trainees.edit', compact('trainee'));
    }

    public function update(Request $request, $id)
    {
        $trainee = Trainee::findOrFail($id);

        $request->validate([
            'surname' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'gender' => 'required|in:M,F',
            'phone_number' => 'required|string|max:20',
            'username' => 'required|string|unique:trainees,username,' . $id,
            'password' => 'nullable|string|min:6',
            'status' => 'required|in:Active,Inactive',
        ]);

        $updateData = [
            'surname' => strtoupper($request->surname),
            'first_name' => strtoupper($request->first_name),
            'middle_name' => $request->middle_name ? strtoupper($request->middle_name) : null,
            'gender' => $request->gender,
            'username' => $request->username,
            'phone_number' => $request->phone_number,
            'status' => $request->status,
        ];

        // Only update password if provided
        if ($request->filled('password')) {
            $updateData['password'] = $request->password;
        }

        $trainee->update($updateData);

        return redirect()->route('admin.trainees.show', $trainee->id)
            ->with('success', 'Trainee profile updated successfully!');
    }

    public function viewProfile()
    {
        // Redirect to manage page or show a list
        return redirect()->route('admin.trainees.index');
    }

    public function create()
    {
        return view('admin.trainees.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'surname' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'gender' => 'required|in:M,F',
            'phone_number' => 'required|string|max:20',
            'username' => 'nullable|string|unique:trainees,username',
            'password' => 'nullable|string|min:6',
        ]);

        // Generate username if not provided (format: BCD/XXXXXX)
        $username = $request->username;
        if (!$username) {
            $lastTrainee = Trainee::orderBy('id', 'desc')->first();
            $nextNumber = $lastTrainee ? (int) substr($lastTrainee->username, 4) + 1 : 1;
            $username = 'BCD/' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
        }

        // Generate password if not provided
        $password = $request->password;
        if (!$password) {
            $password = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6));
        }

        Trainee::create([
            'surname' => strtoupper($request->surname),
            'first_name' => strtoupper($request->first_name),
            'middle_name' => $request->middle_name ? strtoupper($request->middle_name) : null,
            'gender' => $request->gender,
            'username' => $username,
            'password' => $password,
            'phone_number' => $request->phone_number,
            'status' => 'Inactive', // Trainees need to pay before activation
        ]);

        return redirect()->route('admin.trainees.index')->with('success', 'Trainee added successfully');
    }

    public function manage(Request $request)
    {
        $query = Trainee::with(['payments', 'accessibleCourses']);

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('surname', 'like', '%' . $search . '%')
                  ->orWhere('first_name', 'like', '%' . $search . '%')
                  ->orWhere('username', 'like', '%' . $search . '%')
                  ->orWhere('phone_number', 'like', '%' . $search . '%');
            });
        }

        $trainees = $query->orderBy('surname')->orderBy('first_name')->paginate(50);

        // Add payment and access info to each trainee
        $trainees->getCollection()->transform(function($trainee) {
            $trainee->has_payment = $trainee->hasCompletedPayment();
            $trainee->total_paid_amount = $trainee->getTotalPaid();
            $trainee->payment_progress = $trainee->getPaymentProgress();
            $trainee->remaining_balance = $trainee->getRemainingBalance();
            $trainee->package_type = $trainee->getCurrentPackageType();
            $trainee->accessible_courses_count = $trainee->accessibleCourses()->count();
            return $trainee;
        });

        $stats = [
            'total' => Trainee::count(),
            'active' => Trainee::where('status', 'Active')->count(),
            'inactive' => Trainee::where('status', 'Inactive')->count(),
            'with_payment' => Trainee::whereHas('payments', function($q) {
                $q->where('status', 'Completed');
            })->count(),
        ];

        return view('admin.trainees.manage', compact('trainees', 'stats'));
    }

    /**
     * Handle bulk actions (activate/deactivate)
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate',
            'trainee_ids' => 'required',
        ]);

        $traineeIds = json_decode($request->trainee_ids, true);
        
        if (!is_array($traineeIds) || empty($traineeIds)) {
            return redirect()->back()->with('error', 'No trainees selected');
        }

        $trainees = Trainee::whereIn('id', $traineeIds)->get();
        $successCount = 0;
        $failedCount = 0;
        $messages = [];

        foreach ($trainees as $trainee) {
            if ($request->action === 'activate') {
                if ($trainee->hasCompletedPayment()) {
                    $trainee->update(['status' => 'Active']);
                    $successCount++;
                } else {
                    $failedCount++;
                    $messages[] = $trainee->full_name . ' (no completed payment)';
                }
            } else {
                $trainee->update(['status' => 'Inactive']);
                $successCount++;
            }
        }

        $message = "{$successCount} trainee(s) " . ($request->action === 'activate' ? 'activated' : 'deactivated') . " successfully.";
        if ($failedCount > 0) {
            $message .= " {$failedCount} trainee(s) could not be activated: " . implode(', ', $messages);
        }

        return redirect()->back()->with('success', $message);
    }

    public function activate(Request $request)
    {
        if ($request->has('trainee_ids')) {
            $activated = 0;
            $noPayment = [];
            
            foreach ($request->trainee_ids as $traineeId) {
                $trainee = Trainee::find($traineeId);
                if ($trainee && $trainee->hasCompletedPayment()) {
                    $trainee->update(['status' => 'Active']);
                    $activated++;
                } else {
                    $noPayment[] = $trainee->full_name ?? 'Unknown';
                }
            }
            
            $message = "{$activated} trainee(s) activated successfully.";
            if (count($noPayment) > 0) {
                $message .= " The following trainees don't have completed payments: " . implode(', ', $noPayment);
            }
            
            return redirect()->back()->with('success', $message);
        }

        $trainees = Trainee::where('status', 'Inactive')
            ->with('payments')
            ->orderBy('surname')
            ->get()
            ->map(function($trainee) {
                $trainee->has_payment = $trainee->hasCompletedPayment();
                return $trainee;
            });
            
        return view('admin.trainees.activate', compact('trainees'));
    }

    public function deactivate(Request $request)
    {
        if ($request->has('trainee_ids')) {
            Trainee::whereIn('id', $request->trainee_ids)->update(['status' => 'Inactive']);
            return redirect()->back()->with('success', 'Trainees deactivated successfully');
        }

        $trainees = Trainee::where('status', 'Active')->orderBy('surname')->get();
        return view('admin.trainees.deactivate', compact('trainees'));
    }

    /**
     * Impersonate a trainee (view as trainee)
     */
    public function viewAs($id)
    {
        $trainee = Trainee::findOrFail($id);
        
        // Store admin user ID in session for later restoration
        session(['admin_user_id' => Auth::id()]);
        session(['impersonating' => true]);
        
        // Log in as the user associated with the trainee
        // If trainee doesn't have a user (old data), create one or use trainee's user
        if ($trainee->user) {
            Auth::login($trainee->user);
        } else {
            // For backward compatibility: if trainee has no user, we can't impersonate
            // In this case, we'd need to create a user or handle differently
            return redirect()->back()
                ->with('error', 'This trainee does not have a user account. Please update the trainee record.');
        }
        
        return redirect()->route('trainee.dashboard')
            ->with('info', 'You are now viewing as ' . $trainee->full_name);
    }

    /**
     * Stop impersonating and return to admin
     */
    public function stopImpersonating()
    {
        $adminUserId = session('admin_user_id');
        
        // Logout from current user
        Auth::logout();
        
        // Clear impersonation session
        session()->forget(['admin_user_id', 'impersonating']);
        
        // Log back in as admin
        if ($adminUserId) {
            Auth::loginUsingId($adminUserId);
        }
        
        return redirect()->route('admin.trainees.index')
            ->with('success', 'Returned to admin view');
    }
}


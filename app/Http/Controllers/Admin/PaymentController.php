<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Trainee;
use App\Models\Course;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with('trainee');

        // Search by trainee name
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('trainee', function($q) use ($search) {
                $q->where('surname', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.payments.index', compact('payments'));
    }

    public function create()
    {
        $trainees = Trainee::orderBy('surname')->get();
        return view('admin.payments.create', compact('trainees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'trainee_id' => 'required|exists:trainees,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:Cash,Bank Transfer,Mobile Money,Card,Other',
            'payment_date' => 'required|date',
            'status' => 'required|in:Pending,Completed,Failed,Refunded',
            'transaction_reference' => 'nullable|string|max:255',
            'receipt_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $payment = Payment::create($request->all());

        // If payment is completed, activate the trainee and grant course access
        if ($payment->status === 'Completed') {
            $trainee = $payment->trainee;
            $trainee->update(['status' => 'Active']);
            
            // Grant course access based on payment
            $this->grantCourseAccess($trainee, $payment);
        }

        return redirect()->route('admin.payments.index')->with('success', 'Payment recorded successfully');
    }

    public function edit($id)
    {
        $payment = Payment::with('trainee')->findOrFail($id);
        $trainees = Trainee::orderBy('surname')->get();
        return view('admin.payments.edit', compact('payment', 'trainees'));
    }

    public function update(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);
        $oldStatus = $payment->status;

        $request->validate([
            'trainee_id' => 'required|exists:trainees,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:Cash,Bank Transfer,Mobile Money,Card,Other',
            'payment_date' => 'required|date',
            'status' => 'required|in:Pending,Completed,Failed,Refunded',
            'transaction_reference' => 'nullable|string|max:255',
            'receipt_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $payment->update($request->all());

        // If payment status changed to Completed, activate the trainee and grant course access
        if ($payment->status === 'Completed' && $oldStatus !== 'Completed') {
            $trainee = $payment->trainee;
            $trainee->update(['status' => 'Active']);
            
            // Grant course access based on payment
            $this->grantCourseAccess($trainee, $payment);
        }

        // If payment status changed from Completed to something else, check if trainee has other completed payments
        if ($oldStatus === 'Completed' && $payment->status !== 'Completed') {
            if (!$payment->trainee->hasCompletedPayment()) {
                $payment->trainee->update(['status' => 'Inactive']);
            }
        }

        return redirect()->route('admin.payments.index')->with('success', 'Payment updated successfully');
    }

    public function destroy($id)
    {
        $payment = Payment::findOrFail($id);
        $trainee = $payment->trainee;
        
        $payment->delete();

        // If deleted payment was completed, check if trainee has other completed payments
        if ($payment->status === 'Completed') {
            if (!$trainee->hasCompletedPayment()) {
                $trainee->update(['status' => 'Inactive']);
            }
        }

        return redirect()->route('admin.payments.index')->with('success', 'Payment deleted successfully');
    }

    /**
     * Grant course access to trainee based on payment
     */
    private function grantCourseAccess(Trainee $trainee, Payment $payment)
    {
        $courseAccessCount = $payment->course_access_count ?? 1;
        
        if ($courseAccessCount <= 0) {
            Log::warning('Admin Payment: No course access count specified', [
                'payment_id' => $payment->id,
                'trainee_id' => $trainee->id
            ]);
            return;
        }
        
        // Get courses trainee already has access to
        $existingAccess = $trainee->accessibleCourses()->pluck('courses.id')->toArray();

        // Priority 1: Use selected_courses from trainee registration if available
        $coursesToGrant = [];
        if ($trainee->selected_courses && count($trainee->selected_courses) > 0) {
            // Filter to only include courses that are active and not already granted
            $selectedCourseIds = is_array($trainee->selected_courses) ? $trainee->selected_courses : [];
            $availableSelectedCourses = Course::where('status', 'Active')
                ->whereIn('id', $selectedCourseIds)
                ->pluck('id')
                ->toArray();
            
            // Filter out courses trainee already has access to
            $coursesToGrant = array_values(array_diff($availableSelectedCourses, $existingAccess));
            
            Log::info('Admin Payment: Using selected courses from registration', [
                'trainee_id' => $trainee->id,
                'selected_courses' => $selectedCourseIds,
                'available_selected' => $availableSelectedCourses,
                'courses_to_grant' => $coursesToGrant,
            ]);
        }
        
        // Priority 2: Fallback to first N courses if no selected_courses (backward compatibility)
        if (empty($coursesToGrant)) {
            $availableCourses = Course::where('status', 'Active')
                ->orderBy('id')
                ->get();

            // Filter out courses trainee already has access to
            $coursesToGrant = $availableCourses->whereNotIn('id', $existingAccess)
                ->take($courseAccessCount)
                ->pluck('id')
                ->toArray();
            
            Log::info('Admin Payment: Using fallback course selection', [
                'trainee_id' => $trainee->id,
                'course_access_count' => $courseAccessCount,
                'courses_to_grant' => $coursesToGrant,
            ]);
        }

        if (count($coursesToGrant) > 0) {
            $trainee->grantCourseAccess($coursesToGrant, $payment->id);
            
            Log::info('Admin Payment: Course access granted', [
                'trainee_id' => $trainee->id,
                'payment_id' => $payment->id,
                'courses_granted' => count($coursesToGrant),
                'course_ids' => $coursesToGrant,
            ]);
        } else {
            Log::warning('Admin Payment: No courses available to grant', [
                'trainee_id' => $trainee->id,
                'payment_id' => $payment->id,
                'requested_count' => $courseAccessCount,
                'existing_access_count' => count($existingAccess),
                'has_selected_courses' => !empty($trainee->selected_courses),
            ]);
        }
    }
}

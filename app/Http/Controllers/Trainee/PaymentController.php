<?php

namespace App\Http\Controllers\Trainee;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\User;
use App\Models\Trainee;
use App\Services\PayVibeService;
use App\Helpers\TraineeHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $payVibeService;

    public function __construct(PayVibeService $payVibeService)
    {
        $this->payVibeService = $payVibeService;
    }

    public function index()
    {
        $user = Auth::user();
        
        // Get or create trainee
        $trainee = TraineeHelper::getCurrentTrainee();
        
        if (!$trainee) {
            return redirect()->route('trainee.payments.create')
                ->with('info', 'Please select a course package to become a trainee.');
        }
        
        $payments = Payment::where('trainee_id', $trainee->id)
            ->orderBy('payment_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Calculate totals
        $totalPaid = Payment::where('trainee_id', $trainee->id)
            ->where('status', 'Completed')
            ->sum('amount');
        
        $totalPending = Payment::where('trainee_id', $trainee->id)
            ->where('status', 'Pending')
            ->sum('amount');

        // Update trainee's total_paid if needed
        if ($trainee->total_paid != $totalPaid) {
            $trainee->total_paid = $totalPaid;
            $trainee->save();
        }

        return view('trainee.payments.index', compact('payments', 'totalPaid', 'totalPending', 'trainee'));
    }

    /**
     * Show payment initiation form
     */
    public function create()
    {
        return view('trainee.payments.create');
    }

    /**
     * Create PayVibe payment transaction
     */
    public function store(Request $request)
    {
        $request->validate([
            'package_type' => 'required|in:single,package',
            'amount' => 'required|numeric|min:100',
            'course_access_count' => 'required|integer|min:1|max:4',
            'is_installment' => 'nullable|boolean',
            'installment_amount' => 'nullable|numeric|min:100',
        ]);

        try {
            $user = Auth::user();
            
            // Get or create trainee when they enroll
            $trainee = TraineeHelper::getOrCreateTrainee($user);
            
            $packageType = $request->package_type;
            $courseAccessCount = $request->course_access_count;
            $isInstallment = $request->has('is_installment') && $request->is_installment;
            
            // Determine total required based on package
            $totalRequired = $packageType === 'package' ? 22500 : 10000;

            // Check if this is an installment payment
            if ($isInstallment) {
                // For installments, use installment_amount or amount field
                $amount = $request->installment_amount ?? $request->amount;
                
                // Validate installment amount
                if ($amount < 100) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Installment amount must be at least ₦100');
                }
                
                // Check if amount exceeds remaining balance
                $remainingBalance = $trainee->getRemainingBalance();
                if ($remainingBalance > 0 && $amount > $remainingBalance) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Installment amount cannot exceed remaining balance of ₦' . number_format($remainingBalance, 2));
                }
                
                // Calculate installment number
                $completedPayments = $trainee->payments()
                    ->where('status', 'Completed')
                    ->where('package_type', $packageType)
                    ->count();
                $installmentNumber = $completedPayments + 1;
            } else {
                // Full payment - use amount field
                $amount = $request->amount;
                
                // Validate full payment amount
                if ($packageType === 'package' && $amount != 22500) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Full package payment must be exactly ₦22,500');
                }

                if ($packageType === 'single' && $amount != 10000) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Full single course payment must be exactly ₦10,000');
                }
                $installmentNumber = null;
            }

            // Update trainee's total required based on package type
            // Only update if not set or if it's a different package (to avoid overwriting)
            if ($trainee->total_required == 0) {
                $trainee->total_required = $totalRequired;
                $trainee->save();
            } elseif ($trainee->total_required != $totalRequired) {
                // Check if trainee has any completed payments for the new package
                $existingPayments = $trainee->payments()
                    ->where('status', 'Completed')
                    ->where('package_type', $packageType)
                    ->exists();
                
                // Only update if they're switching packages or starting fresh
                if (!$existingPayments) {
                    $trainee->total_required = $totalRequired;
                    $trainee->save();
                }
            }

            // Generate virtual account
            $result = $this->payVibeService->generateVirtualAccount(
                $trainee, 
                $amount, 
                $courseAccessCount, 
                $packageType,
                $isInstallment,
                $installmentNumber,
                $totalRequired
            );

            if (!$result['success']) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', $result['message'] ?? 'Failed to generate virtual account');
            }

            return view('trainee.payments.payvibe', [
                'payment' => $result['payment'],
                'virtualAccount' => $result['virtual_account'],
                'bankName' => $result['bank_name'],
                'accountName' => $result['account_name'],
                'finalAmount' => $result['amount'],
                'reference' => $result['reference'],
                'charges' => $result['charges'],
                'packageType' => $packageType,
                'courseAccessCount' => $courseAccessCount,
                'isInstallment' => $isInstallment,
                'totalRequired' => $totalRequired,
            ]);

        } catch (\Exception $e) {
            Log::error('Payment creation error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Something went wrong. Please try again.');
        }
    }

    /**
     * Check payment status
     */
    public function checkStatus($paymentId)
    {
        $user = Auth::user();
        $trainee = TraineeHelper::getCurrentTrainee();
        
        if (!$trainee) {
            return response()->json(['error' => 'Trainee record not found'], 404);
        }
        
        $payment = Payment::where('id', $paymentId)
            ->where('trainee_id', $trainee->id)
            ->firstOrFail();

        // Refresh payment from database
        $payment->refresh();

        return response()->json([
            'status' => $payment->status,
            'message' => $payment->status === 'Completed' 
                ? 'Payment confirmed! Your account has been activated.' 
                : 'Payment is still pending. Please wait a few more minutes.'
        ]);
    }
}

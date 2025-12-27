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
        $user = Auth::user();
        $trainee = TraineeHelper::getCurrentTrainee();
        
        // Get package info from session if available (from registration)
        $packageInfo = session('package_info');
        
        // If trainee exists and has package type, use that
        if ($trainee && $trainee->package_type) {
            $packageInfo = [
                'type' => $trainee->package_type,
                'total_amount' => $trainee->total_required ?? 0,
            ];
        }
        
        // Get active manual payment settings
        $manualPaymentSettings = \App\Models\ManualPaymentSetting::active()->ordered()->get();
        
        return view('trainee.payments.create', compact('packageInfo', 'trainee', 'manualPaymentSettings'));
    }

    /**
     * Create PayVibe payment transaction
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $trainee = TraineeHelper::getCurrentTrainee();
        
        if (!$trainee) {
            return redirect()->route('trainee.register')
                ->with('error', 'Please complete registration first');
        }

        // Get package info from trainee or session
        $packageInfo = session('package_info');
        if (!$packageInfo && $trainee->package_type) {
            $packageInfo = [
                'type' => $trainee->package_type,
                'total_amount' => $trainee->total_required ?? 0,
            ];
        }

        if (!$packageInfo) {
            return redirect()->route('trainee.register')
                ->with('error', 'Package information not found. Please register again.');
        }

        $packageType = $packageInfo['type']; // A, B, C, or D
        $totalRequired = $packageInfo['total_amount'];
        
        // For Package A: full payment of ₦10,000
        // For Packages B, C, D: initial deposit of ₦10,000
        $initialAmount = $packageType === 'A' ? 10000 : 10000;
        
        $paymentMethod = $request->input('payment_method', 'payvibe');
        
        $request->validate([
            'amount' => 'required|numeric|min:100',
            'payment_method' => 'required|in:payvibe,manual',
            'payment_receipt' => 'required_if:payment_method,manual|file|mimes:jpeg,jpg,png,pdf|max:5120',
            'payment_reference' => 'nullable|string|max:255',
        ]);

        try {
            $amount = $request->amount;
            
            // Handle manual payment
            if ($paymentMethod === 'manual') {
                return $this->handleManualPayment($request, $trainee, $packageInfo, $amount);
            }
            
            // Validate amount based on package type
            if ($packageType === 'A') {
                // Package A: must pay full amount
                if ($amount != 10000) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Package A requires full payment of ₦10,000');
                }
            } else {
                // Packages B, C, D: initial deposit of ₦10,000
                if ($amount != 10000) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Initial payment for this package is ₦10,000');
                }
            }
            
            $courseAccessCount = count(session('selected_courses', []));
            if ($courseAccessCount == 0) {
                $courseAccessCount = 1; // Default
            }

            // Determine if this is an installment payment
            // Package A: full payment (not installment)
            // Packages B, C, D: initial deposit (is installment if total > 10000)
            $isInstallment = ($packageType !== 'A' && $totalRequired > 10000);
            $installmentNumber = $isInstallment ? 1 : null;

            // Update trainee's total required if not set
            if ($trainee->total_required == 0 || $trainee->total_required != $totalRequired) {
                $trainee->total_required = $totalRequired;
                $trainee->package_type = $packageType;
                $trainee->save();
            }

            // Generate virtual account
            // For payment tracking, we'll use 'nysc_package_' + package type
            $paymentPackageType = 'nysc_package_' . strtolower($packageType);
            
            $result = $this->payVibeService->generateVirtualAccount(
                $trainee, 
                $amount, 
                $courseAccessCount, 
                $paymentPackageType,
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
     * Handle manual payment submission
     */
    private function handleManualPayment(Request $request, $trainee, $packageInfo, $amount)
    {
        try {
            $packageType = $packageInfo['type'];
            $totalRequired = $packageInfo['total_amount'];
            $courseAccessCount = count(session('selected_courses', []));
            if ($courseAccessCount == 0) {
                $courseAccessCount = 1;
            }

            // Determine if this is an installment payment
            $isInstallment = ($packageType !== 'A' && $totalRequired > 10000);
            $installmentNumber = $isInstallment ? 1 : null;

            // Upload receipt
            $receiptPath = null;
            if ($request->hasFile('payment_receipt')) {
                $receiptPath = $request->file('payment_receipt')->store('payment-receipts', 'public');
            }

            // Create payment record
            $payment = Payment::create([
                'trainee_id' => $trainee->id,
                'amount' => $amount,
                'total_required' => $totalRequired,
                'course_access_count' => $courseAccessCount,
                'package_type' => 'nysc_package_' . strtolower($packageType),
                'is_installment' => $isInstallment,
                'installment_number' => $installmentNumber,
                'payment_method' => 'Manual Payment',
                'transaction_reference' => $request->payment_reference,
                'payment_date' => now(),
                'status' => 'Pending',
                'notes' => $receiptPath ? 'Receipt uploaded: ' . basename($receiptPath) . ' | Path: ' . $receiptPath : 'Manual payment submitted',
            ]);

            return redirect()->route('trainee.payments.index')
                ->with('success', 'Manual payment submitted successfully! Your payment receipt has been uploaded and is pending verification. You will be notified once it\'s confirmed.');
                
        } catch (\Exception $e) {
            Log::error('Manual payment error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to submit manual payment. Please try again.');
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

<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Payment;
use App\Models\Trainee;

class PayVibeService
{
    protected $publicKey;
    protected $secretKey;
    protected $productIdentifier;
    protected $baseUrl;

    public function __construct()
    {
        $this->publicKey = config('services.payvibe.public_key');
        $this->secretKey = config('services.payvibe.secret_key');
        $this->productIdentifier = config('services.payvibe.product_identifier');
        $this->baseUrl = config('services.payvibe.base_url');
    }

    /**
     * Calculate PayVibe charges
     */
    public function calculateCharges($amount)
    {
        $fixedCharge = 100; // Fixed charge of ₦100
        $percentageRate = $amount >= 10000 ? 0.02 : 0.015; // 2% for ₦10,000+, 1.5% for less
        $percentageCharge = round($amount * $percentageRate, 2);
        $totalCharges = $fixedCharge + $percentageCharge;
        $finalAmount = round($amount + $totalCharges, 0);

        return [
            'original_amount' => $amount,
            'fixed_charge' => $fixedCharge,
            'percentage_rate' => $percentageRate,
            'percentage_charge' => $percentageCharge,
            'total_charges' => $totalCharges,
            'final_amount' => $finalAmount
        ];
    }

    /**
     * Generate virtual account for payment
     */
    public function generateVirtualAccount(Trainee $trainee, $amount, $courseAccessCount = 0, $packageType = null, $isInstallment = false, $installmentNumber = null, $totalRequired = null)
    {
        try {
            // Generate unique reference
            $ref = time() . rand(1000, 9999);

            // Calculate charges
            $charges = $this->calculateCharges($amount);
            $finalAmount = $charges['final_amount'];

            // Prepare headers
            $headers = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ];

            if (!empty($this->secretKey)) {
                $headers['Authorization'] = 'Bearer ' . $this->secretKey;
            }

            // Prepare request payload
            $requestData = [
                'reference' => $ref,
                'amount' => $finalAmount,
                'service' => 'sms'
            ];

            if (!empty($this->productIdentifier)) {
                $requestData['product_identifier'] = $this->productIdentifier;
            }

            // Make API call
            $response = Http::withHeaders($headers)
                ->withoutVerifying() // Bypass SSL issues if needed
                ->post($this->baseUrl . '/payments/virtual-accounts/initiate', $requestData);

            // Log response
            Log::info('PayVibe API Response', [
                'status' => $response->status(),
                'body' => $response->body(),
                'reference' => $ref,
                'amount' => $finalAmount
            ]);

            // Handle response
            if ($response->successful()) {
                $responseData = $response->json();

                if (($responseData['status'] ?? false) == 'success' || ($responseData['status'] ?? false) === true) {
                    // Extract virtual account details
                    $virtualAccount = $responseData['data']['virtual_account_number'] ?? 
                                    $responseData['data']['virtual_account'] ?? 
                                    $responseData['virtual_account_number'] ?? 
                                    $responseData['virtual_account'] ?? 'N/A';

                    $bankName = $responseData['data']['bank_name'] ?? 
                               $responseData['bank_name'] ?? 'N/A';

                    $accountName = $responseData['data']['account_name'] ?? 
                                  $responseData['account_name'] ?? 'N/A';

                    // Create payment record
                    $payment = Payment::create([
                        'trainee_id' => $trainee->id,
                        'amount' => $amount, // Original amount to credit
                        'total_required' => $totalRequired,
                        'course_access_count' => $courseAccessCount,
                        'package_type' => $packageType,
                        'is_installment' => $isInstallment,
                        'installment_number' => $installmentNumber,
                        'payment_method' => 'PayVibe',
                        'transaction_reference' => $ref,
                        'payment_date' => now(),
                        'status' => 'Pending',
                        'notes' => ($isInstallment ? 'Installment #' . $installmentNumber . ' - ' : '') . 'PayVibe payment initiated. Package: ' . ($packageType === 'package' ? '4 Courses' : '1 Course') . '. Charges: ₦' . number_format($charges['total_charges'], 2),
                        'receipt_number' => 'PAYVIBE-' . $ref,
                    ]);

                    return [
                        'success' => true,
                        'payment' => $payment,
                        'virtual_account' => $virtualAccount,
                        'bank_name' => $bankName,
                        'account_name' => $accountName,
                        'amount' => $finalAmount,
                        'reference' => $ref,
                        'charges' => $charges,
                    ];
                }
            }

            // Handle error
            Log::error('PayVibe virtual account generation failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'trainee_id' => $trainee->id
            ]);

            return [
                'success' => false,
                'message' => 'Failed to generate virtual account',
                'debug' => $response->body()
            ];

        } catch (\Exception $e) {
            Log::error('PayVibe service error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'success' => false,
                'message' => 'Service error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Verify transaction status
     */
    public function verifyTransaction($reference)
    {
        try {
            $headers = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ];

            if (!empty($this->secretKey)) {
                $headers['Authorization'] = 'Bearer ' . $this->secretKey;
            }

            $response = Http::withHeaders($headers)
                ->withoutVerifying()
                ->post($this->baseUrl . '/payments/virtual-accounts/verify', [
                    'reference' => $reference
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('PayVibe transaction verification failed', [
                'reference' => $reference,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}


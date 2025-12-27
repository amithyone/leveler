<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TestPayVibe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payvibe:test {--amount=10000 : Amount to test with}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test PayVibe API connectivity and generate a test virtual account';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing PayVibe API...');
        $this->newLine();

        // Get configuration
        $publicKey = config('services.payvibe.public_key');
        $secretKey = config('services.payvibe.secret_key');
        $productIdentifier = config('services.payvibe.product_identifier');
        $baseUrl = config('services.payvibe.base_url');

        $this->info('Configuration:');
        $this->line('  Base URL: ' . ($baseUrl ?: 'Not set'));
        $this->line('  Public Key: ' . ($publicKey ? substr($publicKey, 0, 10) . '...' : 'Not set'));
        $this->line('  Secret Key: ' . ($secretKey ? substr($secretKey, 0, 10) . '...' : 'Not set'));
        $this->line('  Product Identifier: ' . ($productIdentifier ?: 'Not set'));
        $this->newLine();

        if (empty($baseUrl) || empty($secretKey)) {
            $this->error('PayVibe configuration is incomplete. Please check your .env file.');
            return 1;
        }

        // Test amount
        $amount = (float) $this->option('amount');
        $ref = 'TEST_' . time() . '_' . rand(1000, 9999);

        $this->info('Generating test virtual account...');
        $this->line('  Reference: ' . $ref);
        $this->line('  Amount: ₦' . number_format($amount, 2));
        $this->newLine();

        try {
            // Prepare headers
            $headers = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ];

            if (!empty($secretKey)) {
                $headers['Authorization'] = 'Bearer ' . $secretKey;
            }

            // Prepare request payload
            $requestData = [
                'reference' => $ref,
                'amount' => $amount,
                'service' => 'sms'
            ];

            if (!empty($productIdentifier)) {
                $requestData['product_identifier'] = $productIdentifier;
            }

            $this->line('Request URL: ' . $baseUrl . '/payments/virtual-accounts/initiate');
            $this->line('Request Data: ' . json_encode($requestData, JSON_PRETTY_PRINT));
            $this->newLine();

            // Make API call
            $response = Http::withHeaders($headers)
                ->withoutVerifying()
                ->timeout(30)
                ->post($baseUrl . '/payments/virtual-accounts/initiate', $requestData);

            $this->info('API Response:');
            $this->line('  Status Code: ' . $response->status());
            $this->line('  Response Body:');
            $this->line($response->body());
            $this->newLine();

            if ($response->successful()) {
                $responseData = $response->json();

                if (($responseData['status'] ?? false) == 'success' || ($responseData['status'] ?? false) === true) {
                    $virtualAccount = $responseData['data']['virtual_account_number'] ?? 
                                    $responseData['data']['virtual_account'] ?? 
                                    $responseData['virtual_account_number'] ?? 
                                    $responseData['virtual_account'] ?? 'N/A';

                    $bankName = $responseData['data']['bank_name'] ?? 
                               $responseData['bank_name'] ?? 'N/A';

                    $accountName = $responseData['data']['account_name'] ?? 
                                  $responseData['account_name'] ?? 'N/A';

                    $this->info('✅ PayVibe API is working!');
                    $this->newLine();
                    $this->info('Virtual Account Details:');
                    $this->line('  Bank Name: ' . $bankName);
                    $this->line('  Account Name: ' . $accountName);
                    $this->line('  Account Number: ' . $virtualAccount);
                    $this->line('  Reference: ' . $ref);
                    $this->line('  Amount: ₦' . number_format($amount, 2));
                    $this->newLine();

                    return 0;
                } else {
                    $this->error('❌ PayVibe API returned an error status');
                    $this->line('Response: ' . json_encode($responseData, JSON_PRETTY_PRINT));
                    return 1;
                }
            } else {
                $this->error('❌ PayVibe API request failed');
                $this->line('Status: ' . $response->status());
                $this->line('Response: ' . $response->body());
                return 1;
            }

        } catch (\Exception $e) {
            $this->error('❌ Error testing PayVibe API:');
            $this->line('  ' . $e->getMessage());
            $this->newLine();
            $this->line('Stack Trace:');
            $this->line($e->getTraceAsString());
            return 1;
        }
    }
}

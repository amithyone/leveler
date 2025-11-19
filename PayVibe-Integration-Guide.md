# PayVibe Integration Guide

## Table of Contents
1. [Overview](#overview)
2. [Prerequisites](#prerequisites)
3. [Setup & Configuration](#setup--configuration)
4. [Database Schema](#database-schema)
5. [API Integration](#api-integration)
6. [Webhook Implementation](#webhook-implementation)
7. [Frontend Integration](#frontend-integration)
8. [Testing](#testing)
9. [Production Deployment](#production-deployment)
10. [Troubleshooting](#troubleshooting)
11. [Notes](#notes)

---

## Overview

PayVibe is a Nigerian payment gateway that allows users to transfer money directly to your business account. This documentation provides a complete guide to integrate PayVibe into any website using Laravel PHP framework.

**Key Features:**
- Direct bank transfer integration
- Real-time webhook notifications
- Automatic wallet crediting
- Transaction tracking
- Mobile-responsive UI`

---

## Prerequisites

**Required Software:**
- PHP 8.0+
- Laravel 9.0+
- MySQL 5.7+
- Composer
- SSL Certificate (for production)

**PayVibe Account Setup:**
1. Register at [PayVibe](https://payvibe.ng)
2. Complete KYC verification
3. Get your API credentials
4. Configure webhook URL

---

## Setup & Configuration

### 1. Environment Variables

Add to your `.env` file:

```env
# PayVibe Configuration
PAYVIBE_API_KEY=your_api_key_here
PAYVIBE_API_SECRET=your_api_secret_here
PAYVIBE_BUSINESS_ACCOUNT=your_business_account_number
PAYVIBE_WEBHOOK_SECRET=your_webhook_secret_here

# XtraPay Business (Optional)
XTRABUSINESS_WEBHOOK_URL=https://xtrapay.cash/api/webhook/receive-transaction
XTRABUSINESS_API_KEY=your_xtrapay_api_key
XTRABUSINESS_API_CODE=your_site_code
```

### 2. Service Configuration

Create or update `config/services.php`:

```php
<?php

return [
    'payvibe' => [
        'api_key' => env('PAYVIBE_API_KEY'),
        'api_secret' => env('PAYVIBE_API_SECRET'),
        'business_account' => env('PAYVIBE_BUSINESS_ACCOUNT'),
        'webhook_secret' => env('PAYVIBE_WEBHOOK_SECRET'),
        'base_url' => 'https://api.payvibe.ng/v1',
    ],
];
```

---

## Database Schema

### 1. Users Table Migration

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('wallet', 15, 2)->default(0.00);
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('wallet');
        });
    }
};
```

### 2. Transactions Table Migration

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('ref_id')->unique();
            $table->decimal('amount', 15, 2);
            $table->decimal('final_amount', 15, 2);
            $table->decimal('charge', 15, 2)->default(0.00);
            $table->integer('method')->default(119); // 119 = PayVibe
            $table->integer('type')->default(2); // 2 = Credit
            $table->integer('status')->default(0); // 0=Pending, 1=Processing, 2=Completed
            $table->json('detail')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
            $table->index(['user_id', 'status']);
            $table->index('ref_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
```

### 3. Models

#### User Model
```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = [
        'name', 'email', 'password', 'wallet'
    ];

    protected $casts = [
        'wallet' => 'decimal:2',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
```

#### Transaction Model
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id', 'ref_id', 'amount', 'final_amount', 
        'charge', 'method', 'type', 'status', 'detail'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'charge' => 'decimal:2',
        'detail' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

---

## API Integration

### 1. PayVibe Service

Create `app/Services/PayVibeService.php`:

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Transaction;
use App\Models\User;

class PayVibeService
{
    protected $apiKey;
    protected $apiSecret;
    protected $businessAccount;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.payvibe.api_key');
        $this->apiSecret = config('services.payvibe.api_secret');
        $this->businessAccount = config('services.payvibe.business_account');
        $this->baseUrl = config('services.payvibe.base_url');
    }

    /**
     * Generate PayVibe account details for user
     */
    public function getAccountDetails()
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->get($this->baseUrl . '/account/details');

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('PayVibe account details failed', [
                'status' => $response->status(),
                'response' => $response->json()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('PayVibe service error', [
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Create a new transaction
     */
    public function createTransaction(User $user, $amount)
    {
        try {
            // Calculate charges (1.5% + ₦100)
            $percentageCharge = $amount * 0.015;
            $fixedCharge = 100;
            $totalCharge = $percentageCharge + $fixedCharge;
            $finalAmount = $amount - $totalCharge;

            // Generate unique reference
            $refId = 'PAYVIBE_' . time() . '_' . $user->id;

            // Create transaction record
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'ref_id' => $refId,
                'amount' => $amount,
                'final_amount' => $finalAmount,
                'charge' => $totalCharge,
                'method' => 119, // PayVibe method ID
                'type' => 2, // Credit
                'status' => 0, // Pending
                'detail' => [
                    'percentage_charge' => $percentageCharge,
                    'fixed_charge' => $fixedCharge,
                    'total_charge' => $totalCharge,
                    'payment_method' => 'payvibe'
                ]
            ]);

            return $transaction;
        } catch (\Exception $e) {
            Log::error('Transaction creation failed', [
                'user_id' => $user->id,
                'amount' => $amount,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Verify transaction status
     */
    public function verifyTransaction($reference)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->get($this->baseUrl . '/transaction/verify/' . $reference);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Transaction verification failed', [
                'reference' => $reference,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}
```

### 2. Webhook Service

Create `app/Services/WebhookService.php`:

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Transaction;
use App\Models\User;

class WebhookService
{
    /**
     * Send transaction notification to external services
     */
    public static function sendToXtrabusiness(Transaction $transaction, User $user, $status = 'successful')
    {
        try {
            $webhookUrl = env('XTRABUSINESS_WEBHOOK_URL');
            $apiKey = env('XTRABUSINESS_API_KEY');
            $apiCode = env('XTRABUSINESS_API_CODE', 'your_site_code');

            if (empty($webhookUrl) || empty($apiKey)) {
                Log::warning('External webhook not configured');
                return false;
            }

            $payload = [
                'site_api_code' => $apiCode,
                'reference' => $transaction->ref_id,
                'amount' => $transaction->final_amount ?? $transaction->amount,
                'currency' => 'NGN',
                'status' => $status === 'successful' ? 'success' : $status,
                'payment_method' => 'payvibe',
                'customer_email' => $user->email,
                'customer_name' => $user->name,
                'description' => 'Deposit via PayVibe',
                'external_id' => (string) $transaction->id,
                'metadata' => [
                    'transaction_id' => $transaction->id,
                    'user_id' => $user->id,
                    'original_amount' => $transaction->amount,
                    'final_amount' => $transaction->final_amount ?? $transaction->amount,
                    'charge' => $transaction->charge ?? null,
                    'payment_reference' => $transaction->ref_id,
                    'site_name' => 'your-site.com',
                    'site_url' => 'https://your-site.com'
                ],
                'timestamp' => $transaction->created_at ? $transaction->created_at->toISOString() : now()->toISOString()
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-API-Key' => $apiKey,
                'User-Agent' => 'Your-Site-Webhook/1.0'
            ])->timeout(30)->post($webhookUrl, $payload);

            Log::info('External webhook sent', [
                'transaction_id' => $transaction->id,
                'status_code' => $response->status(),
                'response' => $response->json()
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('External webhook failed', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
```

---

## Webhook Implementation

### 1. Webhook Controller

Create `app/Http/Controllers/PayVibeWebhookController.php`:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Transaction;
use App\Models\User;
use App\Services\WebhookService;

class PayVibeWebhookController extends Controller
{
    /**
     * Handle incoming PayVibe webhooks
     */
    public function handle(Request $request)
    {
        try {
            $webhookData = $request->all();
            Log::info('PayVibeWebhook: Received webhook', $webhookData);

            // Extract webhook data
            $reference = $webhookData['reference'] ?? null;
            $transactionAmount = $webhookData['transaction_amount'] ?? 0;
            $settledAmount = $webhookData['settled_amount'] ?? 0;
            $platformFee = $webhookData['platform_fee'] ?? 0;
            $netAmount = $webhookData['net_amount'] ?? 0;

            if (!$reference) {
                Log::error('PayVibeWebhook: Missing reference', $webhookData);
                return response()->json(['error' => 'Missing reference'], 400);
            }

            // Find transaction by reference
            $transaction = Transaction::where('ref_id', $reference)->first();

            if (!$transaction) {
                Log::error('PayVibeWebhook: Transaction not found', [
                    'reference' => $reference,
                    'transaction_amount' => $transactionAmount,
                    'net_amount' => $netAmount
                ]);
                return response()->json(['error' => 'Transaction not found'], 404);
            }

            // Check if transaction already processed
            if ($transaction->status == 2) {
                Log::info('PayVibeWebhook: Transaction already processed', [
                    'reference' => $reference,
                    'transaction_id' => $transaction->id
                ]);
                return response()->json(['message' => 'Transaction already processed']);
            }

            // Process the payment
            $user = User::find($transaction->user_id);
            if ($user) {
                // Credit user with the net amount from PayVibe
                $amountToCredit = $netAmount;
                
                // Update wallet balance
                $user->increment('wallet', $amountToCredit);
                
                // Get updated balance for logging
                $newBalance = $user->fresh()->wallet;

                // Update transaction status and store both amounts
                $transaction->status = 2; // Completed
                $transaction->amount = $transactionAmount; // Keep original amount
                $transaction->final_amount = $netAmount; // Store the net amount
                $transaction->save();

                // Send notification to external services
                WebhookService::sendToXtrabusiness($transaction, $user, 'successful');

                Log::info('PayVibeWebhook: Payment processed successfully', [
                    'reference' => $reference,
                    'transaction_amount' => $transactionAmount,
                    'settled_amount' => $settledAmount,
                    'platform_fee' => $platformFee,
                    'net_amount_credited' => $amountToCredit,
                    'user_id' => $user->id,
                    'new_balance' => $newBalance
                ]);

                return response()->json(['message' => 'Payment processed successfully']);
            }

            Log::error('PayVibeWebhook: User not found', [
                'reference' => $reference,
                'user_id' => $transaction->user_id
            ]);

            return response()->json(['error' => 'User not found'], 404);

        } catch (\Exception $e) {
            Log::error('PayVibeWebhook: Processing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
```

### 2. Webhook Route

Add to `routes/api.php`:

```php
Route::post('/webhook/payvibe', [PayVibeWebhookController::class, 'handle']);
```

---

## Frontend Integration

### 1. Fund Wallet Controller

Create `app/Http/Controllers/FundWalletController.php`:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\PayVibeService;
use App\Models\Transaction;

class FundWalletController extends Controller
{
    protected $payVibeService;

    public function __construct(PayVibeService $payVibeService)
    {
        $this->payVibeService = $payVibeService;
    }

    /**
     * Show fund wallet page
     */
    public function index()
    {
        $user = Auth::user();
        $transactions = Transaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('fund-wallet', compact('transactions'));
    }

    /**
     * Create PayVibe transaction
     */
    public function createPayVibeTransaction(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:100|max:1000000'
        ]);

        try {
            $user = Auth::user();
            $amount = $request->amount;

            // Create transaction
            $transaction = $this->payVibeService->createTransaction($user, $amount);

            if (!$transaction) {
                return redirect()->back()->with('error', 'Failed to create transaction');
            }

            // Get PayVibe account details
            $accountDetails = $this->payVibeService->getAccountDetails();

            return view('payvibe-payment', compact('transaction', 'accountDetails'));

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }
}
```

### 2. Fund Wallet View

Create `resources/views/fund-wallet.blade.php`:

```blade
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Fund Wallet</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('fund.payvibe') }}">
                        @csrf
                        <div class="form-group">
                            <label for="amount">Amount (₦)</label>
                            <input type="number" 
                                   class="form-control @error('amount') is-invalid @enderror" 
                                   id="amount" 
                                   name="amount" 
                                   min="100" 
                                   max="1000000" 
                                   required>
                            @error('amount')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Continue with PayVibe
                        </button>
                    </form>

                    <!-- Latest Transactions -->
                    <div class="mt-4">
                        <h5>Latest Transactions</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $data)
                                        <tr>
                                            <td style="font-size: 12px;">{{ $data->id }}</td>
                                            <td style="font-size: 12px;">₦{{ number_format($data->final_amount, 2) }}</td>
                                            <td>
                                                @if ($data->status == 1)
                                                    <span class="badge badge-warning">Processing</span>
                                                @elseif ($data->status == 2)
                                                    <span class="badge badge-success">Completed</span>
                                                @else
                                                    <span class="badge badge-secondary">Pending</span>
                                                @endif
                                            </td>
                                            <td style="font-size: 12px;">{{ $data->created_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>
@endsection
```

---

## Testing

### 1. Local Testing

- Use Laravel’s built-in server:  
  `php artisan serve`
- Test the fund wallet flow and ensure transactions are created.
- Use a tool like [Postman](https://www.postman.com/) or `curl` to simulate PayVibe webhook POSTs to your `/api/webhook/payvibe` endpoint with sample payloads:

```json
{
  "reference": "PAYVIBE_1753783892_1951",
  "transaction_amount": 500,
  "settled_amount": 497.5,
  "platform_fee": 70,
  "net_amount": 430,
  "credited_at": "2025-07-29T11:13:16+01:00"
}
```

- Confirm that the user’s wallet is credited with the `net_amount` and the transaction status is updated.

### 2. End-to-End

- Fund your PayVibe business account and perform a real transfer.
- Confirm the webhook is received and processed.
- Check that the transaction is visible in the user’s transaction history.

---

## Production Deployment

1. **Set environment variables** in your production `.env`.
2. **Ensure HTTPS** is enabled for webhook security.
3. **Whitelist your server IP** in PayVibe dashboard if required.
4. **Monitor logs** for errors and webhook delivery.
5. **Set up log rotation** to prevent large log files.

---

## Troubleshooting

- **Webhook not received:**  
  - Check your server logs.
  - Ensure your endpoint is publicly accessible and uses HTTPS.
  - Verify the webhook URL in PayVibe dashboard.

- **User not credited:**  
  - Confirm the webhook payload matches your expectations.
  - Check for errors in the webhook controller.

- **Duplicate credits:**  
  - Ensure you check if a transaction is already completed before crediting.

- **Log file too large:**  
  - Use daily log rotation and/or a cleanup script.

---

## Notes

- Always validate and sanitize all incoming webhook data.
- Keep your API keys and secrets secure.
- Regularly review your transaction logs for suspicious activity.

---

**You now have a full PayVibe integration blueprint for any Laravel-based website.**
If you need a sample repository or further customization, let me know! 
@extends('layouts.trainee')

@section('title', 'Make Payment')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-credit-card"></i> Make Payment</h1>
    <p>Fund your account to activate your training</p>
</div>

<div class="section">
    @if(session('error'))
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        {{ session('error') }}
    </div>
    @endif

    <div class="payment-form-card">
        <h2 class="form-title">Payment Details</h2>
        
        @if(isset($packageInfo))
        <div class="package-info-box" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem;">
            <h3 style="margin: 0 0 0.5rem 0;"><i class="fas fa-gift"></i> Package {{ $packageInfo['type'] }}</h3>
            <p style="margin: 0.25rem 0; opacity: 0.9;">Total Package Amount: <strong>₦{{ number_format($packageInfo['total_amount']) }}</strong></p>
            @if($packageInfo['type'] === 'A')
                <p style="margin: 0.25rem 0; opacity: 0.9;">Payment: Full payment of ₦10,000</p>
            @else
                <p style="margin: 0.25rem 0; opacity: 0.9;">Initial Payment: ₦10,000 (Remaining balance can be paid later)</p>
            @endif
        </div>
        @endif
        
        <form method="POST" action="{{ route('trainee.payments.store') }}" id="payment-form" enctype="multipart/form-data">
            @csrf
            
            <input type="hidden" name="amount" id="amount" value="{{ isset($packageInfo) && $packageInfo['type'] === 'A' ? '10000' : '10000' }}">
            
            <div class="form-group">
                <label>
                    <i class="fas fa-money-bill-wave"></i> Payment Amount
                </label>
                <div class="payment-amount-display" style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; text-align: center;">
                    <div style="font-size: 2.5rem; font-weight: 700; color: #667eea; margin-bottom: 0.5rem;">
                        ₦{{ isset($packageInfo) ? number_format($packageInfo['type'] === 'A' ? 10000 : 10000) : '10,000' }}
                    </div>
                    @if(isset($packageInfo) && $packageInfo['type'] === 'A')
                        <p style="margin: 0; color: #666;">Full payment for Package A</p>
                    @elseif(isset($packageInfo))
                        <p style="margin: 0; color: #666;">Initial deposit for Package {{ $packageInfo['type'] }}</p>
                        <p style="margin: 0.5rem 0 0 0; color: #666; font-size: 0.9rem;">
                            Remaining: ₦{{ number_format($packageInfo['total_amount'] - 10000) }}
                        </p>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label>
                    <i class="fas fa-credit-card"></i> Payment Method <span class="required">*</span>
                </label>
                <div class="payment-method-options">
                    <label class="payment-method-option">
                        <input type="radio" name="payment_method" value="payvibe" checked onchange="togglePaymentMethod()">
                        <div class="method-content">
                            <i class="fas fa-university"></i>
                            <span>PayVibe Bank Transfer</span>
                            <small>Automatic verification</small>
                        </div>
                    </label>
                    <label class="payment-method-option">
                        <input type="radio" name="payment_method" value="manual" onchange="togglePaymentMethod()">
                        <div class="method-content">
                            <i class="fas fa-hand-holding-usd"></i>
                            <span>Manual Payment</span>
                            <small>Bank transfer details</small>
                        </div>
                    </label>
                </div>
            </div>

            <!-- PayVibe Payment Info -->
            <div id="payvibe-info" class="payment-method-info">
                <div class="method-badge">
                    <i class="fas fa-university"></i>
                    <span>PayVibe Bank Transfer</span>
                </div>
                <p class="method-description">
                    Transfer directly to our bank account. Course access will be granted automatically once payment is confirmed.
                </p>
            </div>

            <!-- Manual Payment Info -->
            <div id="manual-info" class="payment-method-info" style="display: none;">
                <div class="method-badge" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <i class="fas fa-hand-holding-usd"></i>
                    <span>Manual Payment</span>
                </div>
                <p class="method-description">
                    Transfer directly to our bank account using the details below. After payment, upload your receipt for verification.
                </p>
                
                @if(isset($manualPaymentSettings) && $manualPaymentSettings->count() > 0)
                <div class="manual-payment-accounts">
                    @foreach($manualPaymentSettings as $setting)
                    <div class="manual-account-card">
                        <h4><i class="fas fa-university"></i> {{ $setting->name }}</h4>
                        <div class="account-details">
                            <div class="detail-row">
                                <span class="detail-label"><i class="fas fa-building"></i> Bank Name:</span>
                                <span class="detail-value">{{ $setting->bank_name }}</span>
                            </div>
                            <div class="detail-row highlight-row">
                                <span class="detail-label"><i class="fas fa-user"></i> Bank Account Name:</span>
                                <span class="detail-value account-name">{{ $setting->account_name }}</span>
                                <button type="button" class="copy-btn" onclick="copyToClipboard('{{ $setting->account_name }}', this)" title="Copy Account Name">
                                    <i class="fas fa-copy"></i> Copy
                                </button>
                            </div>
                            <div class="detail-row highlight-row">
                                <span class="detail-label"><i class="fas fa-hashtag"></i> Bank Account Number:</span>
                                <span class="detail-value account-number">{{ $setting->account_number }}</span>
                                <button type="button" class="copy-btn" onclick="copyToClipboard('{{ $setting->account_number }}', this)" title="Copy Account Number">
                                    <i class="fas fa-copy"></i> Copy
                                </button>
                            </div>
                            @if($setting->payment_instructions)
                            <div class="payment-instructions">
                                <strong><i class="fas fa-info-circle"></i> Instructions:</strong>
                                <p>{{ $setting->payment_instructions }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    No manual payment accounts configured. Please contact support.
                </div>
                @endif

                <div class="manual-payment-upload" style="margin-top: 20px;">
                    <label for="payment_receipt">
                        <i class="fas fa-file-upload"></i> Upload Payment Receipt <span class="required">*</span>
                    </label>
                    <input type="file" id="payment_receipt" name="payment_receipt" accept="image/*,.pdf" class="form-control" required>
                    <small class="form-help">Upload a screenshot or PDF of your payment receipt (Max: 5MB)</small>
                </div>

                <div class="form-group" style="margin-top: 15px;">
                    <label for="payment_reference">
                        <i class="fas fa-hashtag"></i> Transaction Reference
                    </label>
                    <input type="text" id="payment_reference" name="payment_reference" class="form-control" placeholder="Enter your transaction reference (optional)">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-large" id="submitBtn">
                    <i class="fas fa-arrow-right"></i> <span id="submitBtnText">Continue to Payment</span>
                </button>
                <a href="{{ route('trainee.payments.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>

    <div class="info-box">
        <h3><i class="fas fa-info-circle"></i> Payment Information</h3>
        <ul>
            <li>Payments are processed securely through PayVibe</li>
            <li>You'll receive virtual account details after submitting</li>
            <li>Transfer the exact amount to the provided account</li>
            <li>Your account will be activated automatically (usually within 1-5 minutes)</li>
            <li>You'll receive a confirmation once payment is processed</li>
        </ul>
    </div>
</div>

<script>
function selectPackage(type, amount, courseCount) {
    const paymentType = document.querySelector('input[name="payment_type"]:checked')?.value || 'full';
    
    if (paymentType === 'full') {
        document.getElementById('amount').value = amount;
    } else {
        // For installments, use the installment amount input if set, otherwise default
        const installmentInput = document.getElementById('installment_amount');
        if (installmentInput && installmentInput.value) {
            document.getElementById('amount').value = installmentInput.value;
        } else {
            // Set default installment amount (1/4 of total)
            const defaultInstallment = Math.max(100, Math.floor(amount / 4));
            installmentInput.value = defaultInstallment;
            document.getElementById('amount').value = defaultInstallment;
        }
    }
    
    document.getElementById('course_access_count').value = courseCount;
    
    // Update radio button
    document.getElementById(type).checked = true;
    
    // Update visual selection
    document.querySelectorAll('.package-option').forEach(opt => {
        opt.classList.remove('selected');
    });
    if (event && event.currentTarget) {
        event.currentTarget.classList.add('selected');
    }
}

function toggleInstallmentOptions() {
    const paymentType = document.querySelector('input[name="payment_type"]:checked').value;
    const installmentGroup = document.getElementById('installment-amount-group');
    const isInstallmentInput = document.getElementById('is_installment');
    
    if (paymentType === 'installment') {
        installmentGroup.style.display = 'block';
        isInstallmentInput.value = '1';
        
        // Set default installment amount based on selected package
        const selectedPackage = document.querySelector('input[name="package_type"]:checked');
        if (selectedPackage) {
            const packageType = selectedPackage.value;
            const totalRequired = packageType === 'package' ? 22500 : 10000;
            document.getElementById('installment_amount').value = Math.max(100, Math.floor(totalRequired / 4));
        }
    } else {
        installmentGroup.style.display = 'none';
        isInstallmentInput.value = '0';
        document.getElementById('installment_amount').value = '';
        
        // Reset amount to full package amount
        const selectedPackage = document.querySelector('input[name="package_type"]:checked');
        if (selectedPackage) {
            const packageType = selectedPackage.value;
            const amount = packageType === 'package' ? 22500 : 10000;
            document.getElementById('amount').value = amount;
        }
    }
}

// Update amount when installment amount changes
document.addEventListener('DOMContentLoaded', function() {
    const installmentInput = document.getElementById('installment_amount');
    if (installmentInput) {
        installmentInput.addEventListener('input', function() {
            const paymentType = document.querySelector('input[name="payment_type"]:checked').value;
            if (paymentType === 'installment' && this.value) {
                document.getElementById('amount').value = this.value;
            }
        });
    }
    
    // Initialize payment method display
    togglePaymentMethod();
});

function togglePaymentMethod() {
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
    if (!paymentMethod) return;
    
    const method = paymentMethod.value;
    const payvibeInfo = document.getElementById('payvibe-info');
    const manualInfo = document.getElementById('manual-info');
    const receiptInput = document.getElementById('payment_receipt');
    const submitBtnText = document.getElementById('submitBtnText');
    
    if (method === 'manual') {
        if (payvibeInfo) payvibeInfo.style.display = 'none';
        if (manualInfo) manualInfo.style.display = 'block';
        if (receiptInput) {
            receiptInput.required = true;
        }
        if (submitBtnText) {
            submitBtnText.textContent = 'Submit Payment';
        }
    } else {
        if (payvibeInfo) payvibeInfo.style.display = 'block';
        if (manualInfo) manualInfo.style.display = 'none';
        if (receiptInput) {
            receiptInput.required = false;
            receiptInput.value = '';
        }
        if (submitBtnText) {
            submitBtnText.textContent = 'Continue to Payment';
        }
    }
}

function copyToClipboard(text, button) {
    navigator.clipboard.writeText(text).then(function() {
        const originalHTML = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check"></i> Copied!';
        button.style.background = '#10b981';
        setTimeout(function() {
            button.innerHTML = originalHTML;
            button.style.background = '';
        }, 2000);
    }).catch(function(err) {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.opacity = '0';
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            const originalHTML = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check"></i> Copied!';
            button.style.background = '#10b981';
            setTimeout(function() {
                button.innerHTML = originalHTML;
                button.style.background = '';
            }, 2000);
        } catch (err) {
            alert('Failed to copy: ' + err);
        }
        document.body.removeChild(textArea);
    });
}
</script>

<style>
.package-options {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 15px;
}

.package-option {
    position: relative;
}

.package-option input[type="radio"] {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.package-label {
    display: block;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    padding: 20px;
    cursor: pointer;
    transition: all 0.3s;
    background: white;
}

.package-option:hover .package-label,
.package-option.selected .package-label {
    border-color: #667eea;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
    transform: translateY(-2px);
}

.package-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.package-header h3 {
    margin: 0;
    font-size: 20px;
    font-weight: 700;
    color: #333;
}

.package-badge {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.package-price {
    font-size: 32px;
    font-weight: 700;
    color: #667eea;
    margin-bottom: 15px;
}

.package-details {
    color: #666;
    font-size: 14px;
}

.package-details p {
    margin: 5px 0;
}

.package-savings {
    color: #10b981;
    font-weight: 600;
    font-size: 13px;
}

.payment-type-options {
    display: flex;
    gap: 15px;
    margin-top: 10px;
}

.payment-type-option {
    flex: 1;
    position: relative;
}

.payment-type-option input[type="radio"] {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.type-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 15px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s;
    background: white;
    text-align: center;
}

.type-content i {
    font-size: 24px;
    color: #667eea;
}

.type-content span {
    font-weight: 600;
    color: #333;
    font-size: 14px;
}

.payment-type-option input[type="radio"]:checked + .type-content,
.payment-type-option:hover .type-content {
    border-color: #667eea;
    background: #f0f4ff;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.15);
}

.payment-method-options {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-top: 10px;
}

.payment-method-option {
    position: relative;
}

.payment-method-option input[type="radio"] {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.method-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 20px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s;
    background: white;
    text-align: center;
}

.method-content i {
    font-size: 32px;
    color: #667eea;
}

.method-content span {
    font-weight: 600;
    color: #333;
    font-size: 16px;
}

.method-content small {
    font-size: 12px;
    color: #666;
}

.payment-method-option input[type="radio"]:checked + .method-content,
.payment-method-option:hover .method-content {
    border-color: #667eea;
    background: #f0f4ff;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.15);
}

.manual-payment-accounts {
    margin-top: 20px;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.manual-account-card {
    background: white;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    padding: 20px;
}

.manual-account-card h4 {
    margin: 0 0 15px 0;
    color: #333;
    font-size: 18px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.account-details {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.detail-row {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.detail-label {
    font-weight: 600;
    color: #666;
    min-width: 120px;
}

.detail-value {
    color: #333;
    font-weight: 500;
}

.account-number,
.account-name {
    font-family: monospace;
    font-size: 16px;
    background: #f5f7fa;
    padding: 5px 10px;
    border-radius: 4px;
}

.highlight-row {
    background: #f0f4ff;
    padding: 12px;
    border-radius: 6px;
    border-left: 4px solid #667eea;
    margin: 5px 0;
}

.highlight-row .detail-label {
    color: #667eea;
    font-weight: 700;
}

.highlight-row .detail-value {
    color: #333;
    font-weight: 600;
    font-size: 16px;
}

.copy-btn {
    background: #667eea;
    color: white;
    border: none;
    padding: 5px 12px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
    transition: background 0.3s;
}

.copy-btn:hover {
    background: #5568d3;
}

.payment-instructions {
    margin-top: 15px;
    padding: 15px;
    background: #f0f4ff;
    border-left: 4px solid #667eea;
    border-radius: 4px;
}

.payment-instructions strong {
    display: block;
    margin-bottom: 8px;
    color: #667eea;
}

.payment-instructions p {
    margin: 0;
    color: #555;
    line-height: 1.6;
}

.manual-payment-upload {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border: 2px dashed #e0e0e0;
}

.required {
    color: #ef4444;
}
</style>
@endsection

<style>
.payment-form-card {
    background: white;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.form-title {
    font-size: 24px;
    font-weight: 700;
    color: #333;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
}

.form-group {
    margin-bottom: 25px;
}

.form-group label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    color: #333;
    margin-bottom: 10px;
    font-size: 14px;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 16px;
    transition: all 0.3s;
}

.form-control:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-control.error {
    border-color: #ef4444;
}

.error-message {
    color: #ef4444;
    font-size: 13px;
    margin-top: 5px;
    display: block;
}

.form-help {
    color: #666;
    font-size: 13px;
    margin-top: 5px;
    display: block;
}

.payment-method-info {
    background: #f5f7fa;
    border-radius: 8px;
    padding: 20px;
}

.method-badge {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    margin-bottom: 15px;
}

.method-description {
    color: #666;
    font-size: 14px;
    line-height: 1.6;
    margin: 0;
}

.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
    flex-wrap: wrap;
}

.btn-large {
    padding: 15px 30px;
    font-size: 16px;
    font-weight: 600;
}

.info-box {
    background: #f0f4ff;
    border: 2px solid #667eea;
    border-radius: 12px;
    padding: 20px;
}

.info-box h3 {
    color: #667eea;
    font-size: 18px;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.info-box ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.info-box li {
    padding: 8px 0;
    padding-left: 25px;
    position: relative;
    color: #555;
}

.info-box li:before {
    content: "✓";
    position: absolute;
    left: 0;
    color: #667eea;
    font-weight: bold;
}

@media (max-width: 768px) {
    .payment-form-card {
        padding: 20px;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn-large {
        width: 100%;
    }
}
</style>


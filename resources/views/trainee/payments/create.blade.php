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
        
        <form method="POST" action="{{ route('trainee.payments.store') }}" id="payment-form">
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
                    <i class="fas fa-credit-card"></i> Payment Method
                </label>
                <div class="payment-method-info">
                    <div class="method-badge">
                        <i class="fas fa-university"></i>
                        <span>PayVibe Bank Transfer</span>
                    </div>
                    <p class="method-description">
                        Transfer directly to our bank account. Course access will be granted automatically once payment is confirmed.
                    </p>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-large">
                    <i class="fas fa-arrow-right"></i> Continue to Payment
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
});
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


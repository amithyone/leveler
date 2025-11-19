@extends('layouts.trainee')

@section('title', 'PayVibe Payment Instructions')

@section('content')
<div class="payvibe-payment-page">
    <div class="page-header">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
            <div>
                <h1 class="gradient-text">PayVibe Payment 🔥</h1>
                <p>Transfer the exact amount to the virtual account below</p>
            </div>
            <a href="{{ route('trainee.payments.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Payments
            </a>
        </div>
    </div>

    <!-- Payment Details Card -->
    <div class="payment-details-card">
        <div class="card-icon">💰</div>
        <h2>Payment Instructions</h2>
        <p class="subtitle">Transfer the exact amount to the virtual account below</p>

        <!-- Account Details -->
        <div class="account-details">
            <div class="account-row">
                <span class="label">Bank Name:</span>
                <span class="value" id="bank-name">{{ $bankName }}</span>
            </div>
            <div class="account-row">
                <span class="label">Account Name:</span>
                <span class="value" id="account-name">{{ $accountName }}</span>
            </div>
            <div class="account-row highlight">
                <span class="label">Account Number:</span>
                <span class="value copy-text" id="account-number" onclick="copyAccountNumber()">{{ $virtualAccount }}</span>
            </div>
        </div>

        <button onclick="copyAccountNumber()" class="copy-btn">
            <i class="fas fa-copy"></i> Copy Account Number
        </button>
    </div>

    <!-- Payment Summary -->
    <div class="payment-summary-card">
        <h3>Payment Summary</h3>
        <div class="summary-row">
            <span class="label">Amount to Fund:</span>
            <span class="value amount" id="amount-to-fund">₦{{ number_format($charges['original_amount'], 2) }}</span>
        </div>
        <div class="summary-row">
            <span class="label">Service Charges:</span>
            <span class="value charges" id="service-charges">₦{{ number_format($charges['total_charges'], 2) }}</span>
        </div>
        <div class="summary-row total">
            <span class="label">Total to Transfer:</span>
            <span class="value total-amount" id="total-amount">₦{{ number_format($finalAmount, 2) }}</span>
        </div>
    </div>

    <!-- Reference -->
    <div class="reference-card">
        <div class="reference-row">
            <span class="label">Reference:</span>
            <span class="value copy-text" id="reference" onclick="copyReference()">{{ $reference }}</span>
        </div>
        <button onclick="copyReference()" class="copy-btn-secondary">
            <i class="fas fa-copy"></i> Copy Reference
        </button>
    </div>

    <!-- Instructions -->
    <div class="instructions-card">
        <h3><i class="fas fa-info-circle"></i> Instructions:</h3>
        <ol class="instructions-list">
            <li>Copy the account number above</li>
            <li>Transfer exactly <strong id="instruction-amount">₦{{ number_format($finalAmount, 2) }}</strong> to the account</li>
            <li>Your account will be credited with <strong id="instruction-credit">₦{{ number_format($charges['original_amount'], 2) }}</strong> automatically</li>
            <li>Confirmation usually takes 1-5 minutes</li>
        </ol>
    </div>

    <!-- Status Check -->
    <div class="status-check-card">
        <p class="status-text">Already transferred?</p>
        <button onclick="checkPaymentStatus()" class="check-status-btn" id="check-status-btn">
            <i class="fas fa-check-circle"></i> I've Made the Transfer
        </button>
        <p class="status-note">Your wallet will be credited automatically when payment is confirmed</p>
        <div class="auto-check-indicator" id="auto-check-indicator">
            <span class="pulse-icon">🔄</span>
            <span>Auto-checking every 10 seconds...</span>
        </div>
    </div>
</div>

<!-- Alert Container -->
<div id="alert-container"></div>

<script>
let autoCheckInterval = null;
const paymentId = {{ $payment->id }};
const checkStatusUrl = "{{ route('trainee.payments.check-status', $payment->id) }}";

// Copy account number
function copyAccountNumber() {
    const text = document.getElementById('account-number').textContent;
    copyToClipboard(text, 'Account number copied to clipboard! 📋');
}

// Copy reference
function copyReference() {
    const text = document.getElementById('reference').textContent;
    copyToClipboard(text, 'Reference copied to clipboard! 📋');
}

// Copy to clipboard helper
function copyToClipboard(text, successMessage) {
    navigator.clipboard.writeText(text).then(() => {
        showAlert(successMessage, 'success');
    }).catch(() => {
        // Fallback for older browsers
        const textarea = document.createElement('textarea');
        textarea.value = text;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        showAlert(successMessage, 'success');
    });
}

// Show alert notification
function showAlert(message, type = 'success') {
    const container = document.getElementById('alert-container');
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i> ${message}`;
    container.appendChild(alert);

    setTimeout(() => {
        alert.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => alert.remove(), 300);
    }, 3000);
}

// Check payment status
function checkPaymentStatus() {
    const btn = document.getElementById('check-status-btn');
    const originalHTML = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Checking...';

    fetch(checkStatusUrl)
        .then(response => response.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = originalHTML;

            if (data.status === 'Completed') {
                showAlert(data.message || 'Payment confirmed! Your account has been activated.', 'success');
                // Redirect after 2 seconds
                setTimeout(() => {
                    window.location.href = "{{ route('trainee.payments.index') }}";
                }, 2000);
            } else {
                showAlert(data.message || 'Payment is still pending. Please wait a few more minutes.', 'error');
            }
        })
        .catch(error => {
            btn.disabled = false;
            btn.innerHTML = originalHTML;
            showAlert('Error checking payment status. Please try again.', 'error');
        });
}

// Auto-check payment status every 10 seconds
function startAutoCheck() {
    autoCheckInterval = setInterval(() => {
        fetch(checkStatusUrl)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'Completed') {
                    clearInterval(autoCheckInterval);
                    showAlert('Payment confirmed! Redirecting...', 'success');
                    setTimeout(() => {
                        window.location.href = "{{ route('trainee.payments.index') }}";
                    }, 2000);
                }
            })
            .catch(error => {
                // Silently fail for auto-check
            });
    }, 10000); // Check every 10 seconds
}

// Start auto-check when page loads
document.addEventListener('DOMContentLoaded', function() {
    startAutoCheck();
});

// Clean up interval when page unloads
window.addEventListener('beforeunload', function() {
    if (autoCheckInterval) {
        clearInterval(autoCheckInterval);
    }
});
</script>

<style>
.payvibe-payment-page {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

.gradient-text {
    background: linear-gradient(135deg, #ef4444, #fbbf24, #f59e0b);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-weight: 700;
}

.payment-details-card {
    background: linear-gradient(135deg, #ef4444 0%, #fbbf24 50%, #ef4444 100%);
    border-radius: 16px;
    padding: 30px;
    color: white;
    margin-bottom: 20px;
    box-shadow: 0 4px 20px rgba(239, 68, 68, 0.3);
    text-align: center;
}

.card-icon {
    font-size: 48px;
    margin-bottom: 15px;
}

.payment-details-card h2 {
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 10px;
}

.subtitle {
    opacity: 0.9;
    margin-bottom: 25px;
    font-size: 14px;
}

.account-details {
    background: rgba(0, 0, 0, 0.2);
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    text-align: left;
}

.account-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.account-row:last-child {
    border-bottom: none;
}

.account-row.highlight {
    border-top: 2px solid rgba(255, 255, 255, 0.3);
    padding-top: 20px;
    margin-top: 10px;
}

.account-row .label {
    opacity: 0.8;
    font-size: 14px;
}

.account-row .value {
    font-weight: 700;
    font-size: 18px;
}

.account-row.highlight .value {
    font-size: 24px;
}

.copy-text {
    cursor: pointer;
    transition: all 0.3s;
}

.copy-text:hover {
    opacity: 0.8;
    transform: scale(1.05);
}

.copy-btn {
    width: 100%;
    background: rgba(255, 255, 255, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: white;
    padding: 12px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.copy-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    border-color: rgba(255, 255, 255, 0.5);
}

.payment-summary-card,
.reference-card,
.instructions-card,
.status-check-card {
    background: white;
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.payment-summary-card h3,
.instructions-card h3 {
    font-size: 20px;
    font-weight: 700;
    color: #333;
    margin-bottom: 20px;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #e0e0e0;
}

.summary-row:last-child {
    border-bottom: none;
}

.summary-row.total {
    border-top: 2px solid #e0e0e0;
    padding-top: 15px;
    margin-top: 10px;
}

.summary-row .label {
    color: #666;
    font-size: 14px;
}

.summary-row .value {
    font-weight: 600;
    font-size: 16px;
}

.summary-row .value.amount {
    color: #fbbf24;
}

.summary-row .value.charges {
    color: #ef4444;
}

.summary-row.total .value {
    font-size: 24px;
    color: #667eea;
}

.reference-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.reference-row .label {
    color: #666;
    font-size: 14px;
}

.reference-row .value {
    font-family: monospace;
    font-weight: 700;
    color: #333;
    font-size: 16px;
}

.copy-btn-secondary {
    width: 100%;
    background: #f5f7fa;
    border: 1px solid #e0e0e0;
    color: #333;
    padding: 10px;
    border-radius: 8px;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s;
}

.copy-btn-secondary:hover {
    background: #e0e0e0;
}

.instructions-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.instructions-list li {
    padding: 10px 0;
    padding-left: 30px;
    position: relative;
    color: #555;
    line-height: 1.6;
}

.instructions-list li:before {
    content: counter(step-counter);
    counter-increment: step-counter;
    position: absolute;
    left: 0;
    background: #667eea;
    color: white;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 12px;
}

.instructions-list {
    counter-reset: step-counter;
}

.status-check-card {
    text-align: center;
}

.status-text {
    color: #666;
    margin-bottom: 15px;
    font-size: 14px;
}

.check-status-btn {
    width: 100%;
    background: linear-gradient(135deg, #ef4444 0%, #fbbf24 100%);
    border: none;
    color: white;
    padding: 15px;
    border-radius: 12px;
    font-weight: 700;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4);
}

.check-status-btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(239, 68, 68, 0.5);
}

.check-status-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.status-note {
    font-size: 12px;
    color: #999;
    margin-top: 15px;
}

.auto-check-indicator {
    margin-top: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-size: 12px;
    color: #10b981;
}

.pulse-icon {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 0.5; }
    50% { opacity: 1; }
}

#alert-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 10000;
}

.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 10px;
    font-weight: 600;
    animation: slideIn 0.3s ease;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.alert-success {
    background: rgba(34, 197, 94, 0.2);
    border: 2px solid rgba(34, 197, 94, 0.5);
    color: #059669;
}

.alert-error {
    background: rgba(239, 68, 68, 0.2);
    border: 2px solid rgba(239, 68, 68, 0.5);
    color: #dc2626;
}

@keyframes slideIn {
    from {
        transform: translateX(400px);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOut {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(400px);
        opacity: 0;
    }
}

@media (max-width: 768px) {
    .payvibe-payment-page {
        padding: 15px;
    }
    
    .payment-details-card {
        padding: 20px;
    }
    
    .account-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }
}
</style>
@endsection


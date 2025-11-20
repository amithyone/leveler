<?php $__env->startSection('title', 'My Payments'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <h1><i class="fas fa-money-bill-wave"></i> My Payments</h1>
            <p>View your payment history and status</p>
        </div>
        <a href="<?php echo e(route('trainee.payments.create')); ?>" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-plus"></i> Make Payment
        </a>
    </div>
</div>

<?php
    $trainee = Auth::guard('trainee')->user();
    $packageType = $trainee->getCurrentPackageType();
    $totalRequired = $trainee->getTotalRequiredForPackage();
    $totalPaidForPackage = $trainee->getTotalPaid($packageType);
    $remainingBalance = $trainee->getRemainingBalance();
    $paymentProgress = $trainee->getPaymentProgress();
    $hasFullyPaid = $trainee->hasFullyPaid();
    $packageName = $packageType === 'package' ? '4 Courses Package' : ($packageType === 'single' ? 'Single Course' : 'No Package');
?>

<!-- Payment Summary -->
<div class="payment-summary">
    <div class="summary-card">
        <div class="summary-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="summary-info">
            <div class="summary-label">Total Paid</div>
            <div class="summary-amount">₦<?php echo e(number_format($totalPaid, 2)); ?></div>
        </div>
    </div>

    <div class="summary-card">
        <div class="summary-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
            <i class="fas fa-clock"></i>
        </div>
        <div class="summary-info">
            <div class="summary-label">Pending</div>
            <div class="summary-amount">₦<?php echo e(number_format($totalPending, 2)); ?></div>
        </div>
    </div>

    <?php if($totalRequired > 0): ?>
    <div class="summary-card">
        <div class="summary-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <i class="fas fa-graduation-cap"></i>
        </div>
        <div class="summary-info">
            <div class="summary-label">Package</div>
            <div class="summary-amount" style="font-size: 16px;"><?php echo e($packageName); ?></div>
        </div>
    </div>

    <div class="summary-card">
        <div class="summary-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <i class="fas fa-target"></i>
        </div>
        <div class="summary-info">
            <div class="summary-label">Total Required</div>
            <div class="summary-amount">₦<?php echo e(number_format($totalRequired, 2)); ?></div>
        </div>
    </div>

    <div class="summary-card">
        <div class="summary-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
            <i class="fas fa-money-bill-wave"></i>
        </div>
        <div class="summary-info">
            <div class="summary-label">Paid (<?php echo e($packageName); ?>)</div>
            <div class="summary-amount">₦<?php echo e(number_format($totalPaidForPackage, 2)); ?></div>
        </div>
    </div>

    <div class="summary-card <?php echo e($hasFullyPaid ? 'completed' : ''); ?>">
        <div class="summary-icon" style="background: linear-gradient(135deg, <?php echo e($hasFullyPaid ? '#10b981' : '#ef4444'); ?> 0%, <?php echo e($hasFullyPaid ? '#059669' : '#dc2626'); ?> 100%);">
            <i class="fas fa-<?php echo e($hasFullyPaid ? 'check-double' : 'exclamation-circle'); ?>"></i>
        </div>
        <div class="summary-info">
            <div class="summary-label"><?php echo e($hasFullyPaid ? 'Fully Paid' : 'Remaining'); ?></div>
            <div class="summary-amount">₦<?php echo e(number_format($remainingBalance, 2)); ?></div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php if($totalRequired > 0 && !$hasFullyPaid): ?>
<div class="payment-progress-card">
    <h3>Payment Progress - <?php echo e($packageName); ?></h3>
    <div class="progress-container">
        <div class="progress-bar-large">
            <div class="progress-fill-large" style="width: <?php echo e($paymentProgress); ?>%"></div>
            <span class="progress-text-overlay"><?php echo e(number_format($paymentProgress, 1)); ?>%</span>
        </div>
        <div class="progress-info">
            <span>₦<?php echo e(number_format($totalPaidForPackage, 2)); ?> of ₦<?php echo e(number_format($totalRequired, 2)); ?> paid</span>
            <span>₦<?php echo e(number_format($remainingBalance, 2)); ?> remaining</span>
        </div>
    </div>
    <p class="progress-note">
        <i class="fas fa-info-circle"></i>
        Certificates will be available once payment is fully completed for your <?php echo e($packageName); ?>.
    </p>
</div>
<?php endif; ?>

<!-- Payment History -->
<div class="section">
    <h2 class="section-title">Payment History</h2>

    <?php if($payments->count() > 0): ?>
    <div class="payments-list">
        <?php $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="payment-item">
            <div class="payment-icon">
                <?php if($payment->status === 'Completed'): ?>
                    <i class="fas fa-check-circle" style="color: #10b981;"></i>
                <?php elseif($payment->status === 'Pending'): ?>
                    <i class="fas fa-clock" style="color: #f59e0b;"></i>
                <?php elseif($payment->status === 'Failed'): ?>
                    <i class="fas fa-times-circle" style="color: #ef4444;"></i>
                <?php else: ?>
                    <i class="fas fa-undo" style="color: #8b5cf6;"></i>
                <?php endif; ?>
            </div>
            
            <div class="payment-details">
                <div class="payment-header">
                    <h3>₦<?php echo e(number_format($payment->amount, 2)); ?></h3>
                    <span class="payment-date">
                        <i class="fas fa-calendar"></i>
                        <?php echo e($payment->payment_date->format('M d, Y')); ?>

                    </span>
                </div>
                
                <div class="payment-meta">
                    <span class="payment-method">
                        <i class="fas fa-credit-card"></i>
                        <?php echo e($payment->payment_method); ?>

                    </span>
                    <?php if($payment->is_installment): ?>
                    <span class="installment-badge">
                        <i class="fas fa-calendar-alt"></i>
                        Installment #<?php echo e($payment->installment_number); ?>

                    </span>
                    <?php endif; ?>
                    <?php if($payment->receipt_number): ?>
                    <span class="receipt-number">
                        <i class="fas fa-receipt"></i>
                        Receipt: <?php echo e($payment->receipt_number); ?>

                    </span>
                    <?php endif; ?>
                </div>
                
                <?php if($payment->total_required): ?>
                <div class="payment-package-info">
                    <span class="package-info">
                        <i class="fas fa-info-circle"></i>
                        Package: <?php echo e($payment->package_type === 'package' ? '4 Courses (₦22,500)' : '1 Course (₦10,000)'); ?>

                    </span>
                </div>
                <?php endif; ?>
                
                <?php if($payment->transaction_reference): ?>
                <div class="transaction-ref">
                    <i class="fas fa-hashtag"></i>
                    Reference: <?php echo e($payment->transaction_reference); ?>

                </div>
                <?php endif; ?>
                
                <?php if($payment->notes): ?>
                <div class="payment-notes">
                    <i class="fas fa-sticky-note"></i>
                    <?php echo e($payment->notes); ?>

                </div>
                <?php endif; ?>
            </div>
            
            <div class="payment-status">
                <span class="status-badge 
                    <?php echo e($payment->status === 'Completed' ? 'status-active' : ''); ?>

                    <?php echo e($payment->status === 'Pending' ? 'status-pending' : ''); ?>

                    <?php echo e($payment->status === 'Failed' ? 'status-inactive' : ''); ?>

                    <?php echo e($payment->status === 'Refunded' ? 'status-refunded' : ''); ?>">
                    <?php echo e($payment->status); ?>

                </span>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <?php if($payments->hasPages()): ?>
    <div class="pagination-wrapper">
        <?php echo e($payments->links()); ?>

    </div>
    <?php endif; ?>
    <?php else: ?>
    <div class="empty-state">
        <div class="empty-icon">
            <i class="fas fa-money-bill-wave"></i>
        </div>
        <h3>No Payment Records</h3>
        <p>You don't have any payment records yet.</p>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<style>
.payment-summary {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.summary-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.summary-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    flex-shrink: 0;
}

.summary-info {
    flex: 1;
}

.summary-label {
    font-size: 13px;
    color: #666;
    margin-bottom: 5px;
}

.summary-amount {
    font-size: 24px;
    font-weight: 700;
    color: #333;
}

.payments-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.payment-item {
    background: white;
    border-radius: 12px;
    padding: 20px;
    display: flex;
    align-items: flex-start;
    gap: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: transform 0.2s, box-shadow 0.2s;
}

.payment-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.12);
}

.payment-icon {
    font-size: 32px;
    flex-shrink: 0;
}

.payment-details {
    flex: 1;
}

.payment-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
    flex-wrap: wrap;
    gap: 10px;
}

.payment-header h3 {
    font-size: 24px;
    font-weight: 700;
    color: #333;
    margin: 0;
}

.payment-date {
    font-size: 13px;
    color: #666;
    display: flex;
    align-items: center;
    gap: 5px;
}

.payment-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 10px;
    font-size: 13px;
    color: #666;
}

.payment-method,
.receipt-number {
    display: flex;
    align-items: center;
    gap: 5px;
}

.transaction-ref {
    font-size: 12px;
    color: #999;
    margin-top: 8px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.installment-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: #fef3c7;
    color: #92400e;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.payment-package-info {
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px solid #e0e0e0;
}

.package-info {
    font-size: 12px;
    color: #666;
    display: flex;
    align-items: center;
    gap: 6px;
}

.payment-notes {
    font-size: 13px;
    color: #666;
    margin-top: 10px;
    padding: 10px;
    background: #f5f7fa;
    border-radius: 6px;
    display: flex;
    align-items: flex-start;
    gap: 8px;
}

.payment-status {
    flex-shrink: 0;
}

.status-refunded {
    background-color: #8b5cf6;
    color: white;
}

.payment-progress-card {
    background: white;
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.payment-progress-card h3 {
    font-size: 18px;
    font-weight: 700;
    color: #333;
    margin-bottom: 15px;
}

.progress-container {
    margin-bottom: 15px;
}

.progress-bar-large {
    width: 100%;
    height: 30px;
    background: #e0e0e0;
    border-radius: 15px;
    overflow: hidden;
    margin-bottom: 10px;
}

.progress-fill-large {
    height: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    transition: width 0.3s ease;
    position: relative;
}

.progress-bar-large {
    position: relative;
}

.progress-text-overlay {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: #333;
    font-weight: 700;
    font-size: 12px;
    z-index: 1;
    pointer-events: none;
}

.progress-info {
    display: flex;
    justify-content: space-between;
    font-size: 14px;
    color: #666;
}

.progress-note {
    margin-top: 15px;
    padding: 12px;
    background: #f0f4ff;
    border-left: 4px solid #667eea;
    border-radius: 6px;
    color: #555;
    font-size: 13px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.summary-card.completed {
    border: 2px solid #10b981;
}

@media (max-width: 768px) {
    .payment-item {
        flex-direction: column;
    }
    
    .payment-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .payment-status {
        align-self: flex-start;
    }
}
</style>


<?php echo $__env->make('layouts.trainee', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/leveler/resources/views/trainee/payments/index.blade.php ENDPATH**/ ?>
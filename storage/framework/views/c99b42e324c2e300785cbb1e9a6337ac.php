<?php $__env->startSection('title', 'My Certificates'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1><i class="fas fa-certificate"></i> My Certificates</h1>
    <p>View and download your earned certificates</p>
</div>

<?php if(!$trainee->hasFullyPaid()): ?>
<div class="payment-warning">
    <div class="warning-content">
        <i class="fas fa-exclamation-triangle"></i>
        <div class="warning-text">
            <h3>Payment Incomplete</h3>
            <p>You must complete your payment to access certificates.</p>
            <div class="payment-status">
                <?php
                    $packageType = $trainee->getCurrentPackageType();
                    $totalRequired = $trainee->getTotalRequiredForPackage();
                    $totalPaid = $trainee->getTotalPaid($packageType);
                    $remainingBalance = $trainee->getRemainingBalance();
                    $packageName = $packageType === 'package' ? '4 Courses Package' : ($packageType === 'single' ? 'Single Course' : 'Package');
                ?>
                <div class="status-item">
                    <span class="label">Package:</span>
                    <span class="value"><?php echo e($packageName); ?></span>
                </div>
                <div class="status-item">
                    <span class="label">Total Required:</span>
                    <span class="value">₦<?php echo e(number_format($totalRequired, 2)); ?></span>
                </div>
                <div class="status-item">
                    <span class="label">Total Paid:</span>
                    <span class="value">₦<?php echo e(number_format($totalPaid, 2)); ?></span>
                </div>
                <div class="status-item highlight">
                    <span class="label">Remaining Balance:</span>
                    <span class="value">₦<?php echo e(number_format($remainingBalance, 2)); ?></span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?php echo e($trainee->getPaymentProgress()); ?>%"></div>
                </div>
                <p class="progress-text"><?php echo e(number_format($trainee->getPaymentProgress(), 1)); ?>% Paid</p>
            </div>
            <a href="<?php echo e(route('trainee.payments.create')); ?>" class="btn btn-primary">
                <i class="fas fa-credit-card"></i> Complete Payment
            </a>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if($certificates->count() > 0): ?>
<div class="certificates-grid">
    <?php $__currentLoopData = $certificates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $certificate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="certificate-card">
        <div class="certificate-icon">
            <i class="fas fa-certificate"></i>
        </div>
        <div class="certificate-content">
            <h3><?php echo e($certificate->course->title); ?></h3>
            <p class="certificate-code"><?php echo e($certificate->course->code); ?></p>
            <div class="certificate-meta">
                <span><i class="fas fa-calendar"></i> <?php echo e($certificate->completed_at->format('F d, Y')); ?></span>
                <span><i class="fas fa-percentage"></i> Score: <?php echo e(number_format($certificate->percentage, 1)); ?>%</span>
            </div>
        </div>
        <div class="certificate-actions">
            <a href="<?php echo e(route('trainee.certificates.view', $certificate->id)); ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-eye"></i> View
            </a>
            <a href="<?php echo e(route('trainee.certificates.download', $certificate->id)); ?>" class="btn btn-outline btn-sm">
                <i class="fas fa-download"></i> Download
            </a>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php else: ?>
<div class="empty-state">
    <div class="empty-icon">
        <i class="fas fa-certificate"></i>
    </div>
    <h3>No Certificates Yet</h3>
    <p>Complete assessments and pass courses to earn certificates.</p>
    <a href="<?php echo e(route('trainee.courses.index')); ?>" class="btn btn-primary">
        <i class="fas fa-book"></i> Browse Courses
    </a>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<style>
.payment-warning {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    border: 2px solid #f59e0b;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 30px;
}

.warning-content {
    display: flex;
    gap: 15px;
    align-items: flex-start;
}

.warning-content i {
    font-size: 32px;
    color: #f59e0b;
    flex-shrink: 0;
}

.warning-text {
    flex: 1;
}

.warning-text h3 {
    margin: 0 0 8px 0;
    color: #92400e;
    font-size: 20px;
}

.warning-text > p {
    margin: 0 0 15px 0;
    color: #78350f;
}

.payment-status {
    background: white;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
}

.status-item {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid #e0e0e0;
}

.status-item:last-child {
    border-bottom: none;
}

.status-item.highlight {
    background: #fef3c7;
    padding: 12px;
    border-radius: 6px;
    margin-top: 10px;
    border: none;
}

.status-item .label {
    color: #666;
    font-size: 14px;
}

.status-item .value {
    font-weight: 700;
    color: #333;
    font-size: 16px;
}

.status-item.highlight .value {
    color: #f59e0b;
    font-size: 18px;
}

.progress-bar {
    width: 100%;
    height: 20px;
    background: #e0e0e0;
    border-radius: 10px;
    overflow: hidden;
    margin-top: 15px;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    transition: width 0.3s ease;
}

.progress-text {
    text-align: center;
    margin-top: 8px;
    font-size: 12px;
    color: #666;
    font-weight: 600;
}
</style>


<?php echo $__env->make('layouts.trainee', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/leveler/resources/views/trainee/certificates/index.blade.php ENDPATH**/ ?>
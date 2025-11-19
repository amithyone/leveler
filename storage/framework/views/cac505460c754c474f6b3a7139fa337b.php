

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1 class="page-title">Payment Management</h1>
</div>

<div class="page-content">
    <div class="content-section">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 class="section-title">All Payments</h2>
            <a href="<?php echo e(route('admin.payments.create')); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Record Payment
            </a>
        </div>

        <?php if(session('success')): ?>
        <div class="alert alert-success">
            <?php echo e(session('success')); ?>

        </div>
        <?php endif; ?>

        <!-- Search and Filter -->
        <form method="GET" action="<?php echo e(route('admin.payments.index')); ?>" style="margin-bottom: 20px;">
            <div class="form-row">
                <div class="form-group">
                    <input type="text" name="search" placeholder="Search by trainee name or username..." class="form-group input" value="<?php echo e(request('search')); ?>" style="width: 100%;">
                </div>
                <div class="form-group">
                    <select name="status" class="form-group input" style="width: 100%;">
                        <option value="">All Statuses</option>
                        <option value="Pending" <?php echo e(request('status') === 'Pending' ? 'selected' : ''); ?>>Pending</option>
                        <option value="Completed" <?php echo e(request('status') === 'Completed' ? 'selected' : ''); ?>>Completed</option>
                        <option value="Failed" <?php echo e(request('status') === 'Failed' ? 'selected' : ''); ?>>Failed</option>
                        <option value="Refunded" <?php echo e(request('status') === 'Refunded' ? 'selected' : ''); ?>>Refunded</option>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <a href="<?php echo e(route('admin.payments.index')); ?>" class="btn btn-secondary">Clear</a>
                </div>
            </div>
        </form>

        <?php if($payments->count() > 0): ?>
        <div class="table-container">
            <table class="trainee-table">
                <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Trainee</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Payment Date</th>
                        <th>Status</th>
                        <th>Receipt #</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e(($payments->currentPage() - 1) * $payments->perPage() + $index + 1); ?></td>
                        <td><?php echo e($payment->trainee->full_name ?? 'N/A'); ?></td>
                        <td>₦<?php echo e(number_format($payment->amount, 2)); ?></td>
                        <td><?php echo e($payment->payment_method); ?></td>
                        <td><?php echo e($payment->payment_date->format('M d, Y')); ?></td>
                        <td>
                            <span class="status-badge 
                                <?php echo e($payment->status === 'Completed' ? 'status-active' : ''); ?>

                                <?php echo e($payment->status === 'Failed' ? 'status-inactive' : ''); ?>

                                <?php echo e($payment->status === 'Pending' ? 'status-pending' : ''); ?>

                                <?php echo e($payment->status === 'Refunded' ? 'status-refunded' : ''); ?>">
                                <?php echo e($payment->status); ?>

                            </span>
                        </td>
                        <td><?php echo e($payment->receipt_number ?? 'N/A'); ?></td>
                        <td class="actions-cell">
                            <a href="<?php echo e(route('admin.payments.edit', $payment->id)); ?>" class="action-btn" title="Edit">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
                            <form action="<?php echo e(route('admin.payments.destroy', $payment->id)); ?>" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this payment?');">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="action-btn" title="Delete" style="color: #ef4444;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <?php if($payments->hasPages()): ?>
        <div class="pagination-wrapper">
            <?php echo e($payments->links()); ?>

        </div>
        <?php endif; ?>
        <?php else: ?>
        <p style="padding: 40px; text-align: center; color: #666;">
            No payments found. <a href="<?php echo e(route('admin.payments.create')); ?>">Record a payment</a>
        </p>
        <?php endif; ?>
    </div>
</div>

<style>
.status-pending {
    background-color: #f59e0b;
    color: white;
}

.status-refunded {
    background-color: #8b5cf6;
    color: white;
}
</style>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\LENOVO LEGION\Documents\leveler\resources\views/admin/payments/index.blade.php ENDPATH**/ ?>
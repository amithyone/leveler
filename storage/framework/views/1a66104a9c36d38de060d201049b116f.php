

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1 class="page-title">Admin Dashboard</h1>
</div>

<div class="page-content">
    <div class="content-section">
        <h2 class="section-title">Trainee Profiles</h2>
        
        <?php if(session('success')): ?>
        <div class="alert alert-success">
            <?php echo e(session('success')); ?>

        </div>
        <?php endif; ?>

        <div class="table-container">
            <table class="trainee-table">
                <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Status</th>
                        <th>Phone number</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $trainees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $trainee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e(($trainees->currentPage() - 1) * $trainees->perPage() + $index + 1); ?></td>
                        <td><?php echo e($trainee->full_name); ?></td>
                        <td><?php echo e($trainee->gender); ?></td>
                        <td><?php echo e($trainee->username); ?></td>
                        <td><?php echo e($trainee->password); ?></td>
                        <td>
                            <span class="status-badge <?php echo e($trainee->status === 'Active' ? 'status-active' : 'status-inactive'); ?>">
                                <?php echo e($trainee->status); ?>

                            </span>
                        </td>
                        <td><?php echo e($trainee->phone_number); ?></td>
                        <td class="actions-cell">
                            <div class="action-buttons">
                                <a href="<?php echo e(route('admin.trainees.show', $trainee->id)); ?>" class="action-btn" title="View Profile">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?php echo e(route('admin.trainees.edit', $trainee->id)); ?>" class="action-btn" title="Edit">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                <?php if($trainee->status === 'Active'): ?>
                                <a href="<?php echo e(route('admin.trainees.view-as', $trainee->id)); ?>" class="action-btn view-as-btn" title="View As Trainee" style="color: #667eea;">
                                    <i class="fas fa-user-secret"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px;">
                            No trainees found. <a href="<?php echo e(route('admin.trainees.create')); ?>">Add a trainee</a>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($trainees->hasPages()): ?>
        <div class="pagination-wrapper">
            <?php echo e($trainees->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\LENOVO LEGION\Documents\leveler\resources\views/admin/trainees/index.blade.php ENDPATH**/ ?>
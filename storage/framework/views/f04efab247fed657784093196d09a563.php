

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1 class="page-title">Deactivate Trainees</h1>
</div>

<div class="page-content">
    <div class="content-section">
        <h2 class="section-title">Deactivate Trainees</h2>

        <?php if(session('success')): ?>
        <div class="alert alert-success">
            <?php echo e(session('success')); ?>

        </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('admin.trainees.deactivate')); ?>">
            <?php echo csrf_field(); ?>
            <?php echo method_field('POST'); ?>
            
            <?php if($trainees->count() > 0): ?>
            <div class="table-container">
                <table class="trainee-table">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="select-all"></th>
                            <th>S/N</th>
                            <th>Name</th>
                            <th>Gender</th>
                            <th>Username</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $trainees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $trainee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><input type="checkbox" name="trainee_ids[]" value="<?php echo e($trainee->id); ?>"></td>
                            <td><?php echo e($index + 1); ?></td>
                            <td><?php echo e($trainee->full_name); ?></td>
                            <td><?php echo e($trainee->gender); ?></td>
                            <td><?php echo e($trainee->username); ?></td>
                            <td>
                                <span class="status-badge status-active"><?php echo e($trainee->status); ?></span>
                            </td>
                            <td class="actions-cell">
                                <a href="<?php echo e(route('admin.trainees.view-as', $trainee->id)); ?>" class="action-btn view-as-btn" title="View As Trainee">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Deactivate Selected</button>
            </div>
            <?php else: ?>
            <p>No active trainees found.</p>
            <?php endif; ?>
        </form>
    </div>
</div>

<script>
document.getElementById('select-all')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('input[name="trainee_ids[]"]');
    checkboxes.forEach(cb => cb.checked = this.checked);
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\LENOVO LEGION\Documents\leveler\resources\views/admin/trainees/deactivate.blade.php ENDPATH**/ ?>
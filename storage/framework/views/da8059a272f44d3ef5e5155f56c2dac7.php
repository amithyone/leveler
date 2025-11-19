

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1 class="page-title">Add Trainee</h1>
</div>

<div class="page-content">
    <div class="content-section">
        <h2 class="section-title">Add New Trainee</h2>

        <?php if($errors->any()): ?>
        <div class="alert alert-error">
            <ul>
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('admin.trainees.store')); ?>" class="trainee-form">
            <?php echo csrf_field(); ?>
            <div class="form-row">
                <div class="form-group">
                    <label for="surname">Surname *</label>
                    <input type="text" id="surname" name="surname" value="<?php echo e(old('surname')); ?>" required>
                </div>
                <div class="form-group">
                    <label for="first_name">First Name *</label>
                    <input type="text" id="first_name" name="first_name" value="<?php echo e(old('first_name')); ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="middle_name">Middle Name</label>
                    <input type="text" id="middle_name" name="middle_name" value="<?php echo e(old('middle_name')); ?>">
                </div>
                <div class="form-group">
                    <label for="gender">Gender *</label>
                    <select id="gender" name="gender" required>
                        <option value="">Select Gender</option>
                        <option value="M" <?php echo e(old('gender') === 'M' ? 'selected' : ''); ?>>Male</option>
                        <option value="F" <?php echo e(old('gender') === 'F' ? 'selected' : ''); ?>>Female</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="phone_number">Phone Number *</label>
                    <input type="text" id="phone_number" name="phone_number" value="<?php echo e(old('phone_number')); ?>" required>
                </div>
                <div class="form-group">
                    <label for="username">Username (Auto-generated if empty)</label>
                    <input type="text" id="username" name="username" value="<?php echo e(old('username')); ?>" placeholder="BCD/XXXXXX">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password">Password (Auto-generated if empty)</label>
                    <input type="text" id="password" name="password" value="<?php echo e(old('password')); ?>" placeholder="Leave empty for auto-generation">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Add Trainee</button>
                <a href="<?php echo e(route('admin.trainees.index')); ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\LENOVO LEGION\Documents\leveler\resources\views/admin/trainees/create.blade.php ENDPATH**/ ?>
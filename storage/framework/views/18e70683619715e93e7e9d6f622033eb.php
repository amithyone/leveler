

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <h1 class="page-title">
                <a href="<?php echo e(route('admin.trainees.show', $trainee->id)); ?>" style="color: #667eea; text-decoration: none; margin-right: 10px;">
                    <i class="fas fa-arrow-left"></i>
                </a>
                Edit Trainee Profile
            </h1>
            <p style="margin: 0; color: #666;"><?php echo e($trainee->full_name); ?></p>
        </div>
    </div>
</div>

<div class="page-content">
    <?php if(session('success')): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <?php echo e(session('success')); ?>

    </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        <ul style="margin: 10px 0 0 20px;">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
    <?php endif; ?>

    <div class="content-section">
        <form action="<?php echo e(route('admin.trainees.update', $trainee->id)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="form-section">
                <h3 class="form-section-title">
                    <i class="fas fa-user"></i> Personal Information
                </h3>

                <div class="form-row">
                    <div class="form-group">
                        <label for="surname">Surname <span class="required">*</span></label>
                        <input type="text" name="surname" id="surname" class="form-control" 
                               value="<?php echo e(old('surname', $trainee->surname)); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="first_name">First Name <span class="required">*</span></label>
                        <input type="text" name="first_name" id="first_name" class="form-control" 
                               value="<?php echo e(old('first_name', $trainee->first_name)); ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="middle_name">Middle Name</label>
                        <input type="text" name="middle_name" id="middle_name" class="form-control" 
                               value="<?php echo e(old('middle_name', $trainee->middle_name)); ?>">
                    </div>

                    <div class="form-group">
                        <label for="gender">Gender <span class="required">*</span></label>
                        <select name="gender" id="gender" class="form-control" required>
                            <option value="M" <?php echo e(old('gender', $trainee->gender) === 'M' ? 'selected' : ''); ?>>Male</option>
                            <option value="F" <?php echo e(old('gender', $trainee->gender) === 'F' ? 'selected' : ''); ?>>Female</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="phone_number">Phone Number <span class="required">*</span></label>
                    <input type="text" name="phone_number" id="phone_number" class="form-control" 
                           value="<?php echo e(old('phone_number', $trainee->phone_number)); ?>" required>
                </div>
            </div>

            <div class="form-section">
                <h3 class="form-section-title">
                    <i class="fas fa-key"></i> Account Information
                </h3>

                <div class="form-row">
                    <div class="form-group">
                        <label for="username">Username <span class="required">*</span></label>
                        <input type="text" name="username" id="username" class="form-control" 
                               value="<?php echo e(old('username', $trainee->username)); ?>" required>
                        <small style="color: #666; margin-top: 5px; display: block;">Format: BCD/XXXXXX</small>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="text" name="password" id="password" class="form-control" 
                               value="<?php echo e(old('password')); ?>" minlength="6">
                        <small style="color: #666; margin-top: 5px; display: block;">Leave blank to keep current password</small>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3 class="form-section-title">
                    <i class="fas fa-toggle-on"></i> Status
                </h3>

                <div class="form-group">
                    <label for="status">Account Status <span class="required">*</span></label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="Active" <?php echo e(old('status', $trainee->status) === 'Active' ? 'selected' : ''); ?>>Active</option>
                        <option value="Inactive" <?php echo e(old('status', $trainee->status) === 'Inactive' ? 'selected' : ''); ?>>Inactive</option>
                    </select>
                    <small style="color: #666; margin-top: 5px; display: block;">
                        Note: Trainees need completed payments to be activated
                    </small>
                </div>
            </div>

            <div class="form-actions">
                <a href="<?php echo e(route('admin.trainees.show', $trainee->id)); ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Profile
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.content-section {
    background: white;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.form-section {
    margin-bottom: 30px;
    padding-bottom: 25px;
    border-bottom: 2px solid #f0f0f0;
}

.form-section:last-of-type {
    border-bottom: none;
}

.form-section-title {
    font-size: 18px;
    font-weight: 700;
    color: #333;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
    font-size: 14px;
}

.required {
    color: #ef4444;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 15px;
    transition: all 0.2s;
    font-family: inherit;
}

.form-control:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-control small {
    display: block;
    margin-top: 5px;
    color: #666;
    font-size: 12px;
}

.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
    padding-top: 25px;
    border-top: 2px solid #f0f0f0;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    transition: all 0.2s;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.btn-secondary {
    background: #e5e7eb;
    color: #374151;
}

.btn-secondary:hover {
    background: #d1d5db;
}

.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.alert-success {
    background: #d1fae5;
    color: #065f46;
    border: 2px solid #10b981;
}

.alert-error {
    background: #fee2e2;
    color: #991b1b;
    border: 2px solid #ef4444;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\LENOVO LEGION\Documents\leveler\resources\views/admin/trainees/edit.blade.php ENDPATH**/ ?>
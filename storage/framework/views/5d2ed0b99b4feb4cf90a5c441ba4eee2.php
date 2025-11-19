

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <h1 class="page-title">
                <a href="<?php echo e(route('admin.courses.view')); ?>" style="color: #667eea; text-decoration: none; margin-right: 10px;">
                    <i class="fas fa-arrow-left"></i>
                </a>
                Edit Course
            </h1>
            <p style="margin: 0; color: #666;">Update course information</p>
        </div>
        <a href="<?php echo e(route('admin.courses.show', $course->id)); ?>" class="btn btn-secondary">
            <i class="fas fa-eye"></i> View Details
        </a>
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
        <ul style="margin: 0; padding-left: 20px;">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
    <?php endif; ?>

    <div class="content-section">
        <form method="POST" action="<?php echo e(route('admin.courses.update', $course->id)); ?>" class="course-form">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="form-group">
                <label for="code">
                    <i class="fas fa-code"></i> Course Code <span class="required">*</span>
                </label>
                <input 
                    type="text" 
                    id="code" 
                    name="code" 
                    class="form-control <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                    value="<?php echo e(old('code', $course->code)); ?>"
                    required
                    maxlength="10"
                    placeholder="e.g., BCD, CAO, CSR"
                >
                <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span class="error-message"><?php echo e($message); ?></span>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <small class="form-help">Unique course identifier (max 10 characters)</small>
            </div>

            <div class="form-group">
                <label for="title">
                    <i class="fas fa-heading"></i> Course Title <span class="required">*</span>
                </label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    class="form-control <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                    value="<?php echo e(old('title', $course->title)); ?>"
                    required
                    maxlength="255"
                    placeholder="Enter course title"
                >
                <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span class="error-message"><?php echo e($message); ?></span>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="form-group">
                <label for="description">
                    <i class="fas fa-align-left"></i> Description
                </label>
                <textarea 
                    id="description" 
                    name="description" 
                    class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                    rows="5"
                    placeholder="Enter course description"
                ><?php echo e(old('description', $course->description)); ?></textarea>
                <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span class="error-message"><?php echo e($message); ?></span>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <small class="form-help">Brief description of the course content and objectives</small>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="duration_hours">
                        <i class="fas fa-clock"></i> Duration (Hours)
                    </label>
                    <input 
                        type="number" 
                        id="duration_hours" 
                        name="duration_hours" 
                        class="form-control <?php $__errorArgs = ['duration_hours'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        value="<?php echo e(old('duration_hours', $course->duration_hours)); ?>"
                        step="0.1"
                        min="0"
                        placeholder="e.g., 1.5"
                    >
                    <?php $__errorArgs = ['duration_hours'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span class="error-message"><?php echo e($message); ?></span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <small class="form-help">Course duration in hours</small>
                </div>

                <div class="form-group">
                    <label for="status">
                        <i class="fas fa-toggle-on"></i> Status <span class="required">*</span>
                    </label>
                    <select 
                        id="status" 
                        name="status" 
                        class="form-control <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        required
                    >
                        <option value="Active" <?php echo e(old('status', $course->status) === 'Active' ? 'selected' : ''); ?>>Active</option>
                        <option value="Inactive" <?php echo e(old('status', $course->status) === 'Inactive' ? 'selected' : ''); ?>>Inactive</option>
                    </select>
                    <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span class="error-message"><?php echo e($message); ?></span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <small class="form-help">Active courses are available for enrollment</small>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Course
                </button>
                <a href="<?php echo e(route('admin.courses.view')); ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>

    <!-- Course Statistics -->
    <div class="content-section" style="margin-top: 30px;">
        <h2 class="section-title">
            <i class="fas fa-chart-bar"></i> Course Statistics
        </h2>
        
        <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px;">
            <div class="stat-item">
                <div class="stat-label">Questions</div>
                <div class="stat-value"><?php echo e($course->questionPools()->count()); ?></div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Enrolled</div>
                <div class="stat-value"><?php echo e($course->accessibleTrainees()->count()); ?></div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Results</div>
                <div class="stat-value"><?php echo e($course->results()->count()); ?></div>
            </div>
        </div>
    </div>
</div>

<style>
.course-form {
    max-width: 800px;
}

.form-group {
    margin-bottom: 25px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
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

.required {
    color: #ef4444;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 15px;
    transition: all 0.3s;
    font-family: inherit;
}

.form-control:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-control.error {
    border-color: #ef4444;
}

textarea.form-control {
    resize: vertical;
    min-height: 100px;
}

.error-message {
    color: #ef4444;
    font-size: 13px;
    margin-top: 5px;
    display: block;
}

.form-help {
    color: #666;
    font-size: 12px;
    margin-top: 5px;
    display: block;
}

.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 2px solid #f0f0f0;
}

.stat-item {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    text-align: center;
}

.stat-item .stat-label {
    font-size: 12px;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 5px;
}

.stat-item .stat-value {
    font-size: 24px;
    font-weight: 700;
    color: #667eea;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\LENOVO LEGION\Documents\leveler\resources\views/admin/courses/edit.blade.php ENDPATH**/ ?>
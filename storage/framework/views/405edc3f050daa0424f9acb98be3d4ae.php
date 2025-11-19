<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trainee Registration - Leveler</title>
    <link rel="stylesheet" href="<?php echo e(asset('css/trainee-auth.css')); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="logo-large">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h1>Leveler</h1>
                <p>Create Your Account</p>
            </div>

            <?php if($errors->any()): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <ul>
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
            <?php endif; ?>

            <?php if(session('success')): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo e(session('success')); ?>

            </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('trainee.register')); ?>" class="auth-form">
                <?php echo csrf_field(); ?>

                <div class="form-row">
                    <div class="form-group">
                        <label for="surname">
                            <i class="fas fa-user"></i> Surname <span class="required">*</span>
                        </label>
                        <input type="text" id="surname" name="surname" value="<?php echo e(old('surname')); ?>" required autofocus placeholder="Enter your surname">
                    </div>

                    <div class="form-group">
                        <label for="first_name">
                            <i class="fas fa-user"></i> First Name <span class="required">*</span>
                        </label>
                        <input type="text" id="first_name" name="first_name" value="<?php echo e(old('first_name')); ?>" required placeholder="Enter your first name">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="middle_name">
                            <i class="fas fa-user"></i> Middle Name
                        </label>
                        <input type="text" id="middle_name" name="middle_name" value="<?php echo e(old('middle_name')); ?>" placeholder="Enter your middle name (optional)">
                    </div>

                    <div class="form-group">
                        <label for="gender">
                            <i class="fas fa-venus-mars"></i> Gender <span class="required">*</span>
                        </label>
                        <select id="gender" name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="M" <?php echo e(old('gender') === 'M' ? 'selected' : ''); ?>>Male</option>
                            <option value="F" <?php echo e(old('gender') === 'F' ? 'selected' : ''); ?>>Female</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="phone_number">
                        <i class="fas fa-phone"></i> Phone Number <span class="required">*</span>
                    </label>
                    <input type="tel" id="phone_number" name="phone_number" value="<?php echo e(old('phone_number')); ?>" required placeholder="e.g., 2348061234567">
                    <small class="form-text">Include country code (e.g., 234 for Nigeria)</small>
                </div>

                <div class="form-group">
                    <label for="username">
                        <i class="fas fa-at"></i> Username (Optional)
                    </label>
                    <input type="text" id="username" name="username" value="<?php echo e(old('username')); ?>" placeholder="Leave empty for auto-generation">
                    <small class="form-text">If left empty, a username will be automatically generated for you</small>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">
                            <i class="fas fa-lock"></i> Password <span class="required">*</span>
                        </label>
                        <input type="password" id="password" name="password" required placeholder="Enter your password (min 6 characters)">
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">
                            <i class="fas fa-lock"></i> Confirm Password <span class="required">*</span>
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Confirm your password">
                    </div>
                </div>

                <div class="form-info">
                    <p><i class="fas fa-info-circle"></i> After registration, you'll need to make a payment to activate your account and gain access to courses.</p>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>
            </form>

            <div class="auth-footer">
                <p>Already have an account? <a href="<?php echo e(route('trainee.login')); ?>">Sign In</a></p>
                <p><a href="<?php echo e(route('home')); ?>"><i class="fas fa-arrow-left"></i> Back to Home</a></p>
            </div>
        </div>
    </div>
</body>
</html>

<?php /**PATH C:\Users\LENOVO LEGION\Documents\leveler\resources\views/trainee/auth/register.blade.php ENDPATH**/ ?>
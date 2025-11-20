<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trainee Login - Leveler</title>
    <link rel="stylesheet" href="<?php echo e(asset('css/trainee-auth.css')); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="logo-large">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h1>Leveler</h1>
                <p>Trainee Portal</p>
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

            <?php if(session('status')): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo e(session('status')); ?>

            </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('trainee.login')); ?>" class="auth-form">
                <?php echo csrf_field(); ?>

                <div class="form-group">
                    <label for="username">
                        <i class="fas fa-user"></i> Username
                    </label>
                    <input type="text" id="username" name="username" value="<?php echo e(old('username')); ?>" required autofocus placeholder="Enter your username">
                </div>

                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <input type="password" id="password" name="password" required placeholder="Enter your password">
                </div>

                <div class="form-group checkbox-group">
                    <label>
                        <input type="checkbox" name="remember">
                        <span>Remember me</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </button>
            </form>

            <div class="auth-footer">
                <p>Don't have an account? <a href="<?php echo e(route('trainee.register')); ?>">Register Here</a></p>
                <p><a href="<?php echo e(route('home')); ?>"><i class="fas fa-arrow-left"></i> Back to Home</a></p>
            </div>
        </div>
    </div>
</body>
</html>

<?php /**PATH /var/www/leveler/resources/views/trainee/auth/login.blade.php ENDPATH**/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Trainee Portal'); ?> - Leveler</title>
    <link rel="stylesheet" href="<?php echo e(asset('css/trainee.css')); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="trainee-container">
        <!-- Admin Impersonation Banner -->
        <?php if(session('impersonating')): ?>
        <div class="impersonation-banner">
            <div class="banner-content">
                <i class="fas fa-user-shield"></i>
                <span>You are viewing as <strong><?php echo e(Auth::guard('trainee')->user()->full_name); ?></strong></span>
                <a href="<?php echo e(route('admin.trainees.stop-impersonating')); ?>" class="btn-exit-impersonation">
                    <i class="fas fa-times"></i> Exit View
                </a>
            </div>
        </div>
        <?php endif; ?>

        <!-- Header -->
        <header class="trainee-header">
            <div class="header-content">
                <div class="header-left">
                    <a href="<?php echo e(route('trainee.dashboard')); ?>" class="logo">
                        <i class="fas fa-graduation-cap"></i>
                        <span>Leveler</span>
                    </a>
                </div>
                <div class="header-right">
                    <div class="user-menu">
                        <div class="user-info">
                            <i class="fas fa-user-circle"></i>
                            <span><?php echo e(Auth::guard('trainee')->user()->full_name); ?></span>
                        </div>
                        <div class="user-dropdown">
                            <a href="<?php echo e(route('trainee.dashboard')); ?>">
                                <i class="fas fa-home"></i> Dashboard
                            </a>
                            <a href="<?php echo e(route('trainee.payments.index')); ?>">
                                <i class="fas fa-money-bill-wave"></i> My Payments
                            </a>
                            <a href="<?php echo e(route('trainee.certificates.index')); ?>">
                                <i class="fas fa-certificate"></i> My Certificates
                            </a>
                            <form action="<?php echo e(route('trainee.logout')); ?>" method="POST" style="display: inline;">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="logout-btn">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Navigation -->
        <nav class="trainee-nav">
            <a href="<?php echo e(route('trainee.dashboard')); ?>" class="nav-item <?php echo e(request()->routeIs('trainee.dashboard') ? 'active' : ''); ?>">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="<?php echo e(route('trainee.courses.index')); ?>" class="nav-item <?php echo e(request()->routeIs('trainee.courses.*') ? 'active' : ''); ?>">
                <i class="fas fa-book"></i>
                <span>Courses</span>
            </a>
            <a href="<?php echo e(route('trainee.payments.index')); ?>" class="nav-item <?php echo e(request()->routeIs('trainee.payments.*') ? 'active' : ''); ?>">
                <i class="fas fa-money-bill-wave"></i>
                <span>Payments</span>
            </a>
            <a href="<?php echo e(route('trainee.certificates.index')); ?>" class="nav-item <?php echo e(request()->routeIs('trainee.certificates.*') ? 'active' : ''); ?>">
                <i class="fas fa-certificate"></i>
                <span>Certificates</span>
            </a>
        </nav>

        <!-- Main Content -->
        <main class="trainee-main">
            <?php if(session('success')): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo e(session('success')); ?>

            </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo e(session('error')); ?>

            </div>
            <?php endif; ?>

            <?php if(session('info')): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <?php echo e(session('info')); ?>

            </div>
            <?php endif; ?>

            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>

    <script src="<?php echo e(asset('js/trainee.js')); ?>"></script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>

<?php /**PATH /var/www/leveler/resources/views/layouts/trainee.blade.php ENDPATH**/ ?>
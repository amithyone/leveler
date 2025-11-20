<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Leveler'); ?></title>
    <link rel="stylesheet" href="<?php echo e(asset('css/frontend.css')); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <a href="<?php echo e(route('home')); ?>">Leveler</a>
            </div>
            <button class="mobile-menu-toggle" id="mobileMenuToggle">
                <i class="fas fa-bars"></i>
            </button>
            <ul class="nav-menu" id="navMenu">
                <li><a href="<?php echo e(route('about')); ?>">About DHC</a></li>
                <li><a href="<?php echo e(route('services')); ?>">Our Services</a></li>
                <li><a href="<?php echo e(route('partners')); ?>">Partners</a></li>
                <li><a href="<?php echo e(route('tips-updates')); ?>">Tips & Updates</a></li>
                <li><a href="<?php echo e(route('contact')); ?>">Contact</a></li>
                <?php if(auth()->guard('web')->check()): ?>
                    
                    <li><a href="<?php echo e(route('trainee.dashboard')); ?>" class="nav-portal-btn"><i class="fas fa-user-graduate"></i> Portal</a></li>
                    <li>
                        <form action="<?php echo e(route('logout')); ?>" method="POST" style="display: inline;">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="nav-logout-btn">Logout</button>
                        </form>
                    </li>
                <?php elseif(auth()->guard('trainee')->check()): ?>
                    
                    <li><a href="<?php echo e(route('trainee.dashboard')); ?>" class="nav-portal-btn"><i class="fas fa-user-graduate"></i> Portal</a></li>
                    <li>
                        <form action="<?php echo e(route('trainee.logout')); ?>" method="POST" style="display: inline;">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="nav-logout-btn">Logout</button>
                        </form>
                    </li>
                <?php else: ?>
                    
                    <li><a href="<?php echo e(route('trainee.login')); ?>" class="nav-login-btn">Login</a></li>
                    <li><a href="<?php echo e(route('trainee.register')); ?>" class="nav-register-btn">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <main>
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Leveler</h3>
                    <p>Leveler is a development and management consulting company.</p>
                </div>
                <div class="footer-section">
                    <h4>Site Navigation</h4>
                    <ul>
                        <li><a href="<?php echo e(route('faqs')); ?>">FAQs</a></li>
                        <li><a href="<?php echo e(route('careers')); ?>">Careers</a></li>
                        <li><a href="<?php echo e(route('courses')); ?>">Courses</a></li>
                        <li><a href="<?php echo e(route('e-learning')); ?>">e-Learning</a></li>
                        <li><a href="<?php echo e(route('trainee.login')); ?>">Trainee Login</a></li>
                        <li><a href="<?php echo e(route('trainee.register')); ?>">Register For Course</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Services</h4>
                    <ul>
                        <li><a href="<?php echo e(route('faqs')); ?>">FAQ'S</a></li>
                        <li><a href="<?php echo e(route('news')); ?>">News</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contact Details</h4>
                    <p><i class="fas fa-map-marker-alt"></i> Plot 559c, Capital Street (NCWS House), Garki, Abuja, Nigeria.</p>
                    <p><i class="fas fa-phone"></i> 234 (806) 141-3675</p>
                    <p><i class="fas fa-clock"></i> Mon - Fri: 9.00 to 17.00</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>Copyright © 2024 Leveler</p>
                <div class="footer-links">
                    <a href="<?php echo e(route('terms')); ?>">Terms of Use</a>
                    <a href="<?php echo e(route('privacy')); ?>">Privacy Policy</a>
                    <a href="<?php echo e(route('legal')); ?>">Legal</a>
                </div>
            </div>
            <div class="footer-credit" style="text-align: center; padding-top: 20px; margin-top: 20px; border-top: 1px solid #4a5568;">
                <p style="color: #cbd5e0; font-size: 14px; margin: 0;">
                    Designed and managed by <a href="#" style="color: #667eea; text-decoration: none; font-weight: 600;">Amithy One Media</a>
                </p>
            </div>
        </div>
    </footer>

    <script src="<?php echo e(asset('js/frontend.js')); ?>"></script>
</body>
</html>

<?php /**PATH C:\Users\LENOVO LEGION\Documents\leveler\resources\views/layouts/frontend.blade.php ENDPATH**/ ?>
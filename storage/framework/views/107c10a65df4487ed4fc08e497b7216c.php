

<?php $__env->startSection('title', 'Partners - Leveler'); ?>

<?php $__env->startSection('content'); ?>
<section class="page-header">
    <div class="container">
        <h1>Partners</h1>
    </div>
</section>

<section class="page-content">
    <div class="container">
        <?php if($page && $page->content): ?>
            <div class="page-body">
                <?php echo nl2br(e($page->content)); ?>

            </div>
        <?php else: ?>
            <div class="page-body">
                <h2>Our Partners</h2>
                <p>We are proud to collaborate with leading organizations and institutions to deliver exceptional training and development services.</p>
                <p>Our partners information will be displayed here.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
.page-body {
    max-width: 900px;
    margin: 0 auto;
    line-height: 1.8;
    color: #333;
    font-size: 16px;
}
</style>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.frontend', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\LENOVO LEGION\Documents\leveler\resources\views/frontend/partners.blade.php ENDPATH**/ ?>
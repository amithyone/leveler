<?php $__env->startSection('title', 'Tips & Updates - Leveler'); ?>

<?php $__env->startSection('content'); ?>
<section class="page-header">
    <div class="container">
        <h1>Tips & Updates</h1>
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
                <h2>Tips & Updates</h2>
                <p>Stay updated with the latest tips, industry insights, and professional development advice from our experts.</p>
                <p>Latest tips and updates will be displayed here.</p>
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


<?php echo $__env->make('layouts.frontend', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/leveler/resources/views/frontend/tips-updates.blade.php ENDPATH**/ ?>
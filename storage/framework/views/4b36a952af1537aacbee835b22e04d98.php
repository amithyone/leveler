

<?php $__env->startSection('title', 'About Us - Leveler'); ?>

<?php $__env->startSection('content'); ?>
<section class="page-header">
    <div class="container">
        <h1>About Leveler</h1>
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
                <h2>Leveler</h2>
                <p>…Leveler means Competence</p>
                <p>For over 10 years, we have supported businesses to accelerate growth.</p>
                <p>Leveler is a Business & Management consulting company whose mandate is to aid growth and sustainability of businesses through strategy development.</p>
                <p>Our reputation is built on the foundation of providing business and management solutions that deliver growth, increase profit and boost efficiency.</p>
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


<?php echo $__env->make('layouts.frontend', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\LENOVO LEGION\Documents\leveler\resources\views/frontend/about.blade.php ENDPATH**/ ?>
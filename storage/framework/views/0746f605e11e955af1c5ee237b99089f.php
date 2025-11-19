

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-question-circle"></i> Question Pool</h1>
    <p style="margin: 0; color: #666;">Select a course to view and manage questions</p>
</div>

<div class="page-content">
    <?php if($courses->count() > 0): ?>
    <div class="courses-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
        <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="course-card" onclick="window.location.href='<?php echo e(route('admin.question-pool.course', $course->id)); ?>'">
            <div class="course-card-header">
                <span class="course-code-badge"><?php echo e($course->code); ?></span>
                <span class="status-badge <?php echo e($course->status === 'Active' ? 'status-active' : 'status-inactive'); ?>">
                    <?php echo e($course->status); ?>

                </span>
            </div>
            <h3 class="course-title"><?php echo e($course->title); ?></h3>
            <div class="course-stats">
                <div class="stat-item">
                    <i class="fas fa-question-circle"></i>
                    <span><?php echo e($course->question_pools_count ?? 0); ?> Questions</span>
                </div>
            </div>
            <div class="course-action">
                <a href="<?php echo e(route('admin.question-pool.course', $course->id)); ?>" class="btn btn-primary btn-block">
                    <i class="fas fa-eye"></i> View Questions
                </a>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <i class="fas fa-book-open" style="font-size: 64px; color: #ccc; margin-bottom: 20px;"></i>
        <h3>No Courses Found</h3>
        <p>No courses available. Please add courses first.</p>
    </div>
    <?php endif; ?>
</div>

<style>
.course-card {
    background: white;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    padding: 20px;
    cursor: pointer;
    transition: all 0.3s;
}

.course-card:hover {
    border-color: #667eea;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
    transform: translateY(-2px);
}

.course-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.course-title {
    font-size: 18px;
    font-weight: 700;
    color: #333;
    margin-bottom: 15px;
    line-height: 1.4;
}

.course-stats {
    margin-bottom: 15px;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #666;
    font-size: 14px;
}

.course-action {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #e0e0e0;
}

.btn-block {
    width: 100%;
    text-align: center;
}
</style>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\LENOVO LEGION\Documents\leveler\resources\views/admin/question-pool/index.blade.php ENDPATH**/ ?>
<?php $__env->startSection('title', 'My Courses'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1><i class="fas fa-book"></i> Available Courses</h1>
    <p>Browse and enroll in courses to start your learning journey</p>
</div>

<?php if($courses->count() > 0): ?>
<div class="courses-grid">
    <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="course-card">
        <div class="course-badge">
            <?php if(!$course->has_access): ?>
                <span class="badge badge-danger"><i class="fas fa-lock"></i> Payment Required</span>
            <?php elseif($course->has_passed): ?>
                <span class="badge badge-success"><i class="fas fa-check-circle"></i> Passed</span>
            <?php elseif($course->has_taken): ?>
                <span class="badge badge-warning"><i class="fas fa-redo"></i> Retake Available</span>
            <?php else: ?>
                <span class="badge badge-info"><i class="fas fa-play-circle"></i> Available</span>
            <?php endif; ?>
        </div>
        
        <div class="course-header">
            <h3><?php echo e($course->title); ?></h3>
            <span class="course-code"><?php echo e($course->code); ?></span>
        </div>
        
        <p class="course-description"><?php echo e($course->description); ?></p>
        
        <div class="course-meta">
            <div class="meta-item">
                <i class="fas fa-clock"></i>
                <span><?php echo e($course->duration_hours); ?> Hours</span>
            </div>
            <div class="meta-item">
                <i class="fas fa-question-circle"></i>
                <span><?php echo e($course->questionPools->count()); ?> Questions</span>
            </div>
        </div>

        <?php if($course->latest_result): ?>
        <div class="course-result">
            <div class="result-info">
                <span>Last Score: <strong><?php echo e(number_format($course->latest_result->percentage, 1)); ?>%</strong></span>
                <span class="result-date"><?php echo e($course->latest_result->completed_at->format('M d, Y')); ?></span>
            </div>
        </div>
        <?php endif; ?>

        <div class="course-actions">
            <?php if(!$course->has_access): ?>
                <a href="<?php echo e(route('trainee.payments.create')); ?>" class="btn btn-primary btn-block">
                    <i class="fas fa-lock"></i> Get Access
                </a>
            <?php else: ?>
                <a href="<?php echo e(route('trainee.courses.show', $course->id)); ?>" class="btn btn-primary btn-block">
                    <?php if($course->has_passed): ?>
                        <i class="fas fa-eye"></i> View Result
                    <?php elseif($course->has_taken): ?>
                        <i class="fas fa-redo"></i> Retake Assessment
                    <?php else: ?>
                        <i class="fas fa-play"></i> Start Course
                    <?php endif; ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php else: ?>
<div class="empty-state">
    <i class="fas fa-book-open"></i>
    <h3>No Courses Available</h3>
    <p>There are no courses available at the moment. Please check back later.</p>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.trainee', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/leveler/resources/views/trainee/courses/index.blade.php ENDPATH**/ ?>


<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="dashboard-header">
    <h1>Welcome, <?php echo e(Auth::guard('trainee')->user()->full_name); ?></h1>
    <p>Track your learning progress and achievements</p>
</div>

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <i class="fas fa-book"></i>
        </div>
        <div class="stat-info">
            <div class="stat-number"><?php echo e($stats['enrolled_courses']); ?></div>
            <div class="stat-label">Available Courses</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-info">
            <div class="stat-number"><?php echo e($stats['completed_courses']); ?></div>
            <div class="stat-label">Completed Courses</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <i class="fas fa-clipboard-check"></i>
        </div>
        <div class="stat-info">
            <div class="stat-number"><?php echo e($stats['total_assessments']); ?></div>
            <div class="stat-label">Assessments Taken</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
            <i class="fas fa-certificate"></i>
        </div>
        <div class="stat-info">
            <div class="stat-number"><?php echo e($stats['certificates']->count()); ?></div>
            <div class="stat-label">Certificates Earned</div>
        </div>
    </div>
</div>

<!-- Available Courses -->
<div class="section">
    <div class="section-header">
        <h2><i class="fas fa-book-open"></i> Available Courses</h2>
        <a href="<?php echo e(route('trainee.courses.index')); ?>" class="btn-link">View All <i class="fas fa-arrow-right"></i></a>
    </div>
    
    <?php if($availableCourses->count() > 0): ?>
    <div class="courses-grid">
        <?php $__currentLoopData = $availableCourses->take(6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="course-card">
            <div class="course-header">
                <h3><?php echo e($course->title); ?></h3>
                <span class="course-code"><?php echo e($course->code); ?></span>
            </div>
            <p class="course-description"><?php echo e(\Illuminate\Support\Str::limit($course->description, 100)); ?></p>
            <div class="course-meta">
                <span><i class="fas fa-clock"></i> <?php echo e($course->duration_hours); ?> Hours</span>
                <span><i class="fas fa-question-circle"></i> <?php echo e($course->questionPools->count()); ?> Questions</span>
            </div>
            <div class="course-status">
                <?php if($course->has_passed): ?>
                    <span class="badge badge-success"><i class="fas fa-check"></i> Passed</span>
                <?php elseif($course->has_taken): ?>
                    <span class="badge badge-warning"><i class="fas fa-redo"></i> Retake Available</span>
                <?php else: ?>
                    <span class="badge badge-info"><i class="fas fa-play"></i> Available</span>
                <?php endif; ?>
            </div>
            <div class="course-actions">
                <a href="<?php echo e(route('trainee.courses.show', $course->id)); ?>" class="btn btn-primary btn-sm">
                    <?php if($course->has_passed): ?>
                        <i class="fas fa-eye"></i> View Result
                    <?php elseif($course->has_taken): ?>
                        <i class="fas fa-redo"></i> Retake
                    <?php else: ?>
                        <i class="fas fa-play"></i> Start Course
                    <?php endif; ?>
                </a>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <i class="fas fa-book-open"></i>
        <p>No courses available at the moment.</p>
    </div>
    <?php endif; ?>
</div>

<!-- Recent Results -->
<?php if($recentResults->count() > 0): ?>
<div class="section">
    <div class="section-header">
        <h2><i class="fas fa-chart-line"></i> Recent Assessment Results</h2>
    </div>
    <div class="results-list">
        <?php $__currentLoopData = $recentResults; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="result-item">
            <div class="result-course">
                <h4><?php echo e($result->course->title); ?></h4>
                <span class="result-date"><?php echo e($result->completed_at->format('M d, Y')); ?></span>
            </div>
            <div class="result-score">
                <div class="score-circle <?php echo e($result->status === 'passed' ? 'passed' : 'failed'); ?>">
                    <span><?php echo e(number_format($result->percentage, 0)); ?>%</span>
                </div>
            </div>
            <div class="result-status">
                <?php if($result->status === 'passed'): ?>
                    <span class="badge badge-success"><i class="fas fa-check"></i> Passed</span>
                <?php else: ?>
                    <span class="badge badge-danger"><i class="fas fa-times"></i> Failed</span>
                <?php endif; ?>
            </div>
            <div class="result-actions">
                <a href="<?php echo e(route('trainee.assessment.result', $result->id)); ?>" class="btn btn-sm btn-outline">View Details</a>
                <?php if($result->status === 'passed'): ?>
                    <a href="<?php echo e(route('trainee.certificates.view', $result->id)); ?>" class="btn btn-sm btn-primary">
                        <i class="fas fa-certificate"></i> Certificate
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.trainee', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\LENOVO LEGION\Documents\leveler\resources\views/trainee/dashboard.blade.php ENDPATH**/ ?>
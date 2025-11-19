

<?php $__env->startSection('title', 'News - Leveler'); ?>

<?php $__env->startSection('content'); ?>
<section class="page-header">
    <div class="container">
        <h1><?php echo e($page->title ?? 'News & Updates'); ?></h1>
    </div>
</section>

<section class="page-content">
    <div class="container">
        <?php if($page && $page->content): ?>
            <div class="page-body">
                <?php echo nl2br(e($page->content)); ?>

            </div>
        <?php else: ?>
            <div class="news-content">
                <h2>Latest News & Updates</h2>
                <p>Stay informed about the latest developments, course announcements, and industry insights from Leveler.</p>

                <div class="news-list">
                    <div class="news-item">
                        <div class="news-date">
                            <span class="day"><?php echo e(date('d')); ?></span>
                            <span class="month"><?php echo e(date('M')); ?></span>
                        </div>
                        <div class="news-content-item">
                            <h3>New Course Offerings Available</h3>
                            <p>We're excited to announce new professional development courses designed to enhance your skills and advance your career. Check out our <a href="<?php echo e(route('courses')); ?>">Courses</a> page for the full list.</p>
                        </div>
                    </div>

                    <div class="news-item">
                        <div class="news-date">
                            <span class="day"><?php echo e(date('d', strtotime('-5 days'))); ?></span>
                            <span class="month"><?php echo e(date('M', strtotime('-5 days'))); ?></span>
                        </div>
                        <div class="news-content-item">
                            <h3>Installment Payment Option Now Available</h3>
                            <p>We've introduced flexible installment payment options to make our courses more accessible. You can now pay in installments while still gaining course access.</p>
                        </div>
                    </div>

                    <div class="news-item">
                        <div class="news-date">
                            <span class="day"><?php echo e(date('d', strtotime('-10 days'))); ?></span>
                            <span class="month"><?php echo e(date('M', strtotime('-10 days'))); ?></span>
                        </div>
                        <div class="news-content-item">
                            <h3>Enhanced e-Learning Platform</h3>
                            <p>Our e-learning platform has been upgraded with new features including improved course navigation, better progress tracking, and enhanced assessment tools.</p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
.news-content {
    max-width: 900px;
    margin: 0 auto;
}

.news-content h2 {
    color: #333;
    margin-bottom: 15px;
    font-size: 28px;
}

.news-content > p {
    font-size: 16px;
    color: #666;
    margin-bottom: 40px;
    line-height: 1.8;
}

.news-list {
    display: flex;
    flex-direction: column;
    gap: 25px;
}

.news-item {
    display: flex;
    gap: 20px;
    background: white;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    padding: 25px;
    transition: all 0.3s;
}

.news-item:hover {
    border-color: #667eea;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
}

.news-date {
    min-width: 80px;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 8px;
    padding: 15px;
}

.news-date .day {
    font-size: 28px;
    font-weight: 700;
    line-height: 1;
}

.news-date .month {
    font-size: 14px;
    text-transform: uppercase;
    margin-top: 5px;
}

.news-content-item {
    flex: 1;
}

.news-content-item h3 {
    color: #333;
    margin-bottom: 10px;
    font-size: 20px;
}

.news-content-item p {
    color: #666;
    line-height: 1.8;
}

.news-content-item a {
    color: #667eea;
    text-decoration: none;
}

.news-content-item a:hover {
    text-decoration: underline;
}

.page-body {
    max-width: 900px;
    margin: 0 auto;
    line-height: 1.8;
    color: #333;
}

@media (max-width: 768px) {
    .news-item {
        flex-direction: column;
    }
    
    .news-date {
        width: 100%;
        flex-direction: row;
        justify-content: center;
        gap: 10px;
    }
}
</style>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.frontend', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\LENOVO LEGION\Documents\leveler\resources\views/frontend/news.blade.php ENDPATH**/ ?>
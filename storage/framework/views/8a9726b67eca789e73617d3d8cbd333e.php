

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <h1 class="page-title"><i class="fas fa-chart-bar"></i> Reports & Analytics</h1>
            <p style="margin: 0; color: #666;">Comprehensive system reports and statistics</p>
        </div>
        <div>
            <button onclick="window.print()" class="btn btn-secondary">
                <i class="fas fa-print"></i> Print Report
            </button>
        </div>
    </div>
</div>

<div class="page-content">
    <!-- Overall Statistics -->
    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Total Trainees</div>
                <div class="stat-value"><?php echo e($stats['total_trainees']); ?></div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Active Trainees</div>
                <div class="stat-value"><?php echo e($stats['active_trainees']); ?></div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                <i class="fas fa-book"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Total Courses</div>
                <div class="stat-value"><?php echo e($stats['total_courses']); ?></div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                <i class="fas fa-poll"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Total Results</div>
                <div class="stat-value"><?php echo e($stats['total_results']); ?></div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <i class="fas fa-check-double"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Passed</div>
                <div class="stat-value"><?php echo e($stats['passed_results']); ?></div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Failed</div>
                <div class="stat-value"><?php echo e($stats['failed_results']); ?></div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Total Revenue</div>
                <div class="stat-value">₦<?php echo e(number_format($stats['total_revenue'], 2)); ?></div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);">
                <i class="fas fa-credit-card"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Total Payments</div>
                <div class="stat-value"><?php echo e($stats['total_payments']); ?></div>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
        <!-- Payment Statistics -->
        <div class="content-section">
            <h2 class="section-title">
                <i class="fas fa-money-bill-wave"></i> Payment Statistics
            </h2>
            <div class="payment-stats-grid">
                <div class="payment-stat-item">
                    <div class="payment-stat-label">Today</div>
                    <div class="payment-stat-value">₦<?php echo e(number_format($paymentStats['today'], 2)); ?></div>
                </div>
                <div class="payment-stat-item">
                    <div class="payment-stat-label">This Month</div>
                    <div class="payment-stat-value">₦<?php echo e(number_format($paymentStats['this_month'], 2)); ?></div>
                </div>
                <div class="payment-stat-item">
                    <div class="payment-stat-label">This Year</div>
                    <div class="payment-stat-value">₦<?php echo e(number_format($paymentStats['this_year'], 2)); ?></div>
                </div>
            </div>
        </div>

        <!-- Trainee Status -->
        <div class="content-section">
            <h2 class="section-title">
                <i class="fas fa-users"></i> Trainee Status
            </h2>
            <div class="status-distribution">
                <div class="status-item">
                    <div class="status-label">Active</div>
                    <div class="status-bar">
                        <div class="status-fill" style="width: <?php echo e($stats['total_trainees'] > 0 ? ($traineeStatus['active'] / $stats['total_trainees']) * 100 : 0); ?>%; background: #10b981;">
                            <?php echo e($traineeStatus['active']); ?>

                        </div>
                    </div>
                </div>
                <div class="status-item">
                    <div class="status-label">Inactive</div>
                    <div class="status-bar">
                        <div class="status-fill" style="width: <?php echo e($stats['total_trainees'] > 0 ? ($traineeStatus['inactive'] / $stats['total_trainees']) * 100 : 0); ?>%; background: #f59e0b;">
                            <?php echo e($traineeStatus['inactive']); ?>

                        </div>
                    </div>
                </div>
                <div class="status-item">
                    <div class="status-label">With Payment</div>
                    <div class="status-bar">
                        <div class="status-fill" style="width: <?php echo e($stats['total_trainees'] > 0 ? ($traineeStatus['with_payment'] / $stats['total_trainees']) * 100 : 0); ?>%; background: #3b82f6;">
                            <?php echo e($traineeStatus['with_payment']); ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Course Performance -->
    <div class="content-section">
        <h2 class="section-title">
            <i class="fas fa-chart-line"></i> Course Performance
        </h2>
        <?php if($coursePerformance->count() > 0): ?>
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Total Results</th>
                        <th>Passed</th>
                        <th>Pass Rate</th>
                        <th>Avg Score</th>
                        <th>Questions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $coursePerformance; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <strong><?php echo e($course->code); ?></strong>
                            <br>
                            <small style="color: #666;"><?php echo e($course->title); ?></small>
                        </td>
                        <td><?php echo e($course->total_results); ?></td>
                        <td>
                            <span class="badge badge-success"><?php echo e($course->passed_results); ?></span>
                        </td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="flex: 1; background: #e0e0e0; height: 8px; border-radius: 4px; overflow: hidden;">
                                    <div style="background: #10b981; height: 100%; width: <?php echo e($course->pass_rate); ?>%;"></div>
                                </div>
                                <span style="font-weight: 600; min-width: 50px;"><?php echo e(number_format($course->pass_rate, 1)); ?>%</span>
                            </div>
                        </td>
                        <td>
                            <strong style="color: <?php echo e($course->average_score >= 50 ? '#10b981' : '#ef4444'); ?>;">
                                <?php echo e(number_format($course->average_score, 1)); ?>%
                            </strong>
                        </td>
                        <td><?php echo e($course->total_questions); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="empty-state-small">
            <i class="fas fa-chart-line"></i>
            <p>No course performance data available.</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Recent Activity -->
    <div class="content-section">
        <h2 class="section-title">
            <i class="fas fa-clock"></i> Recent Assessment Results
        </h2>
        <?php if($recentResults->count() > 0): ?>
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Trainee</th>
                        <th>Course</th>
                        <th>Score</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $recentResults; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($result->trainee->full_name); ?></td>
                        <td><?php echo e($result->course->code); ?></td>
                        <td>
                            <strong><?php echo e($result->score); ?>/<?php echo e($result->total_questions); ?></strong>
                            <br>
                            <small style="color: #666;"><?php echo e(number_format($result->percentage, 1)); ?>%</small>
                        </td>
                        <td>
                            <span class="status-badge <?php echo e($result->status === 'passed' ? 'status-active' : 'status-inactive'); ?>">
                                <?php echo e(ucfirst($result->status)); ?>

                            </span>
                        </td>
                        <td><?php echo e($result->completed_at ? $result->completed_at->format('M d, Y') : 'N/A'); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="empty-state-small">
            <i class="fas fa-clock"></i>
            <p>No recent assessment results.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: transform 0.2s;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
}

.stat-info {
    flex: 1;
}

.stat-label {
    font-size: 13px;
    color: #666;
    margin-bottom: 5px;
}

.stat-value {
    font-size: 28px;
    font-weight: 700;
    color: #333;
}

.content-section {
    background: white;
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.section-title {
    font-size: 20px;
    font-weight: 700;
    color: #333;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.payment-stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
}

.payment-stat-item {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
}

.payment-stat-label {
    font-size: 12px;
    color: #666;
    margin-bottom: 10px;
    text-transform: uppercase;
    font-weight: 600;
}

.payment-stat-value {
    font-size: 24px;
    font-weight: 700;
    color: #333;
}

.status-distribution {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.status-item {
    display: flex;
    align-items: center;
    gap: 15px;
}

.status-label {
    min-width: 120px;
    font-weight: 600;
    color: #333;
}

.status-bar {
    flex: 1;
    background: #e0e0e0;
    height: 30px;
    border-radius: 15px;
    overflow: hidden;
    position: relative;
}

.status-fill {
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 12px;
    transition: width 0.3s;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 8px;
    overflow: hidden;
}

.data-table thead {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.data-table th {
    padding: 12px;
    text-align: left;
    font-weight: 600;
    font-size: 12px;
    text-transform: uppercase;
}

.data-table td {
    padding: 12px;
    border-bottom: 1px solid #e0e0e0;
}

.data-table tbody tr:hover {
    background: #f8f9fa;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
}

.status-active {
    background: #d1fae5;
    color: #065f46;
}

.status-inactive {
    background: #fee2e2;
    color: #991b1b;
}

.badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
}

.badge-success {
    background: #d1fae5;
    color: #065f46;
}

.empty-state-small {
    text-align: center;
    padding: 40px 20px;
    color: #999;
}

.empty-state-small i {
    font-size: 48px;
    margin-bottom: 15px;
    display: block;
}

@media (max-width: 768px) {
    .payment-stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\LENOVO LEGION\Documents\leveler\resources\views/admin/reports/index.blade.php ENDPATH**/ ?>
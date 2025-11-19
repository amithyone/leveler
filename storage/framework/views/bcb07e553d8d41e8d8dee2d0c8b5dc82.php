

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <h1 class="page-title"><i class="fas fa-poll"></i> View Results</h1>
            <p style="margin: 0; color: #666;">View and manage all assessment results</p>
        </div>
    </div>
</div>

<div class="page-content">
    <?php if(session('success')): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <?php echo e(session('success')); ?>

    </div>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Total Results</div>
                <div class="stat-value"><?php echo e($stats['total']); ?></div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Passed</div>
                <div class="stat-value"><?php echo e($stats['passed']); ?></div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Failed</div>
                <div class="stat-value"><?php echo e($stats['failed']); ?></div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Average Score</div>
                <div class="stat-value"><?php echo e(number_format($stats['average_score'], 1)); ?>%</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="content-section" style="margin-bottom: 20px;">
        <form method="GET" action="<?php echo e(route('admin.results.index')); ?>" class="filters-form">
            <div class="filters-row">
                <div class="filter-group">
                    <label for="course_id">Course:</label>
                    <select name="course_id" id="course_id" class="form-control">
                        <option value="">All Courses</option>
                        <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($course->id); ?>" <?php echo e(request('course_id') == $course->id ? 'selected' : ''); ?>>
                            <?php echo e($course->code); ?> - <?php echo e($course->title); ?>

                        </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="status">Status:</label>
                    <select name="status" id="status" class="form-control">
                        <option value="all" <?php echo e(request('status') === 'all' || !request('status') ? 'selected' : ''); ?>>All</option>
                        <option value="passed" <?php echo e(request('status') === 'passed' ? 'selected' : ''); ?>>Passed</option>
                        <option value="failed" <?php echo e(request('status') === 'failed' ? 'selected' : ''); ?>>Failed</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="date_from">From Date:</label>
                    <input type="date" name="date_from" id="date_from" class="form-control" value="<?php echo e(request('date_from')); ?>">
                </div>

                <div class="filter-group">
                    <label for="date_to">To Date:</label>
                    <input type="date" name="date_to" id="date_to" class="form-control" value="<?php echo e(request('date_to')); ?>">
                </div>

                <div class="filter-group" style="flex: 1;">
                    <label for="search">Search:</label>
                    <input type="text" name="search" id="search" class="form-control" 
                           placeholder="Search by trainee name or username..." 
                           value="<?php echo e(request('search')); ?>">
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="<?php echo e(route('admin.results.index')); ?>" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Results Table -->
    <div class="content-section">
        <?php if($results->count() > 0): ?>
        <div class="table-header" style="margin-bottom: 15px;">
            <strong>Showing <?php echo e($results->firstItem()); ?> - <?php echo e($results->lastItem()); ?> of <?php echo e($results->total()); ?> results</strong>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Trainee</th>
                        <th>Course</th>
                        <th>Score</th>
                        <th>Percentage</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($results->firstItem() + $index); ?></td>
                        <td>
                            <div style="font-weight: 600; color: #333;"><?php echo e($result->trainee->full_name); ?></div>
                            <small style="color: #666;"><?php echo e($result->trainee->username); ?></small>
                        </td>
                        <td>
                            <div style="font-weight: 600; color: #333;"><?php echo e($result->course->code); ?></div>
                            <small style="color: #666;"><?php echo e($result->course->title); ?></small>
                        </td>
                        <td>
                            <strong><?php echo e($result->score); ?> / <?php echo e($result->total_questions); ?></strong>
                        </td>
                        <td>
                            <div style="font-weight: 600; font-size: 16px; color: <?php echo e($result->percentage >= 50 ? '#10b981' : '#ef4444'); ?>;">
                                <?php echo e(number_format($result->percentage, 1)); ?>%
                            </div>
                        </td>
                        <td>
                            <span class="status-badge <?php echo e($result->status === 'passed' ? 'status-active' : 'status-inactive'); ?>">
                                <?php echo e(ucfirst($result->status)); ?>

                            </span>
                        </td>
                        <td>
                            <?php echo e($result->completed_at ? $result->completed_at->format('M d, Y') : 'N/A'); ?>

                            <br>
                            <small style="color: #666;"><?php echo e($result->completed_at ? $result->completed_at->format('h:i A') : ''); ?></small>
                        </td>
                        <td class="actions-cell">
                            <a href="<?php echo e(route('admin.results.show', $result->id)); ?>" class="action-btn" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if($results->hasPages()): ?>
        <div class="pagination-wrapper" style="margin-top: 20px;">
            <?php echo e($results->links()); ?>

        </div>
        <?php endif; ?>

        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-poll" style="font-size: 64px; color: #ccc; margin-bottom: 20px;"></i>
            <h3>No Results Found</h3>
            <p>No assessment results match your search criteria.</p>
            <a href="<?php echo e(route('admin.results.index')); ?>" class="btn btn-secondary">
                <i class="fas fa-redo"></i> Clear Filters
            </a>
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

.filters-form {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.filters-row {
    display: grid;
    grid-template-columns: 200px 150px 150px 150px 1fr auto;
    gap: 15px;
    align-items: end;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.filter-group label {
    font-weight: 600;
    color: #333;
    font-size: 13px;
}

.filter-actions {
    display: flex;
    gap: 10px;
}

.form-control {
    padding: 10px 15px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.2s;
}

.form-control:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.data-table thead {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.data-table th {
    padding: 15px;
    text-align: left;
    font-weight: 600;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.data-table td {
    padding: 15px;
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

.action-btn {
    width: 32px;
    height: 32px;
    border: none;
    background: #f0f0f0;
    border-radius: 6px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #666;
    transition: all 0.2s;
    text-decoration: none;
}

.action-btn:hover {
    background: #667eea;
    color: white;
    transform: translateY(-2px);
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    transition: all 0.2s;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-secondary {
    background: #e5e7eb;
    color: #374151;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-state h3 {
    color: #333;
    margin-bottom: 10px;
}

.empty-state p {
    color: #666;
    margin-bottom: 25px;
}

@media (max-width: 768px) {
    .filters-row {
        grid-template-columns: 1fr;
    }
    
    .filter-actions {
        width: 100%;
    }
    
    .filter-actions .btn {
        flex: 1;
    }
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\LENOVO LEGION\Documents\leveler\resources\views/admin/results/index.blade.php ENDPATH**/ ?>
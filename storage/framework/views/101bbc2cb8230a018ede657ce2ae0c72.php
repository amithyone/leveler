

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <h1 class="page-title"><i class="fas fa-calendar-alt"></i> Schedules Management</h1>
            <p style="margin: 0; color: #666;">Manage course schedules and training sessions</p>
        </div>
        <div>
            <a href="<?php echo e(route('admin.schedules.create')); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Schedule
            </a>
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

    <!-- Statistics -->
    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <i class="fas fa-calendar"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Total Schedules</div>
                <div class="stat-value"><?php echo e($stats['total']); ?></div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Scheduled</div>
                <div class="stat-value"><?php echo e($stats['scheduled']); ?></div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                <i class="fas fa-play-circle"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Ongoing</div>
                <div class="stat-value"><?php echo e($stats['ongoing']); ?></div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Completed</div>
                <div class="stat-value"><?php echo e($stats['completed']); ?></div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="content-section" style="margin-bottom: 20px;">
        <form method="GET" action="<?php echo e(route('admin.schedules.index')); ?>" class="filters-form">
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
                        <option value="Scheduled" <?php echo e(request('status') === 'Scheduled' ? 'selected' : ''); ?>>Scheduled</option>
                        <option value="Ongoing" <?php echo e(request('status') === 'Ongoing' ? 'selected' : ''); ?>>Ongoing</option>
                        <option value="Completed" <?php echo e(request('status') === 'Completed' ? 'selected' : ''); ?>>Completed</option>
                        <option value="Cancelled" <?php echo e(request('status') === 'Cancelled' ? 'selected' : ''); ?>>Cancelled</option>
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

                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="<?php echo e(route('admin.schedules.index')); ?>" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Schedules Table -->
    <div class="content-section">
        <?php if($schedules->count() > 0): ?>
        <div class="table-header" style="margin-bottom: 15px;">
            <strong>Showing <?php echo e($schedules->firstItem()); ?> - <?php echo e($schedules->lastItem()); ?> of <?php echo e($schedules->total()); ?> schedules</strong>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Course</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Time</th>
                        <th>Venue</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $schedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($schedules->firstItem() + $index); ?></td>
                        <td>
                            <div style="font-weight: 600; color: #333;"><?php echo e($schedule->course->code); ?></div>
                            <small style="color: #666;"><?php echo e($schedule->course->title); ?></small>
                        </td>
                        <td>
                            <div style="font-weight: 600;"><?php echo e($schedule->start_date->format('M d, Y')); ?></div>
                        </td>
                        <td>
                            <div style="font-weight: 600;"><?php echo e($schedule->end_date->format('M d, Y')); ?></div>
                        </td>
                        <td>
                            <div><?php echo e(date('h:i A', strtotime($schedule->start_time))); ?> - <?php echo e(date('h:i A', strtotime($schedule->end_time))); ?></div>
                        </td>
                        <td>
                            <?php echo e($schedule->venue ?? 'N/A'); ?>

                        </td>
                        <td>
                            <?php
                                $statusColors = [
                                    'Scheduled' => '#3b82f6',
                                    'Ongoing' => '#f59e0b',
                                    'Completed' => '#10b981',
                                    'Cancelled' => '#ef4444'
                                ];
                            ?>
                            <span class="status-badge" style="background: <?php echo e($statusColors[$schedule->status] ?? '#666'); ?>20; color: <?php echo e($statusColors[$schedule->status] ?? '#666'); ?>;">
                                <?php echo e($schedule->status); ?>

                            </span>
                        </td>
                        <td class="actions-cell">
                            <div class="action-buttons">
                                <a href="<?php echo e(route('admin.schedules.edit', $schedule->id)); ?>" class="action-btn" title="Edit">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                <form action="<?php echo e(route('admin.schedules.destroy', $schedule->id)); ?>" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this schedule?');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="action-btn" title="Delete" style="background: #fee2e2; color: #991b1b; border: none; cursor: pointer;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if($schedules->hasPages()): ?>
        <div class="pagination-wrapper" style="margin-top: 20px;">
            <?php echo e($schedules->links()); ?>

        </div>
        <?php endif; ?>

        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-calendar-alt" style="font-size: 64px; color: #ccc; margin-bottom: 20px;"></i>
            <h3>No Schedules Found</h3>
            <p>No schedules match your search criteria.</p>
            <a href="<?php echo e(route('admin.schedules.create')); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create First Schedule
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
    grid-template-columns: 250px 150px 150px 150px auto;
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

.action-buttons {
    display: flex;
    gap: 8px;
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\LENOVO LEGION\Documents\leveler\resources\views/admin/schedules/index.blade.php ENDPATH**/ ?>


<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div class="page-title-actions">
        <h1 class="page-title"><i class="fas fa-file-alt"></i> Pages Management</h1>
        <a href="<?php echo e(route('admin.pages.create')); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create New Page
        </a>
    </div>
</div>

<div class="page-content">
    <?php if(session('success')): ?>
    <div class="alert alert-success">
        <?php echo e(session('success')); ?>

    </div>
    <?php endif; ?>

    <div class="content-section">
        <h2 class="section-title">All Pages</h2>
        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Title</th>
                        <th>Slug</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Order</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $pages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($index + 1); ?></td>
                        <td><strong><?php echo e($page->title); ?></strong></td>
                        <td><code><?php echo e($page->slug); ?></code></td>
                        <td>
                            <span class="badge badge-info"><?php echo e(ucfirst($page->page_type)); ?></span>
                        </td>
                        <td>
                            <span class="status-badge <?php echo e($page->is_active ? 'status-active' : 'status-inactive'); ?>">
                                <?php echo e($page->is_active ? 'Active' : 'Inactive'); ?>

                            </span>
                        </td>
                        <td><?php echo e($page->order); ?></td>
                        <td class="actions-cell">
                            <a href="<?php echo e(route('admin.pages.show', $page->id)); ?>" class="action-btn view-btn" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?php echo e(route('admin.pages.edit', $page->id)); ?>" class="action-btn edit-btn" title="Edit">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
                            <form action="<?php echo e(route('admin.pages.destroy', $page->id)); ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this page?');" style="display:inline-block;">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="action-btn delete-btn" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="text-center">No pages found. <a href="<?php echo e(route('admin.pages.create')); ?>">Create one now</a></td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\LENOVO LEGION\Documents\leveler\resources\views/admin/pages/index.blade.php ENDPATH**/ ?>
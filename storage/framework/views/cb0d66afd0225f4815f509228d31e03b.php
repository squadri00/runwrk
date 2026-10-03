<?php $__env->startSection('title', 'Audit log · Runwrk'); ?>

<?php $__env->startSection('content'); ?>
<h1 class="mb-6 text-2xl font-bold">Audit log</h1>
<?php echo $__env->make('admin._logs', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<div class="mt-4"><?php echo e($logs->links()); ?></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\runwrk\resources\views/admin/audit.blade.php ENDPATH**/ ?>
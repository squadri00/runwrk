<?php $__env->startSection('title', 'Admin · Runwrk'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-8 flex items-center justify-between">
    <h1 class="text-2xl font-bold">Overview</h1>
    <a href="<?php echo e(route('admin.businesses.create')); ?>" class="rounded bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">New business</a>
</div>
<div class="mb-10 grid grid-cols-2 gap-4 sm:grid-cols-4">
    <?php $__currentLoopData = \App\Models\Business::STATUSES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('admin.businesses.index', ['status' => $s])); ?>" class="rounded-lg border border-gray-200 bg-white p-4">
            <div class="text-3xl font-bold"><?php echo e($counts[$s] ?? 0); ?></div>
            <div class="text-sm capitalize text-gray-500"><?php echo e($s); ?></div>
        </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<h2 class="mb-3 font-semibold">Recent activity</h2>
<?php echo $__env->make('admin._logs', ['logs' => $recent], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\runwrk\resources\views/admin/home.blade.php ENDPATH**/ ?>
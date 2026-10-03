<?php $__env->startSection('title', $business->name.' · Runwrk'); ?>

<?php $__env->startSection('body'); ?>
<main class="mx-auto max-w-3xl px-4 py-16">
    <h1 class="text-2xl font-bold"><?php echo e($business->name); ?></h1>
    <p class="mt-2 text-gray-500">Your dashboard is coming in the next update.</p>
</main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\runwrk\resources\views/dashboard.blade.php ENDPATH**/ ?>
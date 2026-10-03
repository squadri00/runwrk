<?php $__env->startSection('title', $business->name.' · Runwrk'); ?>

<?php $__env->startSection('content'); ?>
<?php if(session('new_login')): ?>
    <div class="mb-6 rounded border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900">
        Owner login created. Copy it now, the password is not shown again.<br>
        <span class="font-mono"><?php echo e(session('new_login.email')); ?> / <?php echo e(session('new_login.password')); ?></span>
    </div>
<?php endif; ?>
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-2xl font-bold"><?php echo e($business->name); ?></h1>
        <p class="text-sm text-gray-500">runwrk.com/<?php echo e($business->slug); ?> · <span class="capitalize"><?php echo e($business->status); ?></span> · <?php echo e($business->plan?->name ?? 'No plan'); ?></p>
    </div>
    <div class="flex gap-2">
        <form method="POST" action="<?php echo e(route('admin.businesses.impersonate', $business)); ?>"><?php echo csrf_field(); ?>
            <button class="rounded border border-gray-300 bg-white px-4 py-2 text-sm">Log in as owner</button>
        </form>
        <a href="<?php echo e(route('admin.businesses.edit', $business)); ?>" class="rounded bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Edit</a>
    </div>
</div>

<div class="grid gap-6 md:grid-cols-2">
    <section class="rounded-lg border border-gray-200 bg-white p-5">
        <h2 class="mb-3 font-semibold">Public key</h2>
        <code class="block break-all rounded bg-gray-100 px-3 py-2 text-sm"><?php echo e($business->public_key); ?></code>
        <form method="POST" action="<?php echo e(route('admin.businesses.key', $business)); ?>" class="mt-3" onsubmit="return confirm('The current key stops working immediately. Continue?')"><?php echo csrf_field(); ?>
            <button class="text-sm text-red-600 hover:underline">Generate a new key</button>
        </form>
    </section>
    <section class="rounded-lg border border-gray-200 bg-white p-5">
        <h2 class="mb-3 font-semibold">Allowed domains</h2>
        <?php $__empty_1 = true; $__currentLoopData = $business->domains; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><div class="text-sm"><?php echo e($d->domain); ?></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><p class="text-sm text-gray-400">None. Hosted app only.</p><?php endif; ?>
    </section>
    <section class="rounded-lg border border-gray-200 bg-white p-5">
        <h2 class="mb-3 font-semibold">Users</h2>
        <?php $__currentLoopData = $business->users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div class="text-sm"><?php echo e($u->name); ?> <span class="text-gray-400">· <?php echo e($u->email); ?> · <?php echo e($u->role); ?></span></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </section>
    <section class="rounded-lg border border-gray-200 bg-white p-5 text-sm">
        <h2 class="mb-3 font-semibold">Branding</h2>
        <div class="flex items-center gap-2"><span class="inline-block h-4 w-4 rounded border" style="background: <?php echo e($business->theme_color); ?>"></span> <?php echo e($business->theme_color); ?></div>
        <div class="text-gray-500"><?php echo e($business->phone); ?> <?php echo e($business->address); ?></div>
        <div class="text-gray-500"><?php echo e($business->website_url); ?></div>
    </section>
</div>

<h2 class="mb-3 mt-10 font-semibold">Activity</h2>
<?php echo $__env->make('admin._logs', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\runwrk\resources\views/admin/businesses/show.blade.php ENDPATH**/ ?>
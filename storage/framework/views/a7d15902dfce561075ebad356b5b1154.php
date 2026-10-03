<?php $__env->startSection('title', 'Businesses · Runwrk'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <h1 class="text-2xl font-bold">Businesses</h1>
    <a href="<?php echo e(route('admin.businesses.create')); ?>" class="rounded bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">New business</a>
</div>
<form class="mb-4 flex gap-2">
    <input name="q" value="<?php echo e(request('q')); ?>" placeholder="Search name or slug" class="w-64 rounded border border-gray-300 px-3 py-2 text-sm">
    <select name="status" class="rounded border border-gray-300 px-3 py-2 text-sm">
        <option value="">All statuses</option>
        <?php $__currentLoopData = \App\Models\Business::STATUSES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s); ?>" <?php if(request('status') === $s): echo 'selected'; endif; ?>><?php echo e(ucfirst($s)); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <button class="rounded border border-gray-300 bg-white px-4 py-2 text-sm">Filter</button>
</form>
<div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
    <table class="w-full text-left text-sm">
        <thead class="border-b border-gray-200 bg-gray-50 text-xs uppercase text-gray-500">
            <tr><th class="px-4 py-2">Business</th><th class="px-4 py-2">Slug</th><th class="px-4 py-2">Plan</th><th class="px-4 py-2">Users</th><th class="px-4 py-2">Status</th></tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php $__empty_1 = true; $__currentLoopData = $businesses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 font-medium"><a href="<?php echo e(route('admin.businesses.show', $b)); ?>" class="text-indigo-600"><?php echo e($b->name); ?></a></td>
                    <td class="px-4 py-2 text-gray-500"><?php echo e($b->slug); ?></td>
                    <td class="px-4 py-2"><?php echo e($b->plan?->name ?? '—'); ?></td>
                    <td class="px-4 py-2"><?php echo e($b->users_count); ?></td>
                    <td class="px-4 py-2"><span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs capitalize"><?php echo e($b->status); ?></span></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">No businesses.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<div class="mt-4"><?php echo e($businesses->links()); ?></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\runwrk\resources\views/admin/businesses/index.blade.php ENDPATH**/ ?>
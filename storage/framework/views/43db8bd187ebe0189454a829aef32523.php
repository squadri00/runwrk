<?php $__env->startSection('title', 'New business · Runwrk'); ?>

<?php $__env->startSection('content'); ?>
<h1 class="mb-6 text-2xl font-bold">New business</h1>
<form method="POST" action="<?php echo e(route('admin.businesses.store')); ?>" class="max-w-xl space-y-4 rounded-lg border border-gray-200 bg-white p-6">
    <?php echo csrf_field(); ?>
    <?php echo $__env->make('admin._input', ['name' => 'name', 'label' => 'Business name'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin._input', ['name' => 'slug', 'label' => 'Slug', 'placeholder' => 'joes-barber', 'hint' => 'Used in the hosted app address: runwrk.com/slug'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin._input', ['name' => 'owner_name', 'label' => 'Owner name'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin._input', ['name' => 'owner_email', 'label' => 'Owner email', 'type' => 'email'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="mb-1 block text-sm font-medium">Plan</label>
            <select name="plan_id" class="w-full rounded border border-gray-300 px-3 py-2">
                <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($p->id); ?>"><?php echo e($p->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium">Status</label>
            <select name="status" class="w-full rounded border border-gray-300 px-3 py-2">
                <?php $__currentLoopData = \App\Models\Business::STATUSES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s); ?>" <?php if(old('status', 'trial') === $s): echo 'selected'; endif; ?>><?php echo e(ucfirst($s)); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Allowed domains</label>
        <textarea name="domains" rows="3" placeholder="joesbarber.com&#10;www.joesbarber.com" class="w-full rounded border border-gray-300 px-3 py-2"><?php echo e(old('domains')); ?></textarea>
        <p class="mt-1 text-xs text-gray-500">One per line. Websites that may connect to this business. Leave empty for the hosted app only.</p>
    </div>
    <p class="text-sm text-gray-500">A password is generated and shown once after saving.</p>
    <button class="rounded bg-indigo-600 px-5 py-2 font-medium text-white hover:bg-indigo-700">Create</button>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\runwrk\resources\views/admin/businesses/create.blade.php ENDPATH**/ ?>
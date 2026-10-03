<?php $__env->startSection('title', 'Edit '.$business->name.' · Runwrk'); ?>

<?php $__env->startSection('content'); ?>
<h1 class="mb-6 text-2xl font-bold">Edit <?php echo e($business->name); ?></h1>
<form method="POST" action="<?php echo e(route('admin.businesses.update', $business)); ?>" class="max-w-xl space-y-4 rounded-lg border border-gray-200 bg-white p-6">
    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
    <?php echo $__env->make('admin._input', ['name' => 'name', 'label' => 'Business name', 'value' => $business->name], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin._input', ['name' => 'slug', 'label' => 'Slug', 'value' => $business->slug, 'hint' => 'Changing the slug changes the hosted app address.'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin._input', ['name' => 'short_name', 'label' => 'App name (under the home-screen icon)', 'value' => $business->short_name], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="mb-1 block text-sm font-medium">Plan</label>
            <select name="plan_id" class="w-full rounded border border-gray-300 px-3 py-2">
                <option value="">None</option>
                <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($p->id); ?>" <?php if(old('plan_id', $business->plan_id) == $p->id): echo 'selected'; endif; ?>><?php echo e($p->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium">Status</label>
            <select name="status" class="w-full rounded border border-gray-300 px-3 py-2">
                <?php $__currentLoopData = \App\Models\Business::STATUSES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s); ?>" <?php if(old('status', $business->status) === $s): echo 'selected'; endif; ?>><?php echo e(ucfirst($s)); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <p class="mt-1 text-xs text-gray-500">Suspended or cancelled stops the API and owner logins at once.</p>
        </div>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <?php echo $__env->make('admin._input', ['name' => 'theme_color', 'label' => 'Theme colour', 'value' => $business->theme_color, 'hint' => 'Hex, e.g. #111827'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('admin._input', ['name' => 'background_color', 'label' => 'Background colour', 'value' => $business->background_color, 'hint' => 'Hex, e.g. #ffffff'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
    <?php echo $__env->make('admin._input', ['name' => 'phone', 'label' => 'Phone', 'value' => $business->phone], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin._input', ['name' => 'address', 'label' => 'Address', 'value' => $business->address], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin._input', ['name' => 'website_url', 'label' => 'Website', 'type' => 'url', 'value' => $business->website_url], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin._input', ['name' => 'timezone', 'label' => 'Timezone', 'value' => $business->timezone, 'hint' => 'e.g. America/Toronto'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div>
        <label class="mb-1 block text-sm font-medium">Allowed domains</label>
        <textarea name="domains" rows="4" class="w-full rounded border border-gray-300 px-3 py-2"><?php echo e(old('domains', $business->domains->pluck('domain')->implode("\n"))); ?></textarea>
        <p class="mt-1 text-xs text-gray-500">One per line. Use *.example.com to allow subdomains. Removing a domain blocks it immediately.</p>
        <?php $__errorArgs = ['domains'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="flex gap-3">
        <button class="rounded bg-indigo-600 px-5 py-2 font-medium text-white hover:bg-indigo-700">Save</button>
        <a href="<?php echo e(route('admin.businesses.show', $business)); ?>" class="rounded border border-gray-300 px-5 py-2">Cancel</a>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\runwrk\resources\views/admin/businesses/edit.blade.php ENDPATH**/ ?>
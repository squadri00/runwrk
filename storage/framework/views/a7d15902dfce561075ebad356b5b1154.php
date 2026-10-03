<?php if (isset($component)) { $__componentOriginal96f5a324002a49acd05602ce99cfb071 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal96f5a324002a49acd05602ce99cfb071 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.superadmin','data' => ['title' => 'Businesses']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.superadmin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Businesses']); ?>
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <form class="flex gap-2">
            <input name="q" value="<?php echo e(request('q')); ?>" placeholder="Search name or slug" class="w-56 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
            <select name="status" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">All statuses</option>
                <?php $__currentLoopData = \App\Models\Business::STATUSES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s); ?>" <?php if(request('status') === $s): echo 'selected'; endif; ?>><?php echo e(ucfirst($s)); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <button class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium dark:border-slate-700">Filter</button>
        </form>
        <a href="<?php echo e(route('admin.businesses.create')); ?>" class="rounded-lg bg-violet-700 px-3 py-2 text-sm font-semibold text-white hover:bg-violet-800">New business</a>
    </div>
    <div class="overflow-x-auto rounded-xl bg-white ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-500 dark:border-slate-800 dark:text-slate-400">
                <tr><th class="px-4 py-2.5">Business</th><th class="px-4 py-2.5">Slug</th><th class="px-4 py-2.5">Plan</th><th class="px-4 py-2.5">Users</th><th class="px-4 py-2.5">Status</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                <?php $__empty_1 = true; $__currentLoopData = $businesses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                        <td class="px-4 py-2.5 font-medium"><a href="<?php echo e(route('admin.businesses.show', $b)); ?>" class="hover:underline"><?php echo e($b->name); ?></a></td>
                        <td class="px-4 py-2.5 text-slate-500 dark:text-slate-400"><?php echo e($b->slug); ?></td>
                        <td class="px-4 py-2.5"><?php echo e($b->plan?->name ?? '—'); ?></td>
                        <td class="px-4 py-2.5"><?php echo e($b->users_count); ?></td>
                        <td class="px-4 py-2.5"><span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs capitalize dark:bg-slate-800"><?php echo e($b->status); ?></span></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">No businesses.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="mt-4"><?php echo e($businesses->links()); ?></div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal96f5a324002a49acd05602ce99cfb071)): ?>
<?php $attributes = $__attributesOriginal96f5a324002a49acd05602ce99cfb071; ?>
<?php unset($__attributesOriginal96f5a324002a49acd05602ce99cfb071); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal96f5a324002a49acd05602ce99cfb071)): ?>
<?php $component = $__componentOriginal96f5a324002a49acd05602ce99cfb071; ?>
<?php unset($__componentOriginal96f5a324002a49acd05602ce99cfb071); ?>
<?php endif; ?>
<?php /**PATH D:\xampp\htdocs\runwrk\resources\views/admin/businesses/index.blade.php ENDPATH**/ ?>
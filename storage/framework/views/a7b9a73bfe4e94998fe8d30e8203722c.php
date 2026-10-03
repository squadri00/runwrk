<?php if (isset($component)) { $__componentOriginal96f5a324002a49acd05602ce99cfb071 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal96f5a324002a49acd05602ce99cfb071 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.superadmin','data' => ['title' => 'Plans & pricing']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.superadmin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Plans & pricing']); ?>
    <div class="mb-4 flex items-center justify-between">
        <p class="text-sm text-slate-500 dark:text-slate-400">These appear on the public pricing page. The current plans are demo placeholders: edit them with your real pricing.</p>
        <a href="<?php echo e(route('admin.plans.create')); ?>" class="shrink-0 rounded-lg bg-violet-700 px-3 py-2 text-sm font-semibold text-white hover:bg-violet-800">New plan</a>
    </div>
    <div class="overflow-x-auto rounded-xl bg-white ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-500 dark:border-slate-800 dark:text-slate-400">
                <tr><th class="px-4 py-2.5">Plan</th><th class="px-4 py-2.5">Price</th><th class="px-4 py-2.5">Businesses</th><th class="px-4 py-2.5">Visibility</th><th class="px-4 py-2.5"></th></tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="<?php echo e($plan->archived_at ? 'opacity-50' : ''); ?>">
                        <td class="px-4 py-2.5 font-medium"><?php echo e($plan->name); ?> <span class="font-mono text-xs text-slate-400"><?php echo e($plan->code); ?></span></td>
                        <td class="px-4 py-2.5"><?php echo e($plan->priceLabel()); ?><?php if (! ($plan->isFree())): ?>/<?php echo e($plan->interval); ?><?php endif; ?></td>
                        <td class="px-4 py-2.5"><?php echo e($plan->businesses_count); ?></td>
                        <td class="px-4 py-2.5 text-xs"><?php echo e($plan->archived_at ? 'Archived' : ($plan->is_public ? 'Public' : 'Hidden')); ?></td>
                        <td class="flex justify-end gap-3 px-4 py-2.5 text-sm">
                            <a href="<?php echo e(route('admin.plans.edit', $plan)); ?>" class="underline">Edit</a>
                            <form method="POST" action="<?php echo e(route('admin.plans.archive', $plan)); ?>"><?php echo csrf_field(); ?>
                                <button class="text-slate-500 underline"><?php echo e($plan->archived_at ? 'Restore' : 'Archive'); ?></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
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
<?php /**PATH D:\xampp\htdocs\runwrk\resources\views/admin/plans/index.blade.php ENDPATH**/ ?>
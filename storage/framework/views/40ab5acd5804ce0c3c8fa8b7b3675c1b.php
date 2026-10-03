<?php if (isset($component)) { $__componentOriginal96f5a324002a49acd05602ce99cfb071 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal96f5a324002a49acd05602ce99cfb071 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.superadmin','data' => ['title' => 'Messages']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.superadmin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Messages']); ?>
    <div class="overflow-x-auto rounded-xl bg-white ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-500 dark:border-slate-800 dark:text-slate-400">
                <tr><th class="px-4 py-2.5">From</th><th class="px-4 py-2.5">Interested in</th><th class="px-4 py-2.5">Message</th><th class="px-4 py-2.5">When</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                <?php $__empty_1 = true; $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 <?php echo e($m->read_at ? '' : 'font-semibold'); ?>">
                        <td class="px-4 py-2.5"><a href="<?php echo e(route('admin.messages.show', $m)); ?>" class="hover:underline"><?php echo e($m->name); ?></a><div class="text-xs font-normal text-slate-500"><?php echo e($m->email); ?></div></td>
                        <td class="px-4 py-2.5"><?php echo e(\App\Models\ContactMessage::SERVICES[$m->service] ?? $m->service); ?></td>
                        <td class="max-w-xs truncate px-4 py-2.5 font-normal text-slate-500"><?php echo e($m->message); ?></td>
                        <td class="whitespace-nowrap px-4 py-2.5 font-normal text-slate-500"><?php echo e($m->created_at->diffForHumans()); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="4" class="px-4 py-10 text-center text-slate-500">No messages yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="mt-4"><?php echo e($messages->links()); ?></div>
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
<?php /**PATH D:\xampp\htdocs\runwrk\resources\views/admin/messages/index.blade.php ENDPATH**/ ?>
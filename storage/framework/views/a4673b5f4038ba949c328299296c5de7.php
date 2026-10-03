<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => 'Notifications']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Notifications']); ?>
    <div class="mb-4 flex items-center justify-between">
        <p class="text-sm text-slate-500 dark:text-slate-400"><?php echo e(number_format($subscribers)); ?> <?php echo e(Str::plural('customer', $subscribers)); ?> can receive your messages.</p>
        <a href="<?php echo e(route('notifications.create')); ?>" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800 dark:bg-white dark:text-slate-900">New message</a>
    </div>

    <div class="overflow-x-auto rounded-xl bg-white ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-500 dark:border-slate-800 dark:text-slate-400">
                <tr><th class="px-4 py-2.5">Message</th><th class="px-4 py-2.5">Status</th><th class="px-4 py-2.5">Delivered</th><th class="px-4 py-2.5">Opened</th><th class="px-4 py-2.5">When</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                <?php $__empty_1 = true; $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                        <td class="px-4 py-2.5"><a href="<?php echo e(route('notifications.show', $m->id)); ?>" class="font-medium hover:underline"><?php echo e($m->title); ?></a><div class="max-w-xs truncate text-xs text-slate-500"><?php echo e($m->body); ?></div></td>
                        <td class="px-4 py-2.5"><?php if (isset($component)) { $__componentOriginal31bf215fc831c52c5c3c5e6b7542929f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal31bf215fc831c52c5c3c5e6b7542929f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.message-status','data' => ['status' => $m->status]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('message-status'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($m->status)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal31bf215fc831c52c5c3c5e6b7542929f)): ?>
<?php $attributes = $__attributesOriginal31bf215fc831c52c5c3c5e6b7542929f; ?>
<?php unset($__attributesOriginal31bf215fc831c52c5c3c5e6b7542929f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal31bf215fc831c52c5c3c5e6b7542929f)): ?>
<?php $component = $__componentOriginal31bf215fc831c52c5c3c5e6b7542929f; ?>
<?php unset($__componentOriginal31bf215fc831c52c5c3c5e6b7542929f); ?>
<?php endif; ?></td>
                        <td class="px-4 py-2.5"><?php echo e($m->success_count); ?> / <?php echo e($m->target_count); ?></td>
                        <td class="px-4 py-2.5"><?php echo e($m->click_count); ?></td>
                        <td class="whitespace-nowrap px-4 py-2.5 text-slate-500 dark:text-slate-400"><?php echo e(($m->started_at ?? $m->scheduled_at)?->timezone(auth('web')->user()->business->timezone)->format('M j, g:i a')); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="5" class="px-4 py-10 text-center text-slate-500">No messages yet. Send your first one.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="mt-4"><?php echo e($messages->links()); ?></div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $attributes = $__attributesOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__attributesOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $component = $__componentOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__componentOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>
<?php /**PATH D:\xampp\htdocs\runwrk\resources\views/app/notifications/index.blade.php ENDPATH**/ ?>
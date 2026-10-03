<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => $message->title]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($message->title)]); ?>
    <?php if(in_array($message->status, ['sending']) || ($message->status === 'scheduled' && $message->scheduled_at->lte(now()->addMinute()))): ?>
        <meta http-equiv="refresh" content="5">
    <?php endif; ?>

    <?php ($tz = auth('web')->user()->business->timezone); ?>

    <div class="max-w-2xl space-y-4">
        <div class="rounded-xl bg-white p-5 ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-base font-semibold"><?php echo e($message->title); ?></h2>
                    <p class="mt-1 text-sm text-slate-600 dark:text-slate-300"><?php echo e($message->body); ?></p>
                    <?php if($message->url): ?><p class="mt-2 break-all text-xs text-slate-400"><?php echo e($message->url); ?></p><?php endif; ?>
                </div>
                <?php if (isset($component)) { $__componentOriginal31bf215fc831c52c5c3c5e6b7542929f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal31bf215fc831c52c5c3c5e6b7542929f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.message-status','data' => ['status' => $message->status]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('message-status'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($message->status)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal31bf215fc831c52c5c3c5e6b7542929f)): ?>
<?php $attributes = $__attributesOriginal31bf215fc831c52c5c3c5e6b7542929f; ?>
<?php unset($__attributesOriginal31bf215fc831c52c5c3c5e6b7542929f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal31bf215fc831c52c5c3c5e6b7542929f)): ?>
<?php $component = $__componentOriginal31bf215fc831c52c5c3c5e6b7542929f; ?>
<?php unset($__componentOriginal31bf215fc831c52c5c3c5e6b7542929f); ?>
<?php endif; ?>
            </div>
            <?php if($message->image_path): ?><img src="<?php echo e(Storage::disk('public')->url($message->image_path)); ?>" alt="" class="mt-3 max-h-40 rounded-lg"><?php endif; ?>
            <p class="mt-3 text-xs text-slate-400">
                By <?php echo e($message->author?->name ?? 'unknown'); ?> ·
                <?php if($message->status === 'scheduled'): ?> scheduled for <?php echo e($message->scheduled_at->timezone($tz)->format('M j, g:i a')); ?>

                <?php else: ?> started <?php echo e($message->started_at?->timezone($tz)->format('M j, g:i a')); ?><?php if($message->finished_at): ?>, finished <?php echo e($message->finished_at->timezone($tz)->format('g:i a')); ?><?php endif; ?>
                <?php endif; ?>
            </p>
        </div>

        <?php if($message->started_at): ?>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                <?php $__currentLoopData = [['Audience', $message->target_count], ['Delivered', $message->success_count], ['Opened', $message->click_count], ['Not delivered', $message->failure_count + $message->expired_count]]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label, $value]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="rounded-xl bg-white p-4 ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
                        <div class="text-2xl font-semibold"><?php echo e(number_format($value)); ?></div>
                        <div class="mt-1 text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400"><?php echo e($label); ?></div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php if($message->target_count): ?>
                <div class="h-2 overflow-hidden rounded-full bg-slate-200 dark:bg-slate-800"><div class="h-full bg-emerald-600" style="width: <?php echo e(min(100, round($message->processed() / $message->target_count * 100))); ?>%"></div></div>
            <?php endif; ?>
            <?php if($message->expired_count): ?><p class="text-xs text-slate-400"><?php echo e($message->expired_count); ?> customer(s) removed the app or turned notifications off, so they were taken off the list.</p><?php endif; ?>
        <?php endif; ?>

        <div class="flex gap-3">
            <?php if($message->isCancellable()): ?>
                <form method="POST" action="<?php echo e(route('notifications.cancel', $message->id)); ?>" onsubmit="return confirm('Cancel this message?')"><?php echo csrf_field(); ?>
                    <button class="rounded-lg border border-rose-300 px-4 py-2 text-sm font-medium text-rose-600 dark:border-rose-800">Cancel message</button>
                </form>
            <?php endif; ?>
            <a href="<?php echo e(route('notifications.index')); ?>" class="rounded-lg border border-slate-300 px-4 py-2 text-sm dark:border-slate-700">Back</a>
        </div>
    </div>
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
<?php /**PATH D:\xampp\htdocs\runwrk\resources\views/app/notifications/show.blade.php ENDPATH**/ ?>
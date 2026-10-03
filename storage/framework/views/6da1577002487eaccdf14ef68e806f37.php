<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => 'Subscribers']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Subscribers']); ?>
    <div class="grid max-w-4xl grid-cols-2 gap-4 sm:grid-cols-4">
        <?php $__currentLoopData = [['Total', $total], ['New this week', $week], ['Android', $byPlatform['android'] ?? 0], ['iPhone', $byPlatform['ios'] ?? 0]]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label, $value]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="rounded-xl bg-white p-4 ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
                <div class="text-2xl font-semibold"><?php echo e(number_format($value)); ?></div>
                <div class="mt-1 text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400"><?php echo e($label); ?></div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <p class="mt-4 max-w-2xl text-sm text-slate-500 dark:text-slate-400">Customers join when they tap "Turn on notifications" in your app. We keep no names or phone numbers, only what is needed to deliver your messages. Customers who remove the app drop off automatically.</p>

    <div class="mt-4 max-w-2xl overflow-hidden rounded-xl bg-white ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
        <div class="border-b border-slate-200 px-4 py-3 text-sm font-semibold dark:border-slate-800">Latest sign-ups</div>
        <div class="divide-y divide-slate-200 text-sm dark:divide-slate-800">
            <?php $__empty_1 = true; $__currentLoopData = $recent; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="flex items-center justify-between px-4 py-2.5">
                    <span class="capitalize"><?php echo e($s->platform === 'ios' ? 'iPhone' : $s->platform); ?> <span class="text-xs text-slate-400">· via <?php echo e($s->source === 'hosted' ? 'your app page' : 'your website'); ?></span></span>
                    <span class="text-xs text-slate-500 dark:text-slate-400"><?php echo e($s->created_at->diffForHumans()); ?></span>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="px-4 py-8 text-center text-slate-500">No subscribers yet.</div>
            <?php endif; ?>
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
<?php /**PATH D:\xampp\htdocs\runwrk\resources\views/app/subscribers.blade.php ENDPATH**/ ?>
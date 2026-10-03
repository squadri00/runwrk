<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => 'Dashboard']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Dashboard']); ?>
    <?php
        $steps = [
            ['Add your logo', 'Your logo becomes the icon on your customers\' home screens.', (bool) $business->logo_path, route('branding.edit'), 'Open branding'],
            ['Connect your website', 'We link your website so it can offer your app to visitors.', $business->domains_count > 0, null, 'We set this up with you'],
            ['Invite your team', 'Staff can send messages to your customers.', $business->users_count > 1, route('team.index'), 'Open team'],
            ['Get your first customers', 'Share your app page so customers can turn on notifications.', $subscribers > 0, route('subscribers.index'), 'Open subscribers'],
        ];
        $isOwner = auth('web')->user()->role === 'owner';
    ?>

    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <?php $__currentLoopData = [['Subscribers', $subscribers], ['Messages sent', $sent], ['Opens (30 days)', $clicks]]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label, $value]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="rounded-xl bg-white p-4 ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
                <div class="text-2xl font-semibold"><?php echo e(number_format($value)); ?></div>
                <div class="mt-1 text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400"><?php echo e($label); ?></div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('notifications.create')); ?>" class="flex items-center justify-center rounded-xl bg-slate-900 p-4 text-sm font-semibold text-white hover:bg-slate-800 dark:bg-white dark:text-slate-900">Send a message</a>
    </div>

    <div class="mt-4 rounded-xl bg-white p-5 ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
        <h2 class="text-sm font-semibold">Your app page</h2>
        <p class="mt-1 break-all font-mono text-sm text-slate-600 dark:text-slate-300"><?php echo e(url($business->slug)); ?></p>
        <p class="mt-1 text-xs text-slate-400">Share this link or put it on a poster. Customers open it on their phone, add it to their home screen and turn on notifications.</p>
    </div>

    <div class="mt-4 rounded-xl bg-white ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
        <div class="border-b border-slate-200 px-5 py-3 text-sm font-semibold dark:border-slate-800">Get set up</div>
        <div class="divide-y divide-slate-200 dark:divide-slate-800">
            <?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$title, $text, $done, $href, $cta]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-center gap-4 px-5 py-3.5">
                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-xs font-bold <?php echo e($done ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-500 dark:bg-slate-800'); ?>"><?php echo e($done ? '✓' : ''); ?></span>
                    <div class="min-w-0 flex-1">
                        <div class="text-sm font-medium"><?php echo e($title); ?></div>
                        <div class="text-xs text-slate-500 dark:text-slate-400"><?php echo e($text); ?></div>
                    </div>
                    <?php if (! ($done)): ?>
                        <?php if($href && ($isOwner || ! in_array($href, [route('branding.edit'), route('team.index')]))): ?>
                            <a href="<?php echo e($href); ?>" class="shrink-0 text-sm font-medium underline"><?php echo e($cta); ?></a>
                        <?php elseif(! $href): ?>
                            <span class="shrink-0 text-xs text-slate-400"><?php echo e($cta); ?></span>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <?php if($recent->isNotEmpty()): ?>
        <div class="mt-4 rounded-xl bg-white ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
            <div class="border-b border-slate-200 px-5 py-3 text-sm font-semibold dark:border-slate-800">Recent messages</div>
            <div class="divide-y divide-slate-200 text-sm dark:divide-slate-800">
                <?php $__currentLoopData = $recent; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('notifications.show', $m->id)); ?>" class="flex items-center justify-between px-5 py-2.5 hover:bg-slate-50 dark:hover:bg-slate-800/50">
                        <span class="truncate"><?php echo e($m->title); ?></span><?php if (isset($component)) { $__componentOriginal31bf215fc831c52c5c3c5e6b7542929f = $component; } ?>
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
<?php endif; ?>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    <?php endif; ?>
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
<?php /**PATH D:\xampp\htdocs\runwrk\resources\views/app/dashboard.blade.php ENDPATH**/ ?>
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
        ];
    ?>

    <div class="rounded-xl bg-white p-5 ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
        <h2 class="text-sm font-semibold">Welcome, <?php echo e(auth('web')->user()->name); ?></h2>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Your app address: <span class="font-mono"><?php echo e(url($business->slug)); ?></span> (goes live in the next update)</p>
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
                        <?php if($href && auth('web')->user()->role === 'owner'): ?>
                            <a href="<?php echo e($href); ?>" class="shrink-0 text-sm font-medium underline"><?php echo e($cta); ?></a>
                        <?php elseif(! $href): ?>
                            <span class="shrink-0 text-xs text-slate-400"><?php echo e($cta); ?></span>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <p class="mt-4 text-sm text-slate-500 dark:text-slate-400">Sending messages to your customers' phones arrives in the next update.</p>
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
<?php if (isset($component)) { $__componentOriginalfefb4fd9b7004fa65f70c415ac76903e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfefb4fd9b7004fa65f70c415ac76903e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.site','data' => ['title' => 'Pricing']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.site'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Pricing']); ?>
    <main class="mx-auto max-w-6xl px-4 py-16">
        <div class="mb-12 text-center">
            <h1 class="text-3xl font-bold tracking-tight">Simple pricing. No surprises.</h1>
            <p class="mt-2 text-slate-500 dark:text-slate-400">Your own app and a message to your customers' phones, anytime.</p>
        </div>

        <?php if($plans->isEmpty()): ?>
            <p class="text-center text-slate-500">Pricing isn't available right now. Please check back soon.</p>
        <?php else: ?>
            <?php ($cols = [1 => 'lg:grid-cols-1', 2 => 'lg:grid-cols-2', 3 => 'lg:grid-cols-3', 4 => 'lg:grid-cols-4'][min($plans->count(), 4)]); ?>
            <div class="grid gap-6 sm:grid-cols-2 <?php echo e($cols); ?>">
                <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex flex-col rounded-2xl bg-white p-6 ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
                        <h2 class="text-lg font-semibold"><?php echo e($plan->name); ?></h2>
                        <p class="mt-3 text-4xl font-bold"><?php echo e($plan->priceLabel()); ?><?php if (! ($plan->isFree())): ?><span class="text-base font-normal text-slate-500">/<?php echo e($plan->interval); ?></span><?php endif; ?></p>
                        <?php if($plan->description): ?><p class="mt-2 text-sm text-slate-500 dark:text-slate-400"><?php echo e($plan->description); ?></p><?php endif; ?>
                        <ul class="mt-5 flex-1 space-y-2 text-sm">
                            <?php $__currentLoopData = $plan->features ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="flex gap-2"><span class="text-emerald-600">✓</span> <?php echo e($feature); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                        <?php if($signups): ?>
                            <a href="<?php echo e(route('register', ['plan' => $plan->code])); ?>" class="mt-6 block rounded-lg bg-slate-900 px-4 py-2.5 text-center text-sm font-semibold text-white hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-200">
                                <?php echo e($plan->isFree() ? 'Start free' : 'Get started'); ?>

                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </main>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfefb4fd9b7004fa65f70c415ac76903e)): ?>
<?php $attributes = $__attributesOriginalfefb4fd9b7004fa65f70c415ac76903e; ?>
<?php unset($__attributesOriginalfefb4fd9b7004fa65f70c415ac76903e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfefb4fd9b7004fa65f70c415ac76903e)): ?>
<?php $component = $__componentOriginalfefb4fd9b7004fa65f70c415ac76903e; ?>
<?php unset($__componentOriginalfefb4fd9b7004fa65f70c415ac76903e); ?>
<?php endif; ?>
<?php /**PATH D:\xampp\htdocs\runwrk\resources\views/site/pricing.blade.php ENDPATH**/ ?>
<?php if (isset($component)) { $__componentOriginal96f5a324002a49acd05602ce99cfb071 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal96f5a324002a49acd05602ce99cfb071 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.superadmin','data' => ['title' => $business->name]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.superadmin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($business->name)]); ?>
    <?php if(session('invite_url')): ?>
        <div class="mb-4 rounded-lg bg-amber-50 px-4 py-3 text-sm text-amber-900 ring-1 ring-amber-200 dark:bg-amber-500/15 dark:text-amber-200 dark:ring-amber-500/30">
            A set-password email has been sent. If mail isn't set up yet, send this link to the owner yourself (works for 3 days):
            <code class="mt-1 block break-all font-mono text-xs"><?php echo e(session('invite_url')); ?></code>
        </div>
    <?php endif; ?>

    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-500 dark:text-slate-400">runwrk.com/<?php echo e($business->slug); ?> · <span class="capitalize"><?php echo e($business->status); ?></span> · <?php echo e($business->plan?->name ?? 'No plan'); ?></p>
        <div class="flex gap-2">
            <form method="POST" action="<?php echo e(route('admin.businesses.impersonate', $business)); ?>"><?php echo csrf_field(); ?>
                <button class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm font-medium dark:border-slate-700">Log in as owner</button>
            </form>
            <a href="<?php echo e(route('admin.businesses.edit', $business)); ?>" class="rounded-lg bg-violet-700 px-3 py-1.5 text-sm font-semibold text-white hover:bg-violet-800">Edit</a>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <section class="rounded-xl bg-white p-5 ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
            <h2 class="mb-3 text-sm font-semibold">Public key</h2>
            <code class="block break-all rounded-lg bg-slate-100 px-3 py-2 text-sm dark:bg-slate-800"><?php echo e($business->public_key); ?></code>
            <form method="POST" action="<?php echo e(route('admin.businesses.key', $business)); ?>" class="mt-3" onsubmit="return confirm('The current key stops working immediately. Continue?')"><?php echo csrf_field(); ?>
                <button class="text-sm text-rose-600 hover:underline">Generate a new key</button>
            </form>
        </section>
        <section class="rounded-xl bg-white p-5 ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
            <h2 class="mb-3 text-sm font-semibold">Allowed domains</h2>
            <?php $__empty_1 = true; $__currentLoopData = $business->domains; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><div class="text-sm"><?php echo e($d->domain); ?></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><p class="text-sm text-slate-400">None. Hosted app only.</p><?php endif; ?>
        </section>
        <section class="rounded-xl bg-white p-5 ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
            <h2 class="mb-3 text-sm font-semibold">Users</h2>
            <?php $__currentLoopData = $business->users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-center justify-between gap-2 py-1 text-sm">
                    <span><?php echo e($u->name); ?> <span class="text-slate-400">· <?php echo e($u->email); ?> · <?php echo e($u->role); ?></span></span>
                    <form method="POST" action="<?php echo e(route('admin.businesses.invite', [$business, $u])); ?>"><?php echo csrf_field(); ?>
                        <button class="text-xs text-slate-500 underline hover:text-slate-800 dark:hover:text-slate-200">Send password link</button>
                    </form>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </section>
        <section class="rounded-xl bg-white p-5 text-sm ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
            <h2 class="mb-3 font-semibold">Branding</h2>
            <div class="flex items-center gap-2"><span class="inline-block h-4 w-4 rounded border" style="background: <?php echo e($business->theme_color); ?>"></span> <?php echo e($business->theme_color); ?> / <?php echo e($business->background_color); ?></div>
            <div class="mt-1 text-slate-500 dark:text-slate-400"><?php echo e($business->phone); ?> <?php echo e($business->address); ?></div>
            <div class="text-slate-500 dark:text-slate-400"><?php echo e($business->website_url); ?></div>
        </section>
    </div>

    <h2 class="mb-3 mt-8 text-sm font-semibold">Activity</h2>
    <?php echo $__env->make('admin._logs', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
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
<?php /**PATH D:\xampp\htdocs\runwrk\resources\views/admin/businesses/show.blade.php ENDPATH**/ ?>
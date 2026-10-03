<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['heading' => null, 'subheading' => null, 'wide' => false]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['heading' => null, 'subheading' => null, 'wide' => false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($heading ? $heading.' · '.config('app.name') : config('app.name')); ?></title>
    <?php echo $__env->make('partials.theme-head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="h-full bg-slate-100 text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
    <div class="absolute left-4 top-4">
        <a href="<?php echo e(url('/')); ?>" class="text-sm font-medium text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200">&larr; Back to website</a>
    </div>
    <div class="absolute right-4 top-4"><?php if (isset($component)) { $__componentOriginal2090438866f3dcdb76cd8b070bcc302d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2090438866f3dcdb76cd8b070bcc302d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.theme-toggle','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('theme-toggle'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2090438866f3dcdb76cd8b070bcc302d)): ?>
<?php $attributes = $__attributesOriginal2090438866f3dcdb76cd8b070bcc302d; ?>
<?php unset($__attributesOriginal2090438866f3dcdb76cd8b070bcc302d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2090438866f3dcdb76cd8b070bcc302d)): ?>
<?php $component = $__componentOriginal2090438866f3dcdb76cd8b070bcc302d; ?>
<?php unset($__componentOriginal2090438866f3dcdb76cd8b070bcc302d); ?>
<?php endif; ?></div>
    <div class="flex min-h-full flex-col items-center justify-center px-4 py-12">
        <div class="w-full <?php echo e($wide ? 'max-w-md' : 'max-w-sm'); ?>">
            <div class="mb-8 text-center">
                <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-slate-900 text-lg font-bold text-white dark:bg-white dark:text-slate-900"><?php echo e(mb_substr(config('app.name'), 0, 1)); ?></span>
                <h1 class="mt-3 text-xl font-semibold"><?php echo e($heading ?? config('app.name')); ?></h1>
                <?php if($subheading): ?><p class="mt-1 text-sm text-slate-500 dark:text-slate-400"><?php echo e($subheading); ?></p><?php endif; ?>
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
                <?php echo e($slot); ?>

            </div>

            <?php if(isset($footer)): ?>
                <div class="mt-6 text-center text-sm text-slate-500 dark:text-slate-400"><?php echo e($footer); ?></div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php /**PATH D:\xampp\htdocs\runwrk\resources\views/components/layouts/guest.blade.php ENDPATH**/ ?>
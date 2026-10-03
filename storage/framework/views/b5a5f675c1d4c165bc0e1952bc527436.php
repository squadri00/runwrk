<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['title' => 'Dashboard']));

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

foreach (array_filter((['title' => 'Dashboard']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $user = auth('web')->user();
    $business = $user?->business;
    $isOwner = $user?->role === 'owner';
    $impersonating = app(\App\Domain\Tenancy\Impersonation::class)->isActive();
    $link = fn ($route) => request()->routeIs($route)
        ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900'
        : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800';
?>

<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($title); ?> · <?php echo e(config('app.name')); ?></title>
    <?php echo $__env->make('partials.theme-head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="h-full bg-slate-100 text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
<?php if($impersonating): ?>
    <form method="POST" action="<?php echo e(route('impersonation.stop')); ?>" class="flex items-center justify-center gap-3 bg-amber-400 px-4 py-2 text-sm font-medium text-amber-950">
        <?php echo csrf_field(); ?>
        <span>You are viewing this account as support.</span>
        <button class="rounded bg-amber-950 px-3 py-1 text-white">Return to admin</button>
    </form>
<?php endif; ?>
<div class="min-h-full">
    <div class="mx-auto flex max-w-7xl">
        <div id="mobile-nav-backdrop" class="fixed inset-0 z-30 hidden bg-black/50 md:hidden" onclick="toggleMobileNav()"></div>

        <aside id="mobile-nav-sidebar" class="fixed inset-y-0 left-0 z-40 w-64 -translate-x-full overflow-y-auto border-r border-slate-200 bg-white transition-transform duration-200 md:static md:z-auto md:w-60 md:shrink-0 md:translate-x-0 dark:border-slate-800 dark:bg-slate-900">
            <div class="flex h-14 items-center gap-2 border-b border-slate-200 px-5 dark:border-slate-800">
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg text-sm font-bold text-white" style="background: <?php echo e($business?->theme_color ?? '#111827'); ?>"><?php echo e(mb_substr($business?->name ?? config('app.name'), 0, 1)); ?></span>
                <span class="truncate font-semibold"><?php echo e($business?->name); ?></span>
                <button onclick="toggleMobileNav()" class="ml-auto rounded-lg p-1 text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 md:hidden" aria-label="Close menu">✕</button>
            </div>
            <nav class="space-y-1 p-3 text-sm">
                <a href="<?php echo e(route('dashboard')); ?>" class="block rounded-lg px-3 py-2 font-medium <?php echo e($link('dashboard')); ?>">Dashboard</a>
                <a href="<?php echo e(route('notifications.index')); ?>" class="block rounded-lg px-3 py-2 font-medium <?php echo e($link('notifications.*')); ?>">Notifications</a>
                <a href="<?php echo e(route('connect.index')); ?>" class="block rounded-lg px-3 py-2 font-medium <?php echo e($link('connect.*')); ?>">Connect website</a>
                <a href="<?php echo e(route('subscribers.index')); ?>" class="block rounded-lg px-3 py-2 font-medium <?php echo e($link('subscribers.*')); ?>">Subscribers</a>
                <?php if($isOwner): ?>
                    <a href="<?php echo e(route('branding.edit')); ?>" class="block rounded-lg px-3 py-2 font-medium <?php echo e($link('branding.*')); ?>">Branding</a>
                    <a href="<?php echo e(route('team.index')); ?>" class="block rounded-lg px-3 py-2 font-medium <?php echo e($link('team.*')); ?>">Team</a>
                <?php endif; ?>
                <a href="<?php echo e(route('account.edit')); ?>" class="block rounded-lg px-3 py-2 font-medium <?php echo e($link('account.*')); ?>">My account</a>
            </nav>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="flex h-14 items-center justify-between border-b border-slate-200 bg-white px-4 md:px-6 dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center gap-2 md:gap-3">
                    <button onclick="toggleMobileNav()" class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 md:hidden dark:text-slate-300 dark:hover:bg-slate-800" aria-label="Open menu">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    </button>
                    <h1 class="text-sm font-semibold text-slate-700 dark:text-slate-200"><?php echo e($title); ?></h1>
                </div>
                <div class="flex items-center gap-3 text-sm">
                    <?php if (isset($component)) { $__componentOriginal2090438866f3dcdb76cd8b070bcc302d = $component; } ?>
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
<?php endif; ?>
                    <span class="hidden text-slate-500 sm:inline dark:text-slate-400"><?php echo e($user?->name); ?></span>
                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                        <?php echo csrf_field(); ?>
                        <button class="rounded-lg border border-slate-200 px-3 py-1.5 font-medium text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">Sign out</button>
                    </form>
                </div>
            </header>

            <main class="flex-1 p-4 md:p-6">
                <?php if (isset($component)) { $__componentOriginal5168fdb0c14fd91c6598264bc4be63f2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5168fdb0c14fd91c6598264bc4be63f2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.flash','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flash'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5168fdb0c14fd91c6598264bc4be63f2)): ?>
<?php $attributes = $__attributesOriginal5168fdb0c14fd91c6598264bc4be63f2; ?>
<?php unset($__attributesOriginal5168fdb0c14fd91c6598264bc4be63f2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5168fdb0c14fd91c6598264bc4be63f2)): ?>
<?php $component = $__componentOriginal5168fdb0c14fd91c6598264bc4be63f2; ?>
<?php unset($__componentOriginal5168fdb0c14fd91c6598264bc4be63f2); ?>
<?php endif; ?>
                <?php echo e($slot); ?>

            </main>
        </div>
    </div>
</div>
<script>
    function toggleMobileNav() {
        document.getElementById('mobile-nav-sidebar').classList.toggle('-translate-x-full');
        document.getElementById('mobile-nav-backdrop').classList.toggle('hidden');
    }
</script>
</body>
</html>
<?php /**PATH D:\xampp\htdocs\runwrk\resources\views/components/layouts/app.blade.php ENDPATH**/ ?>
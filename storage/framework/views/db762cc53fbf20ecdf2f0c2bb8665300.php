<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $__env->yieldContent('title', 'Runwrk'); ?></title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 antialiased">
<?php if(app(\App\Domain\Tenancy\Impersonation::class)->isActive()): ?>
    <form method="POST" action="<?php echo e(route('impersonation.stop')); ?>" class="flex items-center justify-center gap-3 bg-amber-400 px-4 py-2 text-sm font-medium text-amber-950">
        <?php echo csrf_field(); ?>
        <span>You are viewing this account as support.</span>
        <button class="rounded bg-amber-950 px-3 py-1 text-white">Return to admin</button>
    </form>
<?php endif; ?>
<?php echo $__env->yieldContent('body'); ?>
</body>
</html>
<?php /**PATH D:\xampp\htdocs\runwrk\resources\views/layouts/app.blade.php ENDPATH**/ ?>
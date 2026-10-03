<?php $__env->startSection('title', 'Admin sign in · Runwrk'); ?>

<?php $__env->startSection('body'); ?>
<main class="mx-auto flex min-h-screen max-w-sm flex-col justify-center px-4">
    <h1 class="mb-6 text-2xl font-bold">Runwrk admin</h1>
    <form method="POST" action="<?php echo e(route('admin.login')); ?>" class="space-y-4 rounded-lg border border-gray-200 bg-white p-6">
        <?php echo csrf_field(); ?>
        <div>
            <label class="mb-1 block text-sm font-medium">Email</label>
            <input type="email" name="email" value="<?php echo e(old('email')); ?>" required autofocus class="w-full rounded border border-gray-300 px-3 py-2">
            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium">Password</label>
            <input type="password" name="password" required class="w-full rounded border border-gray-300 px-3 py-2">
        </div>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="remember"> Remember me</label>
        <button class="w-full rounded bg-indigo-600 py-2 font-medium text-white hover:bg-indigo-700">Sign in</button>
    </form>
</main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\runwrk\resources\views/admin/login.blade.php ENDPATH**/ ?>
<div>
    <label class="mb-1 block text-sm font-medium"><?php echo e($label); ?></label>
    <input type="<?php echo e($type ?? 'text'); ?>" name="<?php echo e($name); ?>" value="<?php echo e(old($name, $value ?? '')); ?>" placeholder="<?php echo e($placeholder ?? ''); ?>" class="w-full rounded border border-gray-300 px-3 py-2">
    <?php if(isset($hint)): ?><p class="mt-1 text-xs text-gray-500"><?php echo e($hint); ?></p><?php endif; ?>
    <?php $__errorArgs = [$name];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
</div>
<?php /**PATH D:\xampp\htdocs\runwrk\resources\views/admin/_input.blade.php ENDPATH**/ ?>
<div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
    <table class="w-full text-left text-sm">
        <thead class="border-b border-gray-200 bg-gray-50 text-xs uppercase text-gray-500">
            <tr><th class="px-4 py-2">When</th><th class="px-4 py-2">Action</th><th class="px-4 py-2">By</th><th class="px-4 py-2">Details</th></tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="whitespace-nowrap px-4 py-2 text-gray-500"><?php echo e($log->created_at?->format('M j, H:i')); ?></td>
                    <td class="px-4 py-2 font-medium"><?php echo e($log->action); ?></td>
                    <td class="px-4 py-2"><?php echo e($log->actor_label); ?><?php if($log->impersonated_by): ?> <span class="text-amber-600">(support)</span><?php endif; ?></td>
                    <td class="px-4 py-2 text-xs text-gray-500"><?php echo e($log->changes ? json_encode($log->changes) : ''); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">Nothing yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php /**PATH D:\xampp\htdocs\runwrk\resources\views/admin/_logs.blade.php ENDPATH**/ ?>
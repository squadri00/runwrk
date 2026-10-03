<div class="accordion" id="<?php echo e($id); ?>">
    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => [$q, $a]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="accordion-item">
            <h3 class="accordion-header" id="<?php echo e($id); ?>-h<?php echo e($i); ?>">
                <button class="accordion-button <?php echo e($i ? 'collapsed' : ''); ?>" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo e($id); ?>-c<?php echo e($i); ?>" aria-expanded="<?php echo e($i ? 'false' : 'true'); ?>" aria-controls="<?php echo e($id); ?>-c<?php echo e($i); ?>"><?php echo e($q); ?></button>
            </h3>
            <div id="<?php echo e($id); ?>-c<?php echo e($i); ?>" class="accordion-collapse collapse <?php echo e($i ? '' : 'show'); ?>" aria-labelledby="<?php echo e($id); ?>-h<?php echo e($i); ?>" data-bs-parent="#<?php echo e($id); ?>">
                <div class="accordion-body"><?php echo e($a); ?></div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php if (! $__env->hasRenderedOnce('d67d660f-331b-4afd-91df-a3ceefe97642')): $__env->markAsRenderedOnce('d67d660f-331b-4afd-91df-a3ceefe97642'); ?>
    <?php $__env->startPush('scripts'); ?><script src="/assets/site/bootstrap/bootstrap.min.js" defer></script><?php $__env->stopPush(); ?>
<?php endif; ?>
<?php /**PATH D:\xampp\htdocs\runwrk\resources\views/marketing/_faq.blade.php ENDPATH**/ ?>
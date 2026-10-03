<?php if (isset($component)) { $__componentOriginalf103f87f9e6975b672a2453f5462c100 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf103f87f9e6975b672a2453f5462c100 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.marketing','data' => ['title' => 'Demo','description' => 'See Runwrk in action. Try the demo app on your own phone.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.marketing'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Demo','description' => 'See Runwrk in action. Try the demo app on your own phone.']); ?>

    <section class="rw-banner">
        <div class="container">
            <h1>Demo</h1>
            <p>See it for yourself.</p>
        </div>
    </section>

    <section class="rw-section">
        <div class="container" style="max-width: 900px">
            <div class="rw-head"><h2>Website demos</h2><p>Coming soon.</p></div>

            <div class="rw-card text-center mt-5">
                <h3>Try the app on your phone</h3>
                <p>Scan this code with your phone, add the demo barber shop app to your home screen, and turn on messages. We can then send you a message.</p>
                <div class="d-inline-block bg-white p-2 border rounded"><?php echo $qr; ?></div>
                <p class="mt-3 mb-0"><a class="more" href="<?php echo e($demoUrl); ?>"><?php echo e($demoUrl); ?></a></p>
            </div>
        </div>
    </section>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf103f87f9e6975b672a2453f5462c100)): ?>
<?php $attributes = $__attributesOriginalf103f87f9e6975b672a2453f5462c100; ?>
<?php unset($__attributesOriginalf103f87f9e6975b672a2453f5462c100); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf103f87f9e6975b672a2453f5462c100)): ?>
<?php $component = $__componentOriginalf103f87f9e6975b672a2453f5462c100; ?>
<?php unset($__componentOriginalf103f87f9e6975b672a2453f5462c100); ?>
<?php endif; ?>
<?php /**PATH D:\xampp\htdocs\runwrk\resources\views/marketing/demo.blade.php ENDPATH**/ ?>
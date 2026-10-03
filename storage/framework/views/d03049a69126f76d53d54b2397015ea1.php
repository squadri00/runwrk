<?php ($w = config('site.website')); ?>
<?php if (isset($component)) { $__componentOriginalf103f87f9e6975b672a2453f5462c100 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf103f87f9e6975b672a2453f5462c100 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.marketing','data' => ['title' => 'Pricing','description' => 'Simple prices. A complete website for $'.e($w['price']).' the first year (regular $'.e($w['original_price']).'), then '.e($w['renewal']).' if you want to keep going. Your own app plans too.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.marketing'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Pricing','description' => 'Simple prices. A complete website for $'.e($w['price']).' the first year (regular $'.e($w['original_price']).'), then '.e($w['renewal']).' if you want to keep going. Your own app plans too.']); ?>

    <section class="rw-banner">
        <div class="container">
            <h1>Pricing</h1>
            <p>Simple prices. No hidden fees.</p>
        </div>
    </section>

    <section class="rw-section">
        <div class="container">
            <div class="rw-head"><h2>Website</h2><p>A complete website for your business, done for you.</p></div>
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="rw-offer">
                        <span class="tag">Limited offer</span>
                        <div class="was">$<?php echo e($w['original_price']); ?></div>
                        <div class="now">$<?php echo e($w['price']); ?></div>
                        <div class="sub">for your first year</div>
                        <ul class="rw-list text-start d-inline-block">
                            <li>Complete website design, 1 or 2 free revisions</li>
                            <li>Free domain name, hosting and SSL</li>
                            <li>Easy back end to edit your own site</li>
                            <li>Search-friendly setup, built to be fast</li>
                            <li>Galleries, accordions, cards, counters and more</li>
                            <li>Contact form, social links and Google Analytics</li>
                            <li>Light and dark mode</li>
                            <li>Complete source code is yours</li>
                            <li>Ready in <?php echo e($w['turnaround']); ?></li>
                        </ul>
                        <div><a class="btn_one d-inline-block" href="<?php echo e(route('contact')); ?>">Get started</a></div>
                        <div class="after"><b>After the first year:</b> optional subscription of <?php echo e($w['renewal']); ?> for hosting, domain, SSL and support.<br>
                            <b>Need help with words?</b> We can write your content for $<?php echo e($w['content_per_page']); ?> a page.</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="rw-section soft rw-plans">
        <div class="container">
            <div class="rw-head"><h2>Your own app</h2><p>Send messages to all your customers' phones. Add it to your website, or use it on its own.</p></div>
            <?php if($plans->isEmpty()): ?>
                <p class="text-center">Plans are coming soon. <a href="<?php echo e(route('contact')); ?>">Contact us</a> and we will set you up.</p>
            <?php else: ?>
                <?php ($cols = [1 => 'col-lg-6', 2 => 'col-lg-6', 3 => 'col-lg-4', 4 => 'col-lg-3'][min($plans->count(), 4)]); ?>
                <div class="row text-center justify-content-center">
                    <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="<?php echo e($cols); ?> col-sm-6">
                            <div class="single-pricing <?php echo e($loop->index === 1 && $plans->count() > 2 ? 'single-pricing-white' : ''); ?>">
                                <div class="price-head"><h2><?php echo e($plan->name); ?></h2><span></span><span></span><span></span><span></span><span></span><span></span></div>
                                <div class="price"><?php echo e($plan->priceLabel()); ?></div>
                                <h5><?php echo e($plan->isFree() ? 'To try it out' : ($plan->interval === 'year' ? 'Yearly' : 'Monthly')); ?></h5>
                                <?php if($plan->description): ?><p class="small-note px-3"><?php echo e($plan->description); ?></p><?php endif; ?>
                                <ul><?php $__currentLoopData = $plan->features ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($f); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
                                <?php if($signups): ?>
                                    <a class="btn_one" href="<?php echo e(route('register', ['plan' => $plan->code])); ?>"><?php echo e($plan->isFree() ? 'Start free' : 'Get started'); ?></a>
                                <?php else: ?>
                                    <a class="btn_one" href="<?php echo e(route('contact')); ?>">Contact us</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="rw-section">
        <div class="container" style="max-width: 820px">
            <div class="rw-head"><h2>Common questions</h2></div>
            <?php echo $__env->make('marketing._faq', ['id' => 'pricefaq', 'items' => [
                ['What does the first-year price include?', 'Your complete website design, a free domain name, hosting, SSL, the easy back end, SEO setup, a contact form, social links, Google Analytics, light and dark mode, and your source code. Nothing else to pay in year one.'],
                ['What do I need to give you?', 'Your business details, the text for your pages, and your photos and logo. If you would rather not write the text, we can do it for $'.$w['content_per_page'].' a page.'],
                ['How many changes can I ask for?', '1 or 2 free revisions while we build it. After that, you can make changes yourself in the back end whenever you like.'],
                ['What happens after year one?', 'You can keep going with an optional subscription of '.$w['renewal'].'. It covers hosting, your domain, SSL and support. You also own your source code.'],
                ['How long does it take?', 'About 1 week once we have your content.'],
                ['Can I get the website and the app?', 'Yes. They work well together. Tell us what you need on the contact page and we will put a package together.'],
                ['Do my customers pay anything for the app?', 'No. It is free for them. They choose to turn messages on and can turn them off any time.'],
            ]], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </section>

    <section class="rw-cta-band">
        <div class="container">
            <h2>Not sure what you need?</h2>
            <p class="mb-4">Tell us about your business and we will point you in the right direction.</p>
            <a class="btn_one" href="<?php echo e(route('contact')); ?>">Contact us</a>
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
<?php /**PATH D:\xampp\htdocs\runwrk\resources\views/marketing/pricing.blade.php ENDPATH**/ ?>
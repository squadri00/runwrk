<?php ($w = config('site.website')); ?>
<?php if (isset($component)) { $__componentOriginalf103f87f9e6975b672a2453f5462c100 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf103f87f9e6975b672a2453f5462c100 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.marketing','data' => ['title' => 'Runwrk: Simple websites and your own app for small businesses','description' => 'A modern website for your small business for $'.e($w['price']).' the first year, ready in about 1 week. Plus your own app to send a message to all your customers\' phones.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.marketing'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Runwrk: Simple websites and your own app for small businesses','description' => 'A modern website for your small business for $'.e($w['price']).' the first year, ready in about 1 week. Plus your own app to send a message to all your customers\' phones.']); ?>

    <section class="rw-hero">
        <div class="rw-orb o1" aria-hidden="true"></div>
        <div class="rw-orb o2" aria-hidden="true"></div>
        <div class="rw-orb o3" aria-hidden="true"></div>
        <div class="rw-dots" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i></div>
        <div class="container">
            <div class="row align-items-center gy-5 gx-lg-5">
                <div class="col-lg-6">
                    <a href="<?php echo e(route('web-design')); ?>" class="rw-pill"><b>OFFER</b> Website, first year $<?php echo e($w['price']); ?> <span aria-hidden="true">&rarr;</span></a>
                    <h1>A website you can <span>edit yourself.</span> An app your customers keep.</h1>
                    <p class="lead">We build fast, good-looking websites for small businesses in <?php echo e($w['turnaround']); ?>. Then we give you your own app, so you can send a message to all your customers' phones, any time you like.</p>
                    <div class="btns">
                        <a class="btn_one" href="<?php echo e(route('pricing')); ?>">See prices</a>
                        <a class="btn_line" href="<?php echo e(route('demo')); ?>">Try the app demo</a>
                    </div>
                    <div class="chips">
                        <span>Free domain, hosting and SSL in year one</span>
                        <span>No hidden fees</span>
                        <span>You own the code</span>
                    </div>
                </div>
                <div class="col-lg-6 rw-hero-art">
                    <?php echo $__env->make('marketing._hero-art', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <div class="rw-float f1" aria-hidden="true"><span class="ti-check"></span> Message sent</div>
                    <div class="rw-float f2" aria-hidden="true"><span class="ti-bell"></span> New customer</div>
                    <div class="rw-float f3" aria-hidden="true"><span class="ti-thumb-up"></span> Site is live</div>
                </div>
            </div>
        </div>
    </section>

    <section class="rw-video-section" id="intro">
        <div class="container" style="max-width: 940px">
            <div class="rw-head">
                <span class="eyebrow">Watch</span>
                <h2>See Runwrk in action</h2>
                <p>A short video that shows what we do and how it works.</p>
            </div>
            <?php if($videoId): ?>
                <div class="rw-video" data-video="<?php echo e($videoId); ?>">
                    <button type="button" class="rw-video-play" aria-label="Play the Runwrk intro video">
                        <span class="ring"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5.5v13l11-6.5z" fill="currentColor"/></svg></span>
                        <span class="label">Watch the intro</span>
                    </button>
                </div>
            <?php else: ?>
                <div class="rw-video empty">
                    <div class="rw-video-play" role="img" aria-label="Intro video coming soon">
                        <span class="ring"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5.5v13l11-6.5z" fill="currentColor"/></svg></span>
                        <span class="label">Intro video coming soon</span>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="rw-section">
        <div class="container">
            <div class="rw-head">
                <span class="eyebrow">What we do</span>
                <h2>Two simple ways to grow your business</h2>
                <p>Get found online, then stay in touch with the customers you already have.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="rw-card">
                        <div class="ico"><span class="ti-layout-grid2"></span></div>
                        <h3>A website for your business</h3>
                        <p>A complete, modern website that looks great on phones and computers. You get a simple back end where you can change your own text, photos and pages. $<?php echo e($w['price']); ?> for the first year, with domain, hosting and SSL included.</p>
                        <a class="more" href="<?php echo e(route('web-design')); ?>">See what is included &rarr;</a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="rw-card">
                        <div class="ico coral"><span class="ti-mobile"></span></div>
                        <h3>Your own app and messages</h3>
                        <p>Your customers add your app to their phone. You type a message and press send, and it shows up on all their screens. Great for offers, reminders and last-minute openings. No app store needed.</p>
                        <a class="more" href="<?php echo e(route('your-app')); ?>">See how it works &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="rw-facts">
        <div class="container">
            <div class="row">
                <div class="col-6 col-lg-3 fact"><div class="num"><span data-count="7">7</span> <small>days</small></div><p>To launch your website</p></div>
                <div class="col-6 col-lg-3 fact"><div class="num">$<span data-count="<?php echo e($w['price']); ?>"><?php echo e($w['price']); ?></span></div><p>Everything in the first year</p></div>
                <div class="col-6 col-lg-3 fact"><div class="num"><span data-count="1">1</span> <small>free</small></div><p>Domain name</p></div>
                <div class="col-6 col-lg-3 fact"><div class="num"><span data-count="100">100</span><small>%</small></div><p>Source code is yours</p></div>
            </div>
        </div>
    </section>

    <section class="rw-section soft">
        <div class="container">
            <div class="rw-head">
                <span class="eyebrow">Built for small business</span>
                <h2>Everything your website needs, nothing it does not</h2>
                <p>Professional from day one, and simple enough to run yourself.</p>
            </div>
            <div class="row g-4">
                <?php $__currentLoopData = [
                    ['ti-pencil-alt', 'Easy to edit', 'Change text, photos and pages yourself. No code, no waiting.'],
                    ['ti-bolt', 'Built to be fast', 'Designed to pass Google\'s speed test, so visitors stay.'],
                    ['ti-mobile', 'Great on every screen', 'Looks right on phones, tablets and computers.'],
                    ['ti-search', 'Ready for Google', 'SEO set up, so people can find your business.'],
                    ['ti-lock', 'Safe and secure', 'Free SSL gives your visitors the padlock they look for.'],
                    ['ti-layout-slider', 'Ready-made sections', 'Galleries, accordions, cards, counters and more, one click away.'],
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$icon, $h, $p]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-6 col-lg-4"><div class="rw-tile"><div class="ico"><span class="<?php echo e($icon); ?>"></span></div><div><h4><?php echo e($h); ?></h4><p><?php echo e($p); ?></p></div></div></div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </section>

    <section class="rw-section">
        <div class="container">
            <div class="row align-items-center gy-5 gx-lg-5">
                <div class="col-lg-6">
                    <div class="rw-head text-start mx-0 mb-3">
                        <span class="eyebrow">Fair prices</span>
                        <h2>Agencies charge thousands. You pay $<?php echo e($w['price']); ?>.</h2>
                    </div>
                    <p>Many web design companies charge thousands of dollars for a simple website, then charge again every time you want a small change. We do it differently.</p>
                    <ul class="rw-list mt-3">
                        <li><b>You send the content, we do the design.</b> Your text, your photos, your logo.</li>
                        <li><b>1 or 2 free revisions</b> so it ends up just right.</li>
                        <li><b>Change things yourself.</b> No waiting, no extra bills for small edits.</li>
                        <li><b>Need help with words?</b> We can write your page content for $<?php echo e($w['content_per_page']); ?> a page.</li>
                    </ul>
                    <a class="btn_one" href="<?php echo e(route('web-design')); ?>">See what is included</a>
                </div>
                <div class="col-lg-6">
                    <div class="rw-offer">
                        <span class="tag">Limited offer</span>
                        <div class="was">$<?php echo e($w['original_price']); ?></div>
                        <div class="now">$<?php echo e($w['price']); ?></div>
                        <div class="sub">for your first year</div>
                        <ul class="rw-list text-start d-inline-block mb-0">
                            <li>Complete website design</li>
                            <li>Free domain name, hosting and SSL</li>
                            <li>Easy back end to edit your site</li>
                            <li>Ready in <?php echo e($w['turnaround']); ?></li>
                        </ul>
                        <div><a class="btn_one mt-3 d-inline-block" href="<?php echo e(route('contact')); ?>">Get started</a></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="rw-section soft">
        <div class="container" style="max-width: 920px">
            <div class="rw-head">
                <span class="eyebrow">Side by side</span>
                <h2>How we compare</h2>
                <p>What you usually get from an agency, and what you get from us.</p>
            </div>
            <div class="table-responsive">
                <table class="rw-compare">
                    <thead><tr><th></th><th>Typical agency</th><th class="us">Runwrk</th></tr></thead>
                    <tbody>
                        <tr><td>First-year price</td><td>Often thousands of dollars</td><td class="us">$<?php echo e($w['price']); ?>, everything included</td></tr>
                        <tr><td>Domain and hosting</td><td>Usually billed separately</td><td class="us">Free in year one</td></tr>
                        <tr><td>Small changes</td><td>Often an extra charge each time</td><td class="us">Do them yourself, any time</td></tr>
                        <tr><td>Your source code</td><td>Often kept by the agency</td><td class="us">Yours to keep</td></tr>
                        <tr><td>Time to launch</td><td>Often weeks or months</td><td class="us"><?php echo e(ucfirst($w['turnaround'])); ?></td></tr>
                        <tr><td>Messages to customers' phones</td><td>Rarely offered</td><td class="us">Available with your own app</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="rw-section">
        <div class="container">
            <div class="rw-head"><span class="eyebrow">How it works</span><h2>From hello to live in three steps</h2><p>You send the content. We do the rest.</p></div>
            <div class="row g-4 rw-steps three">
                <div class="col-md-4 step"><div class="no">1</div><h3>Tell us about your business</h3><p>Send us your business details, photos and the text you want on each page.</p></div>
                <div class="col-md-4 step"><div class="no">2</div><h3>We design it</h3><p>We build your website and send it to you to look at. You get 1 or 2 free revisions.</p></div>
                <div class="col-md-4 step"><div class="no">3</div><h3>You go live</h3><p>In <?php echo e($w['turnaround']); ?> your site is online with your own domain. Log in and change anything, any time.</p></div>
            </div>
        </div>
    </section>

    <section class="rw-section rw-band">
        <div class="container">
            <div class="row align-items-center gy-5 gx-lg-5">
                <div class="col-lg-6 order-lg-2"><?php echo $__env->make('marketing._phone', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div>
                <div class="col-lg-6 order-lg-1">
                    <div class="rw-head text-start mx-0 mb-3"><span class="eyebrow" style="color:#aeb4f0">Your own app</span><h2>No app store needed.</h2></div>
                    <p>Your customers open a link and add your app to their phone. Your logo sits on their home screen like any other app. Then you can send a message to all of them, any time.</p>
                    <ul class="rw-list mt-3">
                        <li><b>Today's special</b> or a new product</li>
                        <li><b>Appointment reminders</b></li>
                        <li><b>Last-minute openings</b> you want to fill fast</li>
                    </ul>
                    <a class="btn_one" href="<?php echo e(route('your-app')); ?>">Learn about the app</a>
                    <?php if($fromPrice): ?><span class="ms-3">From <?php echo e($fromPrice->priceLabel()); ?> a <?php echo e($fromPrice->interval); ?></span><?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <section class="rw-section">
        <div class="container" style="max-width: 820px">
            <div class="rw-head"><span class="eyebrow">Questions</span><h2>Quick answers</h2></div>
            <?php echo $__env->make('marketing._faq', ['items' => [
                ['Do I need to know how to code?', 'No. Your website comes with a simple back end. You log in, click on the text or picture you want to change, and save. If you can use email, you can use it.'],
                ['How long does it take?', 'Usually '.$w['turnaround'].' after we have your content (your text, photos and business details).'],
                ['What happens after the first year?', 'Your first year is $'.$w['price'].'. After that, an optional subscription of '.$w['renewal'].' keeps your hosting, domain, SSL and support going. You also get your complete source code, so you are never locked in.'],
                ['Do my customers have to download anything for the app?', 'No. They open a link, add it to their home screen, and tap one button to turn on messages. No app store.'],
            ], 'id' => 'homefaq'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </section>

    <section class="rw-cta-band">
        <div class="container">
            <h2>Ready to get started?</h2>
            <p class="mb-4">Tell us about your business and we will reply by email.</p>
            <a class="btn_one me-2" href="<?php echo e(route('contact')); ?>">Contact us</a>
            <a class="btn_line" href="<?php echo e(route('pricing')); ?>">See prices</a>
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
<?php /**PATH D:\xampp\htdocs\runwrk\resources\views/marketing/home.blade.php ENDPATH**/ ?>
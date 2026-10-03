<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['title', 'description', 'noindex' => false]));

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

foreach (array_filter((['title', 'description', 'noindex' => false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $canonical = url(request()->path() === '/' ? '/' : '/'.request()->path());
    $fullTitle = request()->routeIs('home') ? $title : $title.' | '.config('app.name');
    $nav = [['web-design', 'Web Design'], ['your-app', 'Your App'], ['pricing', 'Pricing'], ['demo', 'Demo'], ['contact', 'Contact']];
    $parent = config('site.parent');
    $ld = ['@context' => 'https://schema.org', '@type' => 'Organization', 'name' => config('app.name'), 'url' => url('/'), 'parentOrganization' => ['@type' => 'Organization', 'name' => $parent['name'], 'url' => $parent['url']]];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($fullTitle); ?></title>
    <meta name="description" content="<?php echo e($description); ?>">
    <link rel="canonical" href="<?php echo e($canonical); ?>">
    <?php if($noindex): ?><meta name="robots" content="noindex"><?php endif; ?>
    <meta name="theme-color" content="#525fe1">
    <script>(function(){var d=document.documentElement,t='light';try{t=localStorage.getItem('rw-site-theme')||(matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light');}catch(e){}d.setAttribute('data-theme',t);d.setAttribute('data-bs-theme',t);})();</script>
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?php echo e(config('app.name')); ?>">
    <meta property="og:title" content="<?php echo e($fullTitle); ?>">
    <meta property="og:description" content="<?php echo e($description); ?>">
    <meta property="og:url" content="<?php echo e($canonical); ?>">
    <meta name="twitter:card" content="summary">
    <script type="application/ld+json"><?php echo json_encode($ld, JSON_UNESCAPED_SLASHES); ?></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Jost:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/site/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/site/fonts/themify-icons.css">
    <link rel="stylesheet" href="/assets/site/css/style.css">
    <link rel="stylesheet" href="/assets/site/css/runwrk-site.css?v=<?php echo e(filemtime(public_path('assets/site/css/runwrk-site.css'))); ?>">
    <?php echo $__env->yieldPushContent('head'); ?>
</head>
<body class="<?php echo e(request()->routeIs('home') ? '' : 'rw-dark-top'); ?>">
<header class="rw-nav">
    <div class="container rw-nav-in">
        <a class="rw-logo" href="<?php echo e(url('/')); ?>"><span class="rw-mark">R</span><?php echo e(config('app.name')); ?></a>
        <button class="rw-burger" type="button" aria-label="Menu" aria-expanded="false" aria-controls="rw-menu"><span></span><span></span><span></span></button>
        <nav id="rw-menu" class="rw-menu">
            <?php $__currentLoopData = $nav; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$route, $label]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a class="rw-link <?php echo e(request()->routeIs($route) ? 'active' : ''); ?>" href="<?php echo e(route($route)); ?>"><?php echo e($label); ?></a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php if(auth()->guard('web')->check()): ?>
                <a class="rw-signin" href="<?php echo e(route('dashboard')); ?>">My dashboard</a>
            <?php else: ?>
                <a class="rw-signin" href="<?php echo e(route('login')); ?>">Sign in</a>
            <?php endif; ?>
            <a class="btn_one rw-cta" href="<?php echo e(route('contact')); ?>">Get started</a>
            <button class="rw-theme" type="button" aria-label="Switch between light and dark mode" title="Light / dark mode">
                <svg class="sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M6.3 17.7l-1.4 1.4M19.1 4.9l-1.4 1.4"/></svg>
                <svg class="moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/></svg>
            </button>
        </nav>
    </div>
</header>

<main><?php echo e($slot); ?></main>

<footer>
    <div class="footer section-padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-sm-12 rw-foot-brand">
                    <div class="single_footer">
                        <a class="rw-logo" href="<?php echo e(url('/')); ?>"><span class="rw-mark">R</span><?php echo e(config('app.name')); ?></a>
                        <p>Simple websites and your own app for small businesses. Easy to use, fast to launch, fair prices.</p>
                        <div class="rw-parent"><?php echo e(config('app.name')); ?> is a product of <a href="<?php echo e($parent['url']); ?>" rel="noopener"><?php echo e($parent['name']); ?></a>.</div>
                    </div>
                </div>
                <div class="col-lg-2 col-sm-4 col-6">
                    <div class="single_footer">
                        <h4>Services</h4>
                        <ul>
                            <li><a href="<?php echo e(route('web-design')); ?>">Web Design</a></li>
                            <li><a href="<?php echo e(route('your-app')); ?>">Your Own App</a></li>
                            <li><a href="<?php echo e(route('pricing')); ?>">Pricing</a></li>
                            <li><a href="<?php echo e(route('demo')); ?>">Demo</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-2 col-sm-4 col-6">
                    <div class="single_footer">
                        <h4>Company</h4>
                        <ul>
                            <li><a href="<?php echo e(route('contact')); ?>">Contact</a></li>
                            <li><a href="<?php echo e(route('login')); ?>">Sign in</a></li>
                            <li><a href="<?php echo e(route('privacy')); ?>">Privacy</a></li>
                            <li><a href="<?php echo e(route('terms')); ?>">Terms</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-4">
                    <div class="single_footer">
                        <h4>Ready to start?</h4>
                        <p>Tell us about your business. We will reply by email.</p>
                        <a class="btn_one" href="<?php echo e(route('contact')); ?>">Contact us</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="foot_copy">
        <div class="footer_copyright"><p>&copy; <?php echo e(date('Y')); ?> <?php echo e(config('app.name')); ?>, a product of <?php echo e($parent['name']); ?>. All rights reserved.</p></div>
    </div>
</footer>

<button class="rw-top" type="button" aria-label="Back to top" title="Back to top">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
</button>

<script src="/assets/site/js/site.js" defer></script>
<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH D:\xampp\htdocs\runwrk\resources\views/components/layouts/marketing.blade.php ENDPATH**/ ?>
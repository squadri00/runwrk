<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title><?php echo e($business->name); ?></title>
    <link rel="manifest" href="/<?php echo e($business->slug); ?>/manifest.webmanifest">
    <link rel="apple-touch-icon" href="/<?php echo e($business->slug); ?>/icons/apple-touch-180.png">
    <meta name="theme-color" content="<?php echo e($business->theme_color); ?>">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="<?php echo e($business->short_name ?: $business->name); ?>">
    <meta name="robots" content="noindex">
    <style>
        :root { --theme: <?php echo e($business->theme_color); ?>; --on: <?php echo e($onTheme); ?>; --bg: <?php echo e($business->background_color); ?>; }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; font-family: ui-sans-serif, system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif; background: #f1f5f9; color: #0f172a; }
        header { background: var(--theme); color: var(--on); padding: calc(env(safe-area-inset-top) + 32px) 24px 40px; text-align: center; }
        .logo { width: 88px; height: 88px; border-radius: 20px; background: var(--bg); object-fit: contain; padding: 8px; margin: 0 auto 14px; display: block; }
        .initial { width: 88px; height: 88px; border-radius: 20px; background: var(--bg); color: var(--theme); font-size: 40px; font-weight: 700; display: flex; align-items: center; justify-content: center; margin: 0 auto 14px; }
        h1 { margin: 0; font-size: 24px; }
        main { max-width: 480px; margin: -20px auto 0; padding: 0 16px 40px; }
        .card { background: #fff; border-radius: 16px; padding: 18px; margin-bottom: 14px; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
        .card h2 { margin: 0 0 6px; font-size: 16px; }
        .card p { margin: 0 0 12px; font-size: 14px; color: #475569; line-height: 1.45; }
        button, .btn { display: block; width: 100%; padding: 13px; border: 0; border-radius: 12px; font-size: 16px; font-weight: 600; text-align: center; text-decoration: none; cursor: pointer; background: var(--theme); color: var(--on); }
        button.secondary, .btn.secondary { background: #e2e8f0; color: #0f172a; }
        button[disabled] { opacity: .6; }
        .row { display: flex; gap: 10px; } .row > * { flex: 1; }
        ol { padding-left: 20px; margin: 0; font-size: 14px; color: #334155; line-height: 1.7; }
        .note { font-size: 13px; color: #64748b; margin-top: 10px; }
        .ok { color: #047857; font-weight: 600; }
        [hidden] { display: none !important; }
    </style>
</head>
<body>
<header>
    <?php if($logo): ?><img class="logo" src="<?php echo e($logo); ?>" alt=""><?php else: ?><div class="initial"><?php echo e(mb_strtoupper(mb_substr($business->name, 0, 1))); ?></div><?php endif; ?>
    <h1><?php echo e($business->name); ?></h1>
</header>

<main>
    <section class="card" id="notify-card">
        <h2>Get our latest news</h2>
        <p id="notify-text">Turn on notifications to hear about our offers and updates, straight on your phone.</p>
        <button id="notify-on" hidden>Turn on notifications</button>
        <button id="notify-off" class="secondary" hidden>Turn off notifications</button>
        <p class="note" id="notify-note" hidden></p>
    </section>

    <section class="card" id="ios-guide" hidden>
        <h2>Add this app to your iPhone</h2>
        <ol>
            <li>Tap the <strong>Share</strong> button at the bottom of Safari.</li>
            <li>Scroll down and tap <strong>Add to Home Screen</strong>.</li>
            <li>Open <strong><?php echo e($business->short_name ?: $business->name); ?></strong> from your home screen.</li>
            <li>Tap <strong>Turn on notifications</strong>.</li>
        </ol>
    </section>

    <section class="card" id="install-card" hidden>
        <h2>Install our app</h2>
        <p>Keep us one tap away on your home screen.</p>
        <button id="install-btn" hidden>Install app</button>
        <p class="note" id="install-help" hidden>Open your browser menu and choose <strong>Add to Home screen</strong> or <strong>Install app</strong>.</p>
    </section>

    <?php if($business->phone || $mapUrl || $business->website_url): ?>
        <section class="card">
            <div class="row">
                <?php if($business->phone): ?><a class="btn" href="tel:<?php echo e(preg_replace('/[^0-9+]/', '', $business->phone)); ?>">Call</a><?php endif; ?>
                <?php if($mapUrl): ?><a class="btn secondary" href="<?php echo e($mapUrl); ?>" target="_blank" rel="noopener">Directions</a><?php endif; ?>
            </div>
            <?php if($business->website_url): ?><a class="btn secondary" style="margin-top:10px" href="<?php echo e($business->website_url); ?>">Visit our website</a><?php endif; ?>
            <?php if($business->address): ?><p class="note"><?php echo e($business->address); ?></p><?php endif; ?>
        </section>
    <?php endif; ?>
</main>

<script src="/assets/runwrk.js"></script>
<script>
(function () {
    var R = Runwrk.init({ key: <?php echo json_encode($business->public_key, 15, 512) ?>, api: location.origin + '/api/v1', sw: '/<?php echo e($business->slug); ?>/sw.js', scope: '/<?php echo e($business->slug); ?>/', source: 'hosted' });
    var $ = function (id) { return document.getElementById(id); };
    var show = function (id, on) { $(id).hidden = !on; };

    function note(text) { $('notify-note').textContent = text; show('notify-note', !!text); }

    function render() {
        return R.state().then(function (s) {
            show('notify-on', false); show('notify-off', false); show('ios-guide', false); show('install-card', false); note('');

            if (s.ios && !s.standalone) { show('ios-guide', true); $('notify-text').textContent = 'To get notifications on iPhone, add this app to your home screen first.'; return; }
            if (!s.supported) { $('notify-text').textContent = 'Notifications are not available in this browser.'; return; }

            if (s.subscribed) {
                $('notify-text').innerHTML = '<span class="ok">Notifications are on.</span> You will hear about our latest offers.';
                show('notify-off', true);
            } else if (s.permission === 'denied') {
                $('notify-text').textContent = 'Notifications are blocked for this app. Turn them on in your phone or browser settings, then come back.';
            } else {
                show('notify-on', true);
            }

            if (!s.standalone && !s.ios) {
                show('install-card', true);
                show('install-btn', R.canInstall());
                show('install-help', !R.canInstall());
            }
        });
    }

    $('notify-on').onclick = function () {
        var btn = this;
        btn.disabled = true;
        R.subscribe().then(function () { return null; }, function (e) { return e || new Error('failed'); }).then(function (err) {
            btn.disabled = false;
            return render().then(function () {
                if (err) { note(err.message === 'denied' ? 'You chose not to allow notifications.' : 'Something went wrong. Please try again.'); }
            });
        });
    };
    $('notify-off').onclick = function () { R.unsubscribe().then(render); };
    $('install-btn').onclick = function () { R.install().then(render); };
    window.addEventListener('beforeinstallprompt', function () { setTimeout(render, 50); });
    window.addEventListener('appinstalled', render);
    render();
})();
</script>
</body>
</html>
<?php /**PATH D:\xampp\htdocs\runwrk\resources\views/hosted/app.blade.php ENDPATH**/ ?>
/*
 * Runwrk connect: add to any website with one line.
 *   <script src="https://runwrk.com/assets/runwrk-connect.js" data-key="pk_..." async></script>
 * Optional: data-sw, data-manifest, data-position="left|right", data-source="snippet|grav"
 * Shows a small "Get our app" button that lets visitors install the app and turn notifications on or off.
 */
(function () {
    var tag = document.currentScript;
    if (!tag || window.__runwrkConnect) { return; }
    var key = tag.getAttribute('data-key');
    if (!key) { return; }
    window.__runwrkConnect = true;

    var base = new URL(tag.src).origin;
    var api = base + '/api/v1';
    var swUrl = tag.getAttribute('data-sw') || '/runwrk-sw.js';
    var manifestUrl = tag.getAttribute('data-manifest') || '/runwrk-manifest.webmanifest';
    var source = tag.getAttribute('data-source') || 'snippet';
    var position = tag.getAttribute('data-position') === 'left' ? 'left' : 'right';
    var DISMISS = 'runwrk-dismissed-' + key;

    function ready(fn) { if (document.readyState !== 'loading') { fn(); } else { document.addEventListener('DOMContentLoaded', fn); } }
    function idle(fn) { if ('requestIdleCallback' in window) { requestIdleCallback(fn, { timeout: 2500 }); } else { setTimeout(fn, 800); } }
    function dismissedRecently() { try { return Date.now() - parseInt(localStorage.getItem(DISMISS) || '0', 10) < 7 * 864e5; } catch (e) { return false; } }
    function textOn(hex) {
        var m = /^#?([0-9a-f]{6})$/i.exec(hex || ''); if (!m) { return '#fff'; }
        var n = parseInt(m[1], 16), r = n >> 16, g = (n >> 8) & 255, b = n & 255;
        return (r * 299 + g * 587 + b * 114) / 1000 > 150 ? '#111827' : '#ffffff';
    }
    function esc(s) { return String(s).replace(/[&<>"]/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]; }); }

    function head(tagName, attrs) {
        var el = document.createElement(tagName);
        Object.keys(attrs).forEach(function (k) { el.setAttribute(k, attrs[k]); });
        document.head.appendChild(el);
    }

    function api_(path) {
        return fetch(api + path, { headers: { 'X-Runwrk-Key': key } }).then(function (r) { if (!r.ok) { throw new Error('api ' + r.status); } return r.json(); });
    }

    function loadLib(cb) {
        if (window.Runwrk) { cb(); return; }
        var s = document.createElement('script');
        s.src = base + '/assets/runwrk.js'; s.async = true; s.onload = cb;
        document.head.appendChild(s);
    }

    ready(function () {
        // The page must point at a manifest served from this website's own address.
        if (!document.querySelector('link[rel="manifest"]')) { head('link', { rel: 'manifest', href: manifestUrl }); }

        api_('/config').then(function (conf) {
            if (!document.querySelector('link[rel="apple-touch-icon"]')) { head('link', { rel: 'apple-touch-icon', href: conf.icon.replace('icon-192', 'apple-touch-180') }); }
            if (!document.querySelector('meta[name="theme-color"]')) { head('meta', { name: 'theme-color', content: conf.themeColor }); }

            loadLib(function () {
                var R = window.Runwrk.init({ key: key, api: api, sw: swUrl, scope: swUrl.replace(/[^/]*$/, '') || '/', source: source });
                idle(function () { build(R, conf); });
            });
        }).catch(function () { /* wrong key or domain not allowed: stay invisible */ });
    });

    function build(R, conf) {
        if (!R.supported() && !R.isIos()) { return; }

        var theme = conf.themeColor || '#111827', on = textOn(theme);
        var host = document.createElement('div');
        host.id = 'runwrk-connect';
        host.style.cssText = 'all:initial;position:fixed;bottom:18px;' + position + ':18px;z-index:2147483000';
        var root = host.attachShadow({ mode: 'open' });
        root.innerHTML =
            '<style>' +
            ':host{font-family:system-ui,-apple-system,"Segoe UI",Roboto,sans-serif}*{box-sizing:border-box}' +
            '.fab{display:flex;align-items:center;gap:8px;border:0;border-radius:40px;padding:12px 18px;font:600 15px system-ui,sans-serif;cursor:pointer;background:' + theme + ';color:' + on + ';box-shadow:0 10px 26px rgba(0,0,0,.28),0 0 0 1px rgba(255,255,255,.22)}' +
            '.fab.compact{padding:12px;width:48px;height:48px;justify-content:center}.fab.compact span{display:none}' +
            '.fab svg{width:20px;height:20px;flex:none}.fab:focus-visible,button:focus-visible{outline:3px solid #fff;outline-offset:2px;box-shadow:0 0 0 5px ' + theme + '}' +
            '.panel{position:absolute;bottom:62px;' + position + ':0;width:min(340px,calc(100vw - 36px));background:#fff;color:#1f2937;border-radius:16px;box-shadow:0 18px 50px rgba(0,0,0,.3);padding:18px;display:none}' +
            '.panel.open{display:block;animation:in .18s ease-out}@keyframes in{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:none}}' +
            '.top{display:flex;align-items:center;gap:12px;margin-bottom:10px}.top img{width:44px;height:44px;border-radius:10px;background:#f1f5f9}' +
            '.top b{font-size:16px;color:#111827;display:block}.top small{color:#64748b;font-size:13px}' +
            '.x{margin-left:auto;background:none;border:0;font-size:22px;line-height:1;color:#64748b;cursor:pointer;padding:4px 8px}' +
            'p{margin:0 0 12px;font-size:14px;line-height:1.5;color:#374151}ol{margin:0 0 12px;padding-left:20px;font-size:14px;line-height:1.7;color:#374151}' +
            '.btn{display:block;width:100%;border:0;border-radius:10px;padding:12px;font:600 15px system-ui,sans-serif;cursor:pointer;margin-top:8px;background:' + theme + ';color:' + on + '}' +
            '.btn.alt{background:#e5e7eb;color:#111827}.ok{color:#047857;font-weight:600}.note{font-size:12px;color:#6b7280;margin-top:10px}' +
            '@media(prefers-color-scheme:dark){.note{color:#9ca3af}.panel{background:#171b2e;color:#e5e7eb}.top b{color:#f3f4f6}p,ol{color:#cbd5e1}.btn.alt{background:#2a3050;color:#f3f4f6}.top img{background:#2a3050}}' +
            '@media(prefers-reduced-motion:reduce){.panel.open{animation:none}}' +
            '</style>' +
            '<div class="panel" role="dialog" aria-label="' + esc(conf.name) + ' app" id="p"></div>' +
            '<button class="fab" id="f" aria-haspopup="dialog" aria-expanded="false"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9M13.7 21a2 2 0 0 1-3.4 0"/></svg><span id="fl">Get our app</span></button>';
        document.body.appendChild(host);

        var panel = root.getElementById('p'), fab = root.getElementById('f'), label = root.getElementById('fl');

        function render(s) {
            var standalone = s.standalone, html = '';
            html += '<div class="top"><img src="' + esc(conf.icon) + '" alt=""><div><b>' + esc(conf.name) + '</b><small>Our app</small></div><button class="x" id="c" aria-label="Close">&times;</button></div>';

            if (s.ios && !standalone) {
                html += '<p>Add our app to your iPhone, then turn on messages:</p><ol><li>Tap the <b>Share</b> button in Safari.</li><li>Tap <b>Add to Home Screen</b>.</li><li>Open the app from your home screen.</li><li>Tap <b>Turn on notifications</b>.</li></ol>';
            } else if (s.subscribed) {
                html += '<p class="ok">Notifications are on.</p><p>You will hear about our latest news and offers.</p><button class="btn alt" id="off">Turn off notifications</button>';
            } else if (s.permission === 'denied') {
                html += '<p>Notifications are blocked for this site. Turn them on in your browser settings, then come back.</p>';
            } else {
                html += '<p>Get our latest news and offers straight on your phone.</p><button class="btn" id="on">Turn on notifications</button>';
            }

            if (!standalone && !s.ios) {
                html += R.canInstall() ? '<button class="btn alt" id="inst">Install our app</button>' : '<p class="note">To install: open your browser menu and choose "Add to Home screen" or "Install app".</p>';
            }
            html += '<p class="note" id="msg" hidden></p>';
            panel.innerHTML = html;

            var $ = function (id) { return panel.querySelector('#' + id); };
            if ($('c')) { $('c').onclick = function () { setOpen(false); try { localStorage.setItem(DISMISS, String(Date.now())); } catch (e) {} refresh(); }; }
            if ($('on')) { $('on').onclick = function () { var b = this; b.disabled = true; R.subscribe().then(null, function (e) { return e; }).then(function (err) { return refresh().then(function () { if (err) { var m = panel.querySelector('#msg'); if (m) { m.textContent = err.message === 'denied' ? 'You chose not to allow notifications.' : 'Something went wrong. Please try again. (' + (err.step || '?') + ': ' + String(err.name || '') + ' ' + String(err.message || '').slice(0, 110) + ')'; m.hidden = false; } } }); }); }; }
            if ($('off')) { $('off').onclick = function () { R.unsubscribe().then(refresh); }; }
            if ($('inst')) { $('inst').onclick = function () { R.install().then(refresh); }; }
        }

        function setOpen(open) { panel.classList.toggle('open', open); fab.setAttribute('aria-expanded', open); }

        function refresh() {
            return R.state().then(function (s) {
                render(s);
                var hide = s.standalone && s.subscribed;
                host.style.display = hide ? 'none' : '';
                label.textContent = s.standalone && !s.subscribed ? 'Turn on notifications' : 'Get our app';
                fab.classList.toggle('compact', dismissedRecently() && !panel.classList.contains('open'));
            });
        }

        fab.onclick = function () { var open = !panel.classList.contains('open'); setOpen(open); if (open) { fab.classList.remove('compact'); } };
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && panel.classList.contains('open')) { setOpen(false); fab.focus(); } });
        window.addEventListener('beforeinstallprompt', function () { setTimeout(refresh, 60); });
        window.addEventListener('appinstalled', refresh);
        refresh();
    }
})();

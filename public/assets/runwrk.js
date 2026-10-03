/* Runwrk browser library: install prompt, notification opt-in/out. Used by hosted apps and connected websites. */
(function (w) {
    var cfg = {};
    var deferredPrompt = null;

    w.addEventListener('beforeinstallprompt', function (e) { e.preventDefault(); deferredPrompt = e; });

    function api(path, body, method) {
        return fetch(cfg.api + path, {
            method: method || 'POST',
            headers: { 'Content-Type': 'application/json', 'X-Runwrk-Key': cfg.key },
            body: body ? JSON.stringify(body) : undefined
        }).then(function (r) {
            if (!r.ok) { throw new Error('api ' + r.status); }
            return r.json();
        });
    }

    function b64ToBytes(b64) {
        var pad = '='.repeat((4 - b64.length % 4) % 4);
        var raw = atob((b64 + pad).replace(/-/g, '+').replace(/_/g, '/'));
        var out = new Uint8Array(raw.length);
        for (var i = 0; i < raw.length; i++) { out[i] = raw.charCodeAt(i); }
        return out;
    }

    function registration() {
        return navigator.serviceWorker.register(cfg.sw, { scope: cfg.scope }).then(function () {
            return navigator.serviceWorker.ready;
        });
    }

    function payload(sub) {
        var j = sub.toJSON();
        var enc = (w.PushManager && PushManager.supportedContentEncodings && PushManager.supportedContentEncodings[0]) || 'aes128gcm';
        return { endpoint: j.endpoint, keys: j.keys, contentEncoding: enc, source: cfg.source || 'hosted' };
    }

    var Runwrk = {
        init: function (c) {
            cfg = c;
            if (cfg.sw && 'serviceWorker' in navigator) {
                registration().then(function (reg) {
                    if (Notification.permission !== 'granted') { return; }
                    reg.pushManager.getSubscription().then(function (sub) {
                        var k = 'rw-sync-' + cfg.key;
                        if (sub && !sessionStorage.getItem(k)) {
                            sessionStorage.setItem(k, '1');
                            api('/subscribe', payload(sub)).catch(function () {});
                        }
                    });
                }).catch(function () {});
            }
            return Runwrk;
        },
        isIos: function () { return /iphone|ipad|ipod/i.test(navigator.userAgent) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1); },
        isStandalone: function () { return w.matchMedia('(display-mode: standalone)').matches || navigator.standalone === true; },
        supported: function () { return 'serviceWorker' in navigator && 'PushManager' in w && 'Notification' in w; },
        canInstall: function () { return !!deferredPrompt; },
        install: function () {
            if (!deferredPrompt) { return Promise.resolve(false); }
            deferredPrompt.prompt();
            return deferredPrompt.userChoice.then(function (c) { deferredPrompt = null; return c.outcome === 'accepted'; });
        },
        state: function () {
            var s = { supported: Runwrk.supported(), ios: Runwrk.isIos(), standalone: Runwrk.isStandalone(), permission: 'Notification' in w ? Notification.permission : 'denied', subscribed: false };
            if (!s.supported) { return Promise.resolve(s); }
            return navigator.serviceWorker.getRegistration(cfg.scope).then(function (reg) {
                return reg ? reg.pushManager.getSubscription() : null;
            }).then(function (sub) { s.subscribed = !!sub && s.permission === 'granted'; return s; });
        },
        subscribe: function () {
            return registration().then(function (reg) {
                return Notification.requestPermission().then(function (perm) {
                    if (perm !== 'granted') { throw new Error('denied'); }
                    return api('/config', null, 'GET').then(function (conf) {
                        return reg.pushManager.subscribe({ userVisibleOnly: true, applicationServerKey: b64ToBytes(conf.vapidPublicKey) });
                    });
                });
            }).then(function (sub) { return api('/subscribe', payload(sub)); });
        },
        unsubscribe: function () {
            return navigator.serviceWorker.getRegistration(cfg.scope).then(function (reg) {
                return reg ? reg.pushManager.getSubscription() : null;
            }).then(function (sub) {
                if (!sub) { return true; }
                var endpoint = sub.endpoint;
                return sub.unsubscribe().then(function () { return api('/unsubscribe', { endpoint: endpoint }); });
            });
        }
    };

    w.Runwrk = Runwrk;
})(window);

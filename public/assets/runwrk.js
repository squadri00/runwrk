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

    // Wait until OUR worker (the one just registered for cfg.scope) is active. navigator.serviceWorker.ready would
    // return the website's own worker when it controls the page, and notifications would then go to the wrong place.
    function whenActive(reg) {
        if (reg.active) { return Promise.resolve(reg); }
        var sw = reg.installing || reg.waiting;
        if (!sw) { return Promise.reject(new Error('no worker')); }
        return new Promise(function (resolve, reject) {
            sw.addEventListener('statechange', function () {
                if (sw.state === 'activated') { resolve(reg); }
                if (sw.state === 'redundant') { reject(new Error('worker failed to install')); }
            });
        });
    }

    function registration() {
        return navigator.serviceWorker.register(cfg.sw, { scope: cfg.scope }).then(whenActive);
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
            var step = 'service-worker';
            return registration().then(function (reg) {
                step = 'permission';
                return Notification.requestPermission().then(function (perm) {
                    if (perm !== 'granted') { throw new Error('denied'); }
                    step = 'settings';
                    return api('/config', null, 'GET').then(function (conf) {
                        step = 'push-service';
                        return reg.pushManager.subscribe({ userVisibleOnly: true, applicationServerKey: b64ToBytes(conf.vapidPublicKey) });
                    });
                });
            }).then(function (sub) {
                step = 'save';
                return api('/subscribe', payload(sub));
            }).catch(function (e) {
                e.step = step;
                if (window.console) { console.error('Runwrk: turning on notifications failed at step ' + step, e); }
                throw e;
            });
        },
        // ---- test tools (help a customer find out why a notification does not show) ----
        onReceived: function (cb) {
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.addEventListener('message', function (e) { if (e.data && e.data.type === 'runwrk-received') { cb(e.data); } });
            }
        },
        // A notification created right here on the device, without any push message. Tells you if the phone can show notifications at all.
        localTest: function () {
            return navigator.serviceWorker.getRegistration(cfg.scope).then(function (reg) {
                if (!reg) { throw new Error('no worker'); }
                return reg.showNotification('Local test', { body: 'This notification was made on your phone, with no message from the internet. If you can see it, your phone can show notifications.', tag: 'runwrk-local-test' });
            });
        },
        // A real push message sent to just this device.
        serverTest: function () {
            return navigator.serviceWorker.getRegistration(cfg.scope).then(function (reg) {
                return reg ? reg.pushManager.getSubscription() : null;
            }).then(function (sub) {
                if (!sub) { throw new Error('not subscribed'); }
                return api('/test', { endpoint: sub.endpoint });
            });
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

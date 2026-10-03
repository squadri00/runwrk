/* Runwrk service worker core. Hosted apps and customer sites load this with importScripts(). */
self.addEventListener('install', function () { self.skipWaiting(); });
self.addEventListener('activate', function (e) { e.waitUntil(self.clients.claim()); });

self.addEventListener('push', function (event) {
    var data = {};
    try { data = event.data ? event.data.json() : {}; } catch (e) { data = { title: 'New message', body: event.data ? event.data.text() : '' }; }

    var options = {
        body: data.body || '',
        icon: data.icon,
        badge: data.badge,
        image: data.image,
        tag: data.tag,
        data: { url: data.url, msg: data.msg, api: data.api, key: data.key }
    };

    event.waitUntil(self.registration.showNotification(data.title || 'New message', options));
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();
    var d = event.notification.data || {};
    var target = d.url ? new URL(d.url, self.location.origin).href : self.location.origin + '/';

    var track = Promise.resolve();
    if (d.api && d.key && d.msg) {
        track = fetch(d.api + '/click', {
            method: 'POST',
            keepalive: true,
            headers: { 'Content-Type': 'application/json', 'X-Runwrk-Key': d.key },
            body: JSON.stringify({ message: d.msg })
        }).catch(function () {});
    }

    var open = self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (list) {
        for (var i = 0; i < list.length; i++) {
            if (list[i].url === target && 'focus' in list[i]) { return list[i].focus(); }
        }
        return self.clients.openWindow ? self.clients.openWindow(target) : null;
    });

    event.waitUntil(Promise.all([track, open]));
});

/* Offline page: only for page navigations, and only when the network fails. Everything else passes straight through. */
var OFFLINE_HTML = '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>You are offline</title>'
    + '<style>body{margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;font-family:system-ui,-apple-system,Segoe UI,Roboto,sans-serif;background:#f1f5f9;color:#0f172a;text-align:center;padding:24px}'
    + '@media(prefers-color-scheme:dark){body{background:#0b1020;color:#f2f4ff}}'
    + 'main{max-width:340px}h1{font-size:22px;margin:0 0 8px}p{margin:0 0 20px;color:#64748b;line-height:1.5}button{font:inherit;font-weight:600;padding:12px 24px;border:0;border-radius:10px;background:#525fe1;color:#fff;cursor:pointer}'
    + '.i{font-size:42px;margin-bottom:8px}</style></head><body><main><div class="i">&#128246;</div><h1>You are offline</h1><p>Check your internet connection, then try again.</p>'
    + '<button onclick="location.reload()">Try again</button></main></body></html>';

self.addEventListener('fetch', function (event) {
    if (event.request.mode !== 'navigate') { return; }
    event.respondWith(fetch(event.request).catch(function () {
        return new Response(OFFLINE_HTML, { status: 503, headers: { 'Content-Type': 'text/html; charset=utf-8' } });
    }));
});

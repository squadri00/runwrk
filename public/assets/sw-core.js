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

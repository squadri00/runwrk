// Loads the real sw-core.js in a fake worker scope and exercises the fetch / push / click handlers.
const fs = require('fs'), vm = require('vm');
const handlers = {}, shown = [], fetched = [], opened = [], pageMessages = [];
let failShow = false;
const fakePage = { postMessage: (m) => pageMessages.push(m) };
const self = {
    addEventListener: (t, fn) => { handlers[t] = fn; },
    skipWaiting() {}, clients: { claim() {}, matchAll: async () => [fakePage], openWindow: async (u) => { opened.push(u); } },
    registration: { showNotification: async (t, o) => { if (failShow) { throw new Error('notifications are blocked by the device'); } shown.push({ t, o }); } },
    location: { origin: 'https://joes.com' },
};
let online = true;
const sandbox = { self, URL, Response, Promise, JSON, fetch: async (req, opts) => { fetched.push({ req, opts }); if (!online) { throw new TypeError('network down'); } return new Response('ok', { status: 200 }); } };
vm.runInNewContext(fs.readFileSync(process.argv[2], 'utf8'), sandbox);

(async () => {
    const out = {};
    // fetch handler
    const run = async (mode) => { let p = null; handlers.fetch({ request: { mode, url: 'https://joes.com/x' }, respondWith: (x) => { p = x; } }); return p; };
    out.nonNavigateIgnored = (await run('no-cors')) === null;
    online = true; const ok = await run('navigate'); out.onlineNavigate = ok.status;
    online = false; const off = await run('navigate'); out.offlineStatus = off.status; out.offlineHtml = (await off.text()).includes('You are offline'); out.offlineContentType = off.headers.get('Content-Type');

    // a real message arrives: shown, confirmed to Runwrk, pages told
    online = true; let waited = null;
    handlers.push({ data: { json: () => ({ title: 'Flash sale', body: 'Half price', icon: 'i.png', url: '/sale', msg: 7, api: 'https://runwrk.com/api/v1', key: 'pk_x', tag: 'm7' }) }, waitUntil: (p) => { waited = p; } });
    await waited; out.pushShown = shown[0] && { title: shown[0].t, body: shown[0].o.body, tag: shown[0].o.tag, url: shown[0].o.data.url };
    const rec = fetched.find((f) => String(f.req).endsWith('/received'));
    out.receipt = { sent: !!rec, body: rec && rec.opts.body, keepalive: rec && rec.opts.keepalive, hasKey: rec && rec.opts.headers['X-Runwrk-Key'] === 'pk_x' };
    out.pageTold = pageMessages[0];

    // a test message (no message id): shown and the page told, but nothing counted on the server
    fetched.length = 0; pageMessages.length = 0;
    handlers.push({ data: { json: () => ({ title: 'Test', body: 'Hello', test: true, api: 'https://runwrk.com/api/v1', key: 'pk_x' }) }, waitUntil: (p) => { waited = p; } });
    await waited;
    out.testPush = { pageTold: pageMessages[0], countedOnServer: fetched.some((f) => String(f.req).endsWith('/received')) };

    // the device refuses to show it: the reason is reported
    fetched.length = 0; failShow = true;
    handlers.push({ data: { json: () => ({ title: 'X', body: 'Y', msg: 8, api: 'https://runwrk.com/api/v1', key: 'pk_x' }) }, waitUntil: (p) => { waited = p; } });
    await waited; failShow = false;
    const err = fetched.find((f) => String(f.req).endsWith('/received'));
    out.displayFailureReported = err && JSON.parse(err.opts.body);

    // broken payload must still show something
    const before = shown.length;
    handlers.push({ data: { json: () => { throw new Error('bad'); }, text: () => 'plain text' }, waitUntil: (p) => { waited = p; } });
    await waited; out.badPayloadStillNotifies = shown.length === before + 1;

    // click handler: opens link, reports the click
    let closed = false; waited = null; fetched.length = 0;
    handlers.notificationclick({ notification: { close: () => { closed = true; }, data: { url: '/sale', msg: 7, api: 'https://runwrk.com/api/v1', key: 'pk_x' } }, waitUntil: (p) => { waited = p; } });
    await waited; const click = fetched.find((f) => String(f.req).includes('/click'));
    out.click = { closed, openedUrl: opened[0], reported: !!click, body: click && click.opts.body, keepalive: click && click.opts.keepalive, hasKeyHeader: click && click.opts.headers['X-Runwrk-Key'] === 'pk_x' };
    console.log(JSON.stringify(out, null, 1));
})();

// Runs the real public/assets/runwrk.js in a fake browser where the website ALREADY has its own service worker
// (navigator.serviceWorker.ready returns that one). Runwrk must subscribe through its OWN registration.
const fs = require('fs'), vm = require('vm');

const calls = { subscribed: [], fetches: [], registered: [] };
const makeReg = (name, scope, active) => ({
    name, scope, active: active ? { state: 'activated' } : null, installing: null, waiting: null,
    pushManager: {
        getSubscription: async () => null,
        subscribe: async (opts) => { calls.subscribed.push(name); return { endpoint: 'https://fcm.googleapis.com/x/' + name, toJSON: () => ({ endpoint: 'https://fcm.googleapis.com/x/' + name, keys: { p256dh: 'p'.repeat(87), auth: 'a'.repeat(22) } }), unsubscribe: async () => true }; },
    },
});

const theirs = makeReg('their-worker', '/', true);              // the website's own worker, controls the page
const mode = process.argv[3] || 'active-now';                    // 'active-now' or 'installing-first'
const ours = makeReg('runwrk-worker', '/runwrk-app/', mode === 'active-now');
if (mode === 'installing-first') {
    const listeners = [];
    ours.installing = { state: 'installing', addEventListener: (t, fn) => listeners.push(fn) };
    setTimeout(() => { ours.installing.state = 'activated'; ours.active = ours.installing; listeners.forEach((fn) => fn()); }, 20);
}

const windowObj = {
    addEventListener() {}, PushManager: function () {}, Notification: { permission: 'default', requestPermission: async () => 'granted' },
    matchMedia: () => ({ matches: false }),
};
windowObj.Notification.permission = 'granted';
const navigator = {
    userAgent: 'Mozilla/5.0 (Windows NT 10.0) Chrome/120', platform: 'Win32', maxTouchPoints: 0,
    serviceWorker: {
        register: async (url, opts) => { calls.registered.push({ url, scope: opts.scope }); return ours; },
        ready: Promise.resolve(theirs),
        getRegistration: async (scope) => (scope && scope.startsWith('/runwrk-app') ? ours : theirs),
    },
};
windowObj.PushManager.supportedContentEncodings = ['aes128gcm'];

const sandbox = {
    window: windowObj, navigator, Notification: windowObj.Notification, PushManager: windowObj.PushManager, sessionStorage: { getItem: () => '1', setItem() {} },
    Uint8Array, atob: (s) => Buffer.from(s, 'base64').toString('binary'), Promise, JSON, setTimeout,
    fetch: async (url, opts) => {
        calls.fetches.push({ url, method: (opts && opts.method) || 'GET' });
        return { ok: true, json: async () => ({ ok: true, vapidPublicKey: 'BEvK-DLGN6iwfvUPbtKl3Qb5fqnU3p5XfyFPeSFiSCxfRNhmvCeysm_ReGSLp9QY_-4uf8nCjqUsxWlZLg-hYOY' }) };
    },
};
vm.runInNewContext(fs.readFileSync(process.argv[2], 'utf8'), sandbox);

(async () => {
    const R = windowObj.Runwrk.init({ key: 'pk_test', api: 'https://runwrk.test/api/v1', sw: '/runwrk-app/runwrk-sw.js', scope: '/runwrk-app/', source: 'snippet' });
    await new Promise((r) => setTimeout(r, 60));
    calls.subscribed.length = 0;
    await R.subscribe();
    const state = await R.state();
    console.log(JSON.stringify({
        mode,
        registeredScope: calls.registered[0] && calls.registered[0].scope,
        subscribedThrough: calls.subscribed,
        savedToRunwrk: calls.fetches.some((f) => f.url.endsWith('/subscribe') && f.method === 'POST'),
        stateSubscribed: state.subscribed,
    }));
})().catch((e) => { console.log(JSON.stringify({ error: String(e) })); process.exit(1); });

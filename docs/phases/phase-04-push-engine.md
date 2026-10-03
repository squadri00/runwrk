# Phase 04 — Push engine and hosted app

Status: **complete**

## Goal
A customer opens the business's app page, installs it, turns on notifications, and the owner sends them a message from the dashboard.

## Answers assumed (you answered "go ahead")
Simple branded app page; message = title, message, link, optional picture, now or scheduled; "Turn off notifications" button inside the app; VAPID keys generated now with backup command and doc; Cloudflare tunnel installed.

## What was built
- **VAPID**: `runwrk:vapid-generate` (writes `.env`, refuses to overwrite), `runwrk:vapid-backup` (saves a copy plus `APP_KEY` under `storage/backups`, git-ignored), `docs/VAPID-KEYS.md`.
- **Public API** (key + allowed domains, per-business CORS): `GET config`, `POST subscribe`, `POST unsubscribe`, `POST click`. The platform's own address is always allowed so hosted apps can call it.
- **Hosted app** at `/{slug}`: branded page (logo, colours, Call, Directions, website), `manifest.webmanifest` (scope `/{slug}/`, 192/512/maskable icons from Phase 3, default icons when no logo), `sw.js`, icon route. Install button on Android/desktop, step-by-step guide on iPhone, notification on/off, denied-state help.
- **Shared browser files**: `public/assets/runwrk.js` (install, subscribe, unsubscribe; will also power the Phase 5 snippet) and `public/assets/sw-core.js` (shows notifications, opens the link on tap, reports the tap). Customer sites will only need a one-line worker that loads the shared core.
- **Sending**: dashboard → Notifications: list, new message (live preview, picture, send now or schedule in the business timezone), message page with progress and counts (audience, delivered, opened, not delivered), cancel. Staff can send too. Dashboard shows subscribers, messages sent, opens (30 days). Subscribers page shows counts by platform/source and latest sign-ups, no personal data.
- **Engine**: message row holds a cursor and counters, so sending is resumable and works from the per-minute cron, right after a "send now", or later from a worker. Batches of 100 are sent in parallel with Guzzle. Audience is frozen when sending starts. Expired subscriptions (404/410) are deleted; other failures count and are dropped after 5 in a row. `runwrk:push-dispatch` (scheduled every minute) and `runwrk:push-run` (send now, for local testing).
- **Security**: subscribe only accepts https endpoints on real push-service domains (we POST to them, so this blocks SSRF); tenant isolation tests for subscriptions, messages, sending, clicks and views; private VAPID key never leaves the server.

## Decisions and why
| Decision | Why |
|---|---|
| Concurrency via a small `ConcurrentWebPush` subclass + Guzzle 8 | Laravel 13 uses Guzzle 8, which the library's async adapter doesn't support. Verified locally: signed, encrypted, 3 parallel sends, 410 → expired, 500 → failure |
| "Send now" runs after the response, not via the queue | Instant for demos on shared hosting; the cron picks up anything left |
| Cursor on the message instead of a row per delivery | Small tables, resumable, same code on a VPS worker |
| Click tracking is a public endpoint with the business key | Anyone with the public key can inflate "opened". Acceptable, noted |
| No offline caching yet | Phase 5 (connectors) |
| Windows needs `OPENSSL_CONF` | XAMPP PHP can't create EC keys without it; `dev-serve.cmd` and the phone script set it, plus a one-time `setx` for your own terminals. Not needed on Hostinger |

## Files and tables
Tables: `push_subscriptions`, `push_messages`. Code: `app/Domain/Push/*`, `app/Jobs/SendPushBatch.php`, `app/Console/Commands/{VapidGenerate,VapidBackup,PushDispatch,PushRun}.php`,
`app/Http/Controllers/{Api/PushApiController,HostedAppController,App/NotificationController,App/SubscriberController}.php`, `resources/views/{hosted,app/notifications}`,
`public/assets/{runwrk.js,sw-core.js,icons/}`, `scripts/dev-phone.ps1`, `dev-serve.cmd`. Package: `minishlink/web-push`.

## How to test
```bash
php artisan test    # 103 tests (41 new for push: engine, API, UI, hosted app, isolation)
```
Phone test: follow `docs/PHONE-TESTING.md` (`.\scripts\dev-phone.ps1`, then open `<address>/demo-barber` on the phone).

## Verified
Automated tests with a fake transport; real encryption/VAPID/HTTP path against a local receiver; hosted page, manifest, icons, service worker registration and API over the real https tunnel in a desktop browser. **Not yet verified:** actual delivery to a phone through Google's push service, install flow on Android, iPhone behaviour.

## Known issues
- Delivery to a real phone is untested until you try it.
- Tunnel address changes every run; subscriptions made on one address belong to that address' origin, so re-subscribe after restarting.
- `Remember me`/HTTPS cookies and `SESSION_SECURE_COOKIE` need attention at deploy.
- Scheduled messages only go out when the cron (or `runwrk:push-run`) runs.
- No per-plan limit on subscribers or messages yet.
- Legal check (CASL/privacy) still needed before marketing copy.

## Open questions
- Did the phone test work? Tell me what you see (Android model, Chrome version if something fails).

## Next: Phase 5 — Connectors
`runwrk.js` snippet + one-line service worker file for any website (hoshmint.com first), Grav plugin, install guides, offline caching, cross-origin manifest check, test against a local copy of hoshmint.

# Runwrk — Architecture (v1)

Runwrk sells small businesses "your own app, and a message to all your customers' phones anytime".
Technically: a branded installable web app with web push, run from one central Laravel platform.

Customer-facing copy never says "PWA".

---

## 1. Shape of the system

```
                         runwrk.com  (one Laravel 13 app, Hostinger shared hosting)
 ┌────────────────────────────────────────────────────────────────────────────────┐
 │  /                marketing site, /pricing, signup                             │
 │  /login           business owner + staff login                                 │
 │  /dashboard/*     owner admin (push, subscribers, branding, staff, modules)    │
 │  /admin/*         super admin (businesses, plans, keys, domains, impersonate)  │
 │  /api/v1/*        public API used by the app layer (key + allowed domains)     │
 │  /assets/*        runwrk.js snippet, sw-core.js, icons                         │
 │  /{slug}          hosted app for a business (zero-setup option)                │
 │                                                                                │
 │  MySQL (business_id on every tenant table)   database queue   cron every min   │
 └────────────────────────────────────────────────────────────────────────────────┘
          ▲ subscribe / manifest / click                     │ Web Push (VAPID)
          │                                                  ▼
 Customer's phone ── installed app ◄── push service (Google FCM / Apple / Mozilla)
          │
          └── served from ONE of:
                A. runwrk.com/{slug}         hosted app, no website work needed
                B. a Grav site               Runwrk PWA plugin
                C. any other site            runwrk.js snippet + one uploaded sw file
```

One codebase, one database, one VAPID key pair for the whole platform.

## 2. Hosting and deployment (matches Chantley / Quotaire)

- Hostinger shared hosting, PHP 8.4, SSH, Composer at `/usr/local/bin/composer`, no Node on the server.
- App code at `~/domains/runwrk.com/runwrk`, with `public_html` replaced by a symlink to `runwrk/public`
  (the method chantley.com runs on). Fallback: the `index.php` shim in Chantley's `HOSTINGER-DEPLOY.md`.
  This answers the "public folder as web root" question: **no root `.htaccess` redirect**. That
  approach risks exposing `.env` if it's misconfigured.
- Deploys: `npm run build` locally → commit `public/build` → `git push` → SSH → `bash deploy.sh`.
- Cron (every minute): `schedule:run`, and `queue:work --stop-when-empty --max-time=55`.
  On a VPS later, swap the second cron for a supervised `queue:work`. No code change.

## 3. Stack

| Concern | Choice |
|---|---|
| Framework | Laravel 13, PHP 8.4 (local 8.4.25, Hostinger 8.4) |
| DB | MariaDB/MySQL, database `runwrkdb` locally |
| UI | Blade + Tailwind 4 (Vite, built locally) + Livewire only where interactive |
| Queue / cache / sessions | `database` driver |
| Push | `minishlink/web-push` (bcmath is enough, gmp optional) |
| Payments | Laravel Cashier (Stripe) in the billing phase |
| Tests | PHPUnit, plus `runwrk:qa-run` smoke command (Chantley pattern) |

## 4. Multi-tenancy

Same proven design as Chantley, renamed to the business vocabulary:

- One database. Every tenant-owned row has `business_id`, indexed (with `business_id, created_at` on big tables).
- `App\Domain\Tenancy\BelongsToBusiness` trait adds a `BusinessScope` global scope and auto-fills `business_id`.
- `CurrentBusiness` singleton is set by:
  - `ResolveBusiness` middleware for `/dashboard` (from the logged-in user),
  - the API pipeline for `/api/v1` (from the public key + Origin check),
  - the slug route for `/{slug}`,
  - `CurrentBusiness::run($business, fn () => ...)` in jobs, commands, seeders.
- If no business is resolved, tenant queries return **nothing**. They never fail open.
- Super admin crosses tenants only via explicit `withoutBusinessScope()`.
- Guards: `web` (business users: owner / staff), `superadmin` (separate table, `/admin`),
  `salesperson` added in the sales phase.
- Impersonation with a persistent banner, audit-logged (`impersonated_by`).
- Tests prove that business A can never read, count or push to business B's subscribers.

## 5. The app layer: three ways to connect a business

| | A. Hosted app | B. Grav plugin | C. Snippet |
|---|---|---|---|
| URL customers install | `runwrk.com/{slug}` | the business's own site | the business's own site |
| Work on the website | none (optional "Get our app" link) | install plugin, paste key | paste 1 script tag + upload 1 file |
| Service worker | `runwrk.com/{slug}/sw.js`, scope `/{slug}/` | served by the plugin at site root | `runwrk-sw.js` uploaded to site root |
| Works on Wix / Squarespace | yes | n/a | no (can't upload the file) |
| Use for | demos, any business, fallback | Grav clients | hoshmint.com, static / WordPress sites |

- The service worker on customer sites is a 1-line file: `importScripts('https://runwrk.com/assets/sw-core.js')`.
  All push, click and caching logic lives centrally, so we update every site by deploying Runwrk only.
- Manifest and icons are generated per business by Core (`/api/v1/manifest/{key}`), with absolute
  `start_url`/`scope` on the business's own allowed domain. Phase 5 verifies cross-origin manifest
  installability on Android Chrome and iOS Safari. If it fails, the plugin and snippet serve the manifest locally.
- **The sales demo uses option A.** The salesperson opens `runwrk.com/demo-barber` on the owner's phone,
  installs it, and sends a push from `/dashboard` on the spot. No dependency on the prospect's website.
- iOS: push works only after "Add to Home Screen" (iOS 16.4+). The install prompt detects iOS
  and shows a step-by-step guide before asking for notification permission.
- On `runwrk.com`, browser permission is per origin. Every business's hosted app shares it, so we
  subscribe only on an explicit tap inside that business's app, never silently.

## 6. Push engine

- One platform VAPID key pair in `.env` (`VAPID_PUBLIC_KEY`, `VAPID_PRIVATE_KEY`, `VAPID_SUBJECT`).
  `php artisan runwrk:vapid-generate` (refuses to overwrite) and `php artisan runwrk:vapid-backup`.
  `docs/VAPID-KEYS.md` explains why losing the key means losing every subscriber.
- Subscribe: `POST /api/v1/subscribe` with the public key. Origin must match the business's allowed domains
  (or be the hosted app). Rate limited. Store endpoint + keys + platform + source.
- Send: the owner writes title, message, link, optional image, now or scheduled → `push_messages` row →
  the scheduler fans out `SendPushBatch` jobs (about 100 subscriptions each, flushed concurrently by web-push)
  → the queue cron processes as many batches as fit in 55 s.
- Results: success/failure/expired counts on the message. Subscriptions are deleted on 404/410.
  Clicks are tracked via the service worker → `POST /api/v1/click`.
- Unsubscribe: an in-app "Turn off notifications" button → deletes the subscription. Every notification
  links back into the app where that button lives.
- Opt-in only: the browser permission prompt is the consent record. We store `subscribed_at` + origin + user agent.

## 7. Database outline

Platform (Phase 1–3)

| Table | Key columns |
|---|---|
| `businesses` | name, slug (unique, not reserved), status (trial/active/suspended/cancelled), plan_id, public_key (`pk_…`, unique), short_name, logo_path, icon_path, theme_color, background_color, phone, address, website_url, hours (json), timezone, salesperson_id (later) |
| `business_domains` | business_id, domain (normalised host, unique per business) |
| `users` | business_id, name, email (unique), password, role (owner/staff), two_factor_secret, two_factor_confirmed_at, last_login_at |
| `superadmins` | name, email, password, two_factor fields |
| `plans` | name, code, price fields (filled in billing phase), features (json) |
| `settings` | business_id nullable, key, value (encrypted where secret) |
| `audit_logs` | business_id nullable, actor_type, actor_id, impersonated_by, action, meta (json) |
| Laravel | `jobs`, `failed_jobs`, `cache`, `sessions`, `password_reset_tokens` |

Push (Phase 4)

| Table | Key columns |
|---|---|
| `push_subscriptions` | business_id, endpoint, endpoint_hash (unique), p256dh, auth, encoding, platform (android/ios/desktop), source (hosted/grav/snippet), origin, user_agent, last_success_at, fail_count, created_at |
| `push_messages` | business_id, created_by, title, body, url, image_path, status (draft/scheduled/sending/sent/cancelled), scheduled_at, started_at, finished_at, target_count, success_count, failure_count, expired_count, click_count |

Later phases (outline only, designed when we get there)

- Barber/salon: `services`, `team_members`, `bookings`, `loyalty_cards`, `loyalty_stamps`, `gallery_items`.
- Billing: Cashier columns on `businesses`, plus `subscriptions` and `subscription_items`.
- Sales: `salespeople`, `sales` (business, salesperson, amount, status), `commissions`
  (amount, held_until, status: held/payable/paid/clawed_back). Reuses Chantley's partner/commission code.

## 8. Reserved paths

`admin`, `login`, `logout`, `api`, `dashboard`, `pricing`, `assets`, `signup`, `register`, `password`,
`build`, `storage`, `up`, `privacy`, `terms`, `contact`, `demo`, `stripe`, `sw.js`, `manifest`.
Enforced by a validation rule on `businesses.slug`, plus a test that fails if any registered route's
first segment isn't reserved.

## 9. Secrets

- `.env` only: `APP_KEY`, DB credentials, VAPID keys. Never committed; `.env.example` has placeholders.
- Stripe and SMTP keys: stored **encrypted in `settings`**, entered in the super admin panel (Chantley pattern),
  so keys rotate without an SSH session. This depends on `APP_KEY`, which is backed up alongside the VAPID keys.

## 10. Legal note

The browser permission prompt is the push consent. Push is **not** "law-free": CASL, privacy law and app-store-style
claims need a legal check before any marketing copy says so. Store consent metadata; make unsubscribing one tap.

## 11. Risks

| # | Risk | Mitigation |
|---|---|---|
| 1 | VAPID key lost or changed → every subscription dead | `.env` + backup command + doc; generate command refuses to overwrite |
| 2 | Business changes domain → its subscribers can't be reached from the new site | Documented as a one-way door; hosted app (A) is domain-independent |
| 3 | iOS friction (must install first) | iOS guide in the install flow; demo on Android first; iPhone test device |
| 4 | Snippet sites that can't host a file (Wix, Squarespace) | Hosted app option A |
| 5 | Cross-origin manifest not honoured by a browser | Phase 5 verification; plugin/snippet fall back to a locally served manifest |
| 6 | Shared hosting throughput (cron, 55 s/min) | Concurrent batch flush; measure in Phase 4; same code on a VPS worker |
| 7 | Tenant data leak | Global scope fails closed, isolation tests, `qa-run` check |
| 8 | Public key is visible; Origin can be spoofed by scripts | Rate limits, subscribe-only scope for the key; sending needs an owner login |
| 9 | `APP_KEY` lost → encrypted Stripe/SMTP settings unreadable | Backed up with VAPID keys |
| 10 | Push permission shared across hosted apps on runwrk.com | Subscribe only on explicit tap per business |
| 11 | Marketing claims about consent/law | Legal check before launch copy (§10) |

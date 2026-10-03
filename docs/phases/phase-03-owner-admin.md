# Phase 03 — Owner admin (and Phase 2 fixes)

Status: **awaiting approval**

## Goal
Business owners can register, sign in, brand their app and manage their team. Phase 2 feedback applied.

## Phase 2 fixes (your answers)
| Item | Done |
|---|---|
| Chantley look | Ported Chantley's slate/violet sidebar layout, dark-mode toggle, guest card, flash messages. The admin, owner and public pages all use it |
| Registration and login "exactly like Chantley" | Pricing → register (plan chosen) → 6-digit emailed code → business + owner created and logged in. Rate-limited login (5/min per email+IP), forgot/reset password, "Back to website" guest layout |
| Owner sets their own password | Self-signup: they choose it. Super-admin-created owners and invited staff get a "set your password" email (link valid 3 days, also shown to you in case mail isn't set up). No password is ever generated or shown |
| Demo pricing page + plans | `/pricing` lists public plans. Seeded demo placeholders: Free trial, Starter $29, Growth $59, Pro $99 (only if no plans exist). Super admin → Plans & pricing: create, edit, archive/restore, hide, reorder. Edit them with the real pricing |
| 2FA "prepare it now" | Built fully for the super admin: QR setup, code confirm, login challenge, 8 one-time recovery codes, disable/regenerate with password |
| Impersonation | Banner and "Return to admin" button now in the new owner layout; start/stop audited |
| GitHub | Pushed `main` and tags `phase-00..02-complete` to `squadri00/runwrk` (still **public**) |

## What was built (Phase 3)
- Owner dashboard with a "Get set up" checklist (logo, website connected, team).
- **Branding** (owner only): business name, app name, brand colour, icon background colour, phone, address, website, timezone, logo upload.
  Logo is converted with PHP GD into `icon-192`, `icon-512`, `maskable-512` and `apple-touch-180` PNGs (stored under `storage/app/public/businesses/{id}/icons`). Icons rebuild when the background colour changes. Opening hours removed from the product and the database.
- **Team** (owner only): invite staff or another owner by email, resend invite, remove (not yourself, not the last owner). Roles: owner (everything) and staff (dashboard and, later, push/subscribers).
- **My account**: name and password change (needs current password).
- **Super admin → Settings**: platform name, support email, "allow signups" switch, SMTP (host, port, encryption, username, password stored encrypted, from address/name) with a "send test email" button. Overrides `.env` at runtime, also for cron jobs.
- Super admin → Security (2FA), Plans, Settings added to the sidebar.

## Decisions and why
| Decision | Why |
|---|---|
| No opening hours | Your call: info lives on the customer's website; the app layer is ours |
| Staff can't edit branding or team | Matches your "owner / staff" answer |
| Allowed domains stay super-admin-only | The security boundary for the public API; owners see them read-only |
| Country/tax fields and Turnstile captcha from Chantley not copied | Tax belongs to the billing phase; captcha can be added then. Signup is still throttled and code-verified |
| Paid plans don't charge yet | Stripe arrives in billing; until then every plan confirms by code and billing is followed up by hand (Chantley's behaviour for an unlinked plan) |
| Team removal is a hard delete | `users.email` is unique, so a soft-deleted address could never be re-invited |
| Reset links last 3 days | Same link serves invites, which may wait a while. Normal forgot-password links also last 3 days |
| Signup code and recovery codes stored hashed | A database leak doesn't expose them |

## Files and tables
Tables: `pending_registrations` (new); `plans` gains price, interval, currency, description, stripe_price_id, is_public, sort_order, archived_at (dropped `active`); `superadmins` gains `two_factor_recovery_codes`; `businesses.hours` dropped.
Code: `app/Http/Controllers/{Auth,App,Admin}`, `app/Services/IconGenerator.php`, `app/Domain/Tenancy/Invites.php`, `app/Domain/Ops/PlatformSettings.php`, `app/Domain/Billing/RegistrationFinalizer.php`, `app/Mail/*`, `resources/views/{components,auth,app,admin,site,mail}`.
Packages: `pragmarx/google2fa`, `bacon/bacon-qr-code`.

## How to test
```bash
php artisan migrate:fresh --seed
php artisan storage:link          # once, so uploaded logos are visible
php artisan test                  # 61 tests
php artisan serve --port=8123
```
- Public: `/pricing`, then Start free, register, read the code from `storage/logs/laravel.log` (mail is logged locally), verify.
- Owner demo logins: `owner@demo.test`, `owner@hoshmint.test` (password `password`) at `/login`.
- Admin: `/admin/login`. Turn on 2FA at Security, sign out, sign in again.
- SMTP: Settings → fill in, Save, "Send a test email".

## Known issues
- Hostinger SMTP credentials aren't set yet, so emails go to the log locally. Enter them in Settings (or `.env`) before go-live.
- Seeded demo plans are placeholders; `db:seed` on production creates them only when no plans exist.
- The owner dashboard shows the future app address `runwrk.com/{slug}`; it goes live in the next phase.
- No CAPTCHA on signup yet.
- Test suite passes, but I only eyeballed the pricing page in the browser (the preview pane was too small for reliable screenshots). Please click through the owner screens once.
- Legal: signup has no terms/privacy checkbox yet (CASL/privacy review is on the checklist before marketing).

## Open questions
- Hostinger mailbox for sending (address, username, password) when you're ready.
- Make the GitHub repo private?

## Next: Phase 4 — Push engine and hosted app
VAPID keys and backup command, subscribe/unsubscribe API, hosted app at `runwrk.com/{slug}` with manifest and icons from this phase, service worker, install prompt with iPhone guide, send form, scheduling, cron batch sender, logs, click tracking. First test on your Android phone through a Cloudflare tunnel.

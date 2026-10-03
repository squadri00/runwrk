# Phase 00 — Discovery and architecture

Status: **awaiting approval**

## Goal
Agree on the architecture, data model, risks and phase plan before writing code.

## What was produced
- `docs/ARCHITECTURE.md`: system shape, hosting, tenancy, the three ways to connect a business,
  push engine, database outline, reserved paths, secrets, legal note, risks.
- This document and `docs/PROGRESS.md`.

## Decisions and why
| Decision | Why |
|---|---|
| Hostinger + `public_html` symlink to `runwrk/public`, `deploy.sh` over SSH | Exactly how chantley.com runs; proven, no root `.htaccess` exposure risk |
| Laravel 13 / PHP 8.4 / Blade + Tailwind 4 + Livewire where needed | Same stack as Chantley |
| Tenancy copied from Chantley (`account_id` → `business_id`) | Already battle-tested, including impersonation and audit |
| Hosted app at `runwrk.com/{slug}` added as option A | Works for every business (even Wix), and makes the sales demo independent of the prospect's site |
| Service worker on customer sites = 1-line `importScripts` of central `sw-core.js` | Update all customers by deploying Runwrk only |
| Ticketing removed from the Hoshmint pilot | Owner decision: Hoshmint uses push to drive sales through its existing ticketing |
| Stripe/SMTP keys encrypted in DB via super admin; VAPID in `.env` | Chantley pattern: rotate payment/mail keys without SSH. VAPID is set once and must never change |
| Old phases 4+5 regrouped; deploy moved earlier | Push can't be tested without the subscribe flow, and Hoshmint can't pilot until Runwrk is live |
| No `gmp` needed | web-push only requires curl/json/mbstring/openssl; bcmath (present) is enough |

## Revised phase plan
| # | Phase | Contents |
|---|---|---|
| 0 | Discovery | This document |
| 1 | Foundation | git (`main`), Laravel 13, `.env`, tenancy (trait, scope, resolver by login/key/slug), guards, audit, settings, reserved paths, seeders, tests, `deploy.sh` + `HOSTINGER-DEPLOY.md` skeleton |
| 2 | Super admin | Businesses: create, plan, status, slug, public key (regenerate), allowed domains, branding; impersonate; audit view |
| 3 | Owner admin | Login, dashboard, branding (name, logo, icon, colours, hours, contact), staff users, 2FA-ready |
| 4 | Push engine + hosted app | VAPID commands + backup doc, hosted app `/{slug}`, dynamic manifest/icons, service worker, install prompt + iOS guide, subscribe/unsubscribe, send form (title, message, link, image), scheduling, batch sender, logs, counts, dead-subscription cleanup, click tracking. End-to-end test on your Android phone via Cloudflare Tunnel |
| 5 | Connectors | Universal snippet + `runwrk-sw.js`, Grav PWA plugin, install guides for both, cross-origin manifest verification, test against a local copy of hoshmint |
| 6 | First deploy + Hoshmint pilot | Deploy to Hostinger, connect hoshmint.com via snippet, push templates (announcement, reminder, last-minute sale), first real sends |
| 7 | Barber & salon module + demo | Services, team, bookings (no customer account), loyalty stamps, gallery, tap-to-call, map; polished `demo-barber` business |
| 8 | Marketing site + billing | Home, pricing, signup, Stripe subscriptions ($600 year one then monthly), invoices, failed payments, suspend/reactivate |
| 9 | Sales tools | Salesperson accounts, sale tracking, $200 commission with 30-day holdback, reports, training outline (reuse Chantley partner code) |
| 10 | Hardening & QA | Isolation sweep, security review, rate limits, backups (DB, `.env`, VAPID, APP_KEY), error logging, performance |
| 11 | Production go-live | Final runbook, rollback plan, VPS migration plan |

## Files and tables added
Docs only. No code or tables.

## How to test it
Read `docs/ARCHITECTURE.md` and approve or change the plan.

## Known issues
- MySQL `root` has a password, so I couldn't confirm that the `runwrkdb` database and user exist (see questions).

## Open questions (for Phase 1)
See the Phase 1 questions in the chat reply.

## Next phase preview
Phase 1 — Foundation: git init on `main`, Laravel 13 install, tenancy layer with isolation tests,
super admin + business user guards, seeders, `deploy.sh`.

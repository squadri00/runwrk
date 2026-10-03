# Phase 02 — Super admin

Status: **complete** (fixes and extras in phase-03 doc)

## Goal
Manage business customers from one panel: create, plan, status, public key, allowed domains, support impersonation.

## What was built
- `/admin/login` (rate limited 5/min), logout, separate `superadmin` guard; owners are redirected away from `/admin`.
- `/admin` overview (counts by status, recent activity).
- `/admin/businesses`: search and status filter, create, view, edit.
  - Create makes the business, its owner login and its allowed domains in one transaction. A random password is shown **once**.
  - Edit covers name, slug (reserved/duplicate checked), plan, status, branding fields, contact, timezone, allowed domains.
  - Allowed domains are normalised (lowercase, host only) and validated; `*.example.com` supported.
  - Regenerate public key: the old key stops working immediately.
- Impersonation: "Log in as owner" opens `/dashboard` as that business's owner with an amber "Return to admin" bar. Start and stop are audited; every audit row written meanwhile carries `impersonated_by`.
- `/admin/audit` full log, plus per-business activity on its page.
- Placeholder `/dashboard` (owner area, built in Phase 3).
- Tailwind 4 built locally; `public/build` is committed (Hostinger has no Node).

## Decisions and why
| Decision | Why |
|---|---|
| Assumed answers to my Phase 2 questions (plain Blade, generated owner password, 2FA columns only, banner impersonation) | You said "go ahead" without answering; these were my stated defaults |
| Generated password shown once instead of an emailed set-password link | Mail isn't configured yet; avoids a half-built reset flow. A real password reset arrives with owner login in Phase 3 |
| Status changes live in the edit form, no separate suspend button | Fewer screens; the effect is immediate and audited as a diff |
| Edit audit stores only changed fields (`from`/`to`) | Readable trail without logging full records |
| Logo/icon upload not in the super admin | Owners upload their own in Phase 3 |
| Removed Vite's Google/Bunny font plugin | It fetched fonts from a CDN at build time; system fonts are fine for an admin tool |

## Files added (main)
`app/Http/Controllers/Admin/{Auth,Business,Impersonation,Audit}Controller.php`, `DashboardController.php`,
`resources/views/{layouts,admin,dashboard}`, `routes/web.php`, `Business::syncDomains()`, `tests/Feature/SuperAdminTest.php`.
No new tables.

## How to test
```bash
php artisan migrate:fresh --seed
php artisan test                      # 32 tests, 12 new
php artisan serve --port=8123
```
Sign in at http://127.0.0.1:8123/admin/login with the `SUPERADMIN_EMAIL` and password from `.env` (local default password: `password`).
Create a business, copy the shown password, click "Log in as owner", then "Return to admin".

## Known issues
- 2FA is not active (columns exist). Say if you want it before launch; it's about half a day.
- An impersonated business that is suspended returns 403 and shows no banner; use `/admin` to get back.
- GitHub repo `squadri00/runwrk` is **public** per your screenshot. The remote is added locally; nothing is pushed yet.

## Open questions
- Keep the GitHub repo public, or make it private before the first push?

## Next: Phase 3 — Owner admin
Owner login/logout/password reset, dashboard shell, branding settings (name, logo, icon, colours, hours, contact), staff users.

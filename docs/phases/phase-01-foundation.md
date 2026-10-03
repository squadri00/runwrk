# Phase 01 — Foundation

Status: **complete**

## Goal
A working Laravel base with tenant isolation proven by tests, so every later feature is built on it.

## What was built
- Git repo on `main`, `.gitignore` (excludes `.env`), Phase 0 commit tagged `phase-00-complete`.
- Laravel 13 (PHP 8.4) in the repo root, wired to MariaDB `runwrkdb`. Password lives only in `.env`.
- Tenancy: `CurrentBusiness`, `BusinessScope` (fails closed), `BelongsToBusiness` trait.
- Resolvers: `ResolveBusiness` (logged-in user, blocks suspended/cancelled) and `ResolveApiBusiness`
  (public key + allowed domains + per-business CORS + preflight).
- `OriginChecker`: exact and `*.domain` matches; empty list = no browser access; localhost only in local env.
- `AvailableSlug` validation rule and `config/runwrk.php` reserved paths.
- Guards: `web` (users) and `superadmin`. `Audit::log()` and `Settings` (with encrypted secrets) helpers.
- Seeders: one plan, superadmin from `.env`, and (local only) businesses `demo-barber` and `hoshmint`.
- `routes/api.php` with `GET /api/v1/ping` as the smoke endpoint.
- `deploy.sh` and `docs/HOSTINGER-DEPLOY.md` skeleton. `CLAUDE.md` rewritten for this project.

## Decisions and why
| Decision | Why |
|---|---|
| Scope fails closed (no business → no rows) | Chantley's scope was inert without a tenant; a forgotten resolver there leaks data, here it returns nothing |
| `User`, `BusinessDomain`, `Setting` not scoped | Needed before a tenant is known (login, key check); reached via the business relation |
| Framework CORS disabled (`config/cors.php` empty) | Laravel's default sent `Access-Control-Allow-Origin: *` on every `/api` response, defeating the domain list |
| Tests run on `runwrkdb_test` with a hard guard | `RefreshDatabase` wipes the database; the guard fails any run pointed at a non-`_test` database |
| Request without Origin passes the key check | Not a cross-origin browser request. The key is public, so scripts can spoof Origin anyway; the real protection is rate limiting and owner-only sending |
| No schema-export or qa-run commands yet | Added when there is enough to check (Phase 4 onward) |
| No UI yet | Super admin UI is Phase 2 |

## Tables
`plans`, `businesses` (slug, status, public_key, branding), `business_domains`, `users` (business_id, role, 2FA columns),
`superadmins`, `settings`, `audit_logs`, plus Laravel `cache`, `jobs`, `sessions`, `password_reset_tokens`.

## How to test
```bash
php artisan migrate:fresh --seed
php artisan test        # 20 tests: isolation, API key/origin/CORS, reserved paths, middleware
php artisan serve --port=8123
```
Then call `GET http://127.0.0.1:8123/api/v1/ping` with header `X-Runwrk-Key: <hoshmint public_key>` and
`Origin: https://hoshmint.com` (200, CORS echoed) versus `Origin: https://evil.com` (403).

## Known issues
- No login screens yet (Phase 2 for super admin, Phase 3 for owners).
- `.gitattributes`/CRLF warnings on Windows are harmless.

## Open questions
None blocking. Phase 2 questions follow in chat.

## Next: Phase 2 — Super admin
Login at `/admin`, create and manage businesses (plan, status, slug, public key regeneration, allowed domains, branding),
impersonate an owner for support, audit log view.

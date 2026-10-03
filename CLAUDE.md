# Runwrk — project guide

Multi-tenant SaaS: small businesses get their own installable app and web push to all their customers' phones.
Customer-facing copy never says "PWA". Everything lives in the root of runwrk.com (no subdomains).

**Design:** [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md). **Phase notes:** `docs/phases/`. **Progress:** `docs/PROGRESS.md`.

## Working rules
- Work in phases; stop after each and wait for approval. Ask before each phase (max 5 questions).
- Each phase ends with `docs/phases/phase-XX-name.md`, an updated `docs/PROGRESS.md`, a commit and tag `phase-XX-complete`.
- Short answers, minimal code, no explanatory comments unless critical. Tests for tenant isolation, push sending and payments.
- Never write the DB password anywhere except `.env`. Never commit `.env`.

## Stack
Laravel 13, PHP 8.4, MariaDB (`runwrkdb`, tests use `runwrkdb_test`), Blade + Tailwind 4 (built locally, `public/build` committed),
database queue/cache/sessions. Hostinger shared hosting: no Node, queue runs from cron.

## Multi-tenancy
- One database; every tenant-owned table has `business_id`.
- Models use `App\Domain\Tenancy\BelongsToBusiness` (global `BusinessScope`, auto-fills `business_id`).
  **The scope fails closed**: no current business means no rows. Cross tenants only with `withoutBusinessScope()`.
- `CurrentBusiness` singleton is set by `ResolveBusiness` middleware (login), `ResolveApiBusiness` (public key + allowed domains),
  or `CurrentBusiness::run($business, fn)` for jobs/commands/seeders.
- `User` and `BusinessDomain` are not scoped; reach them through `$business->users` / `$business->domains`.
- Guards: `web` (business users), `superadmin`.
- Reserved first path segments: `config('runwrk.reserved_paths')`. A test fails if a route uses an unreserved one.

## Commands
```bash
php artisan migrate:fresh --seed   # local demo data: demo-barber, hoshmint, superadmin from .env
php artisan test                   # uses runwrkdb_test (TestCase refuses any other database)
```
Local logins (password `password`): `owner@demo.test`, `owner@hoshmint.test`; superadmin = `SUPERADMIN_EMAIL` in `.env`.

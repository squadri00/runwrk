# Deploying Runwrk to Hostinger

Same flow as Chantley: build assets on your PC → `git push` → SSH → `bash deploy.sh`.
Skeleton from Phase 1; the full runbook (cron, SSL, rollback, go-live checklist) is completed in Phase 11.

- App code: `~/domains/runwrk.com/runwrk`
- Web root: `public_html` is a symlink to `runwrk/public`
- PHP 8.4 (`/opt/alt/php84/usr/bin/php`), Composer at `/usr/local/bin/composer`

## One-time setup
1. GitHub: create a private repo `runwrk`, then locally `git remote add origin <url>` and `git push -u origin main --tags`.
2. hPanel: PHP 8.4 with extensions `pdo_mysql, mbstring, openssl, curl, bcmath, gd, zip, fileinfo`; create the database; enable SSH.
3. SSH:
   ```bash
   cd ~/domains/runwrk.com
   mv public_html public_html_backup
   git clone <repo-url> runwrk && cd runwrk
   cp .env.example .env && nano .env
   PHP=/opt/alt/php84/usr/bin/php
   $PHP /usr/local/bin/composer install --no-dev --optimize-autoloader
   $PHP artisan key:generate && $PHP artisan migrate --force && $PHP artisan db:seed --force
   cd .. && ln -s runwrk/public public_html
   ```
4. Cron (every minute), after the push engine exists (Phase 4):
   ```
   /opt/alt/php84/usr/bin/php /home/<user>/domains/runwrk.com/runwrk/artisan schedule:run >> /dev/null 2>&1
   /opt/alt/php84/usr/bin/php /home/<user>/domains/runwrk.com/runwrk/artisan queue:work --stop-when-empty --max-time=55 --tries=3 >> /dev/null 2>&1
   ```

## Push keys (once, on the server)
```bash
cd ~/domains/runwrk.com/runwrk
/opt/alt/php84/usr/bin/php artisan runwrk:vapid-generate   # writes VAPID keys into .env
/opt/alt/php84/usr/bin/php artisan runwrk:vapid-backup     # then download storage/backups/*.txt and delete it from the server
```
Read `docs/VAPID-KEYS.md`. Never regenerate after customers have subscribed. Also run `php artisan storage:link` (deploy.sh does it).

## First deploy (done 2026-10-04) and what to remember
- Account folder: `~/domains/runwrk.com/` holds `runwrk/` (the app), `public_html` (a link to `runwrk/public`) and `public_html_backup` (the old empty folder).
- **The website PHP version is set per domain in hPanel and must be 8.4.** hPanel > Websites > runwrk.com > Dashboard > PHP Configuration > 8.4. The SSH command line can say 8.4 while the website still runs 8.3 (symptom: every page shows `Composer detected issues in your platform ... >= 8.4.1`).
- `crontab` is not available over SSH here. Add the two jobs in hPanel > Advanced > Cron Jobs (every minute):
  ```
  /opt/alt/php84/usr/bin/php /home/u528385036/domains/runwrk.com/runwrk/artisan schedule:run >> /dev/null 2>&1
  /opt/alt/php84/usr/bin/php /home/u528385036/domains/runwrk.com/runwrk/artisan queue:work --stop-when-empty --max-time=55 --tries=3 >> /dev/null 2>&1
  ```
- First logins are in `~/runwrk-first-login.txt` (mode 600). Save them in your password manager, then delete the file.
- The live push keys backup is `runwrk/storage/backups/vapid-*.txt`. Download it, store it safely, then delete it from the server. Never regenerate the keys.
- Signups are switched off until email works (Super admin > Settings > allow new businesses to sign up).
- Updates: push to GitHub, then `cd ~/domains/runwrk.com/runwrk && bash deploy.sh`. The script also creates the storage folders Laravel needs.
- The repository is public, so the server clones it without a login. If you make it private, add a deploy key on the server first.

## Production `.env`
Different database and password from local. Quote any value containing `#`, `!` or `@`.
`APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://runwrk.com`, `SESSION_SECURE_COOKIE=true`.
Back up `.env` (APP_KEY and VAPID keys especially) outside the server.

## Every deploy
```bash
npm run build && git add -A && git commit -m "..." && git push
# then on the server
cd ~/domains/runwrk.com/runwrk && bash deploy.sh
```

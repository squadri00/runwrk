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

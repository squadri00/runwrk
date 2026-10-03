# VAPID keys: read this once

Runwrk signs every push message with **one platform key pair** (`VAPID_PUBLIC_KEY`, `VAPID_PRIVATE_KEY` in `.env`).
Each customer's phone subscribes against the public key. If the key pair is lost or changed, **every existing subscriber
stops receiving messages and must subscribe again.** There is no recovery.

## Rules
1. Generate once per environment: `php artisan runwrk:vapid-generate`. It refuses to run if keys already exist.
2. Back up straight away: `php artisan runwrk:vapid-backup`. It writes `storage/backups/vapid-<date>.txt` (ignored by git).
   Move that file to a password manager or private drive. The file also holds `APP_KEY` (encrypted Stripe/SMTP settings need it).
3. Never commit the keys. Never paste them in chat or tickets.
4. Local and production have **different** key pairs. A phone subscribed locally is not a production subscriber.
5. Moving servers: copy `.env` (or at least these two values and `APP_KEY`). Do not regenerate.

## If the keys are lost
Generate new ones, deploy, and ask customers to open their app and tap "Turn on notifications" again. Their old subscriptions
fail with a 4xx error and are removed automatically.

## Windows / XAMPP note
Creating or using the keys needs `OPENSSL_CONF` to point at XAMPP's config. `dev-serve.cmd` and `scripts/dev-phone.ps1` set it.
For terminals and Apache, set it once for your user:
```powershell
setx OPENSSL_CONF "D:\xampp\php\extras\ssl\openssl.cnf"
```
Then open a new terminal (and restart Apache). Hostinger (Linux) needs nothing.

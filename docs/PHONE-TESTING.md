# Testing on your Android phone

Push needs HTTPS, so your phone reaches your computer through a Cloudflare quick tunnel (free, no account).

## Run it
```powershell
cd D:\xampp\htdocs\runwrk
.\scripts\dev-phone.ps1
```
It prints an address like `https://xxxx.trycloudflare.com`. The address is new each run. Leave the window open.
If PowerShell blocks the script: `powershell -ExecutionPolicy Bypass -File .\scripts\dev-phone.ps1`.

## The demo (what a salesperson will do)
1. **Phone:** open `<address>/demo-barber` in Chrome.
2. Tap **Install app** (or Chrome menu → *Add to Home screen*). Open the new "Demo Barber" icon from the home screen.
3. Tap **Turn on notifications**, then **Allow**.
4. **Computer:** open `<address>/login`, sign in as `owner@demo.test` / `password`.
5. Notifications → New message → write a title and message → **Send message**.
6. The phone buzzes within a few seconds. Tap it: the app opens and "Opened" goes up by one on the message page.
7. In the app, **Turn off notifications** removes the phone from Subscribers.

If nothing arrives: Subscribers should show 1 Android. Message page shows Delivered / Not delivered. Check
`storage/logs/laravel.log`. A "send now" message goes out right after you press send; scheduled messages need
`php artisan runwrk:push-run` locally (production uses the cron every minute).

## Scheduled messages locally
```powershell
php artisan runwrk:push-run
```

## iPhone
Push works only after "Add to Home Screen" (iOS 16.4+), then open from the home screen and tap Turn on notifications.
The hosted page shows these steps on iPhone. A real iPhone is needed to test; online device farms are unreliable for push.

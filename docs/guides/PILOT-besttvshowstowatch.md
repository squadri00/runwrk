# Pilot: besttvshowstowatch.com

First real website connected to live Runwrk (2026-10-04). It replaced hoshmint.com as the pilot.

## The site
Custom PHP site on the same Hostinger account. It already has its own app: `sw.js` (caching, offline page), `manifest.json`, `offline.html`, and `pwa-init.js` (loaded from `integ/incheader.php`, includes its own install banner). Its push code is only a placeholder (no key, endpoint missing), so nothing clashes.

## How Runwrk is connected (safe variant)
A website can run only one service worker per scope, and theirs covers `/`. Registering Runwrk's worker there would replace theirs. So Runwrk runs **in its own corner**: scope `/runwrk-app/`.
- Push works from that scope; the notification tap opens the site normally.
- Their `sw.js`, `manifest.json`, `pwa-init.js`, `offline.html` are untouched (checked with SHA-256 before and after).
- The Runwrk button is set to **notifications only** (`data-install="no"`) because they have their own install banner. iPhone visitors still get the add-to-home-screen steps.

## What was changed on their site (only this)
1. New file `public_html/runwrk-app/runwrk-sw.js` (one line importing Runwrk's shared worker).
2. One line added to `public_html/integ/incheader.php`, right after their `pwa-init.js` line:
   `<script src="https://runwrk.com/assets/runwrk-connect.js" data-key="pk_..." data-sw="/runwrk-app/runwrk-sw.js" data-install="no" data-source="snippet" async></script>`

## On live Runwrk
Business "Best TV Shows To Watch" (slug `best-tv-shows`, plan Starter, active). Allowed websites: `besttvshowstowatch.com`, `www.besttvshowstowatch.com`. Brand colours from their site (#e50914 on #0a0a0f), logo taken from their `icons/icon-512x512.png`. Owner login: squadri00@gmail.com, set-password link saved privately on the server.

## Backup and undo
Backup of the only edited file: `~/domains/besttvshowstowatch.com/runwrk-backup-20261004-230233/` (outside the website folder; also holds `untouched-files.sha256`).

To remove Runwrk completely:
1. Restore the file: `cp -p ~/domains/besttvshowstowatch.com/runwrk-backup-20261004-230233/integ/incheader.php ~/domains/besttvshowstowatch.com/public_html/integ/incheader.php` (or delete line 21).
2. Delete the folder `~/domains/besttvshowstowatch.com/public_html/runwrk-app/`.
Visitors who already turned notifications on simply stop receiving them.

## Verified
Live in a real browser: Runwrk's worker active in `/runwrk-app/`, their `/sw.js` still controls the page and is active, their manifest unchanged (one manifest link), the button shows "Get notifications" in their red. Simulated test proves Runwrk subscribes through its own worker even when the site has one (regression test `tests/Support/lib-sim.cjs`).

## Not yet verified
A real visitor turning notifications on and receiving a message, tapping it, iPhone behaviour. Their pre-existing `css/custom.css` returns 404 (not caused by Runwrk).

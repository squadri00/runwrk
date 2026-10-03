# Phase 05 — Connectors (existing websites)

Status: **awaiting approval** (needs your phone test)

## Goal
Any business website, starting with hoshmint.com, can offer the app and notifications: one script line plus two small files, or the Grav plugin.

## Answers assumed (you said "commence" without answering)
One line plus two files for any site; Grav plugin with the key pasted in; small floating **Get our app** button (bottom right, configurable); simple offline page only; test on a local copy of hoshmint.

## What was built
- **`runwrk-connect.js`** (10 KB, loaded `async`): adds the manifest link and icons if missing, loads the shared library when the browser is idle, shows the floating button. Panel handles Android/desktop (install + notifications on/off), iPhone (add-to-home-screen steps), blocked permission, already-installed app. Styles live in a shadow root, so it cannot clash with the website's CSS. Fails silently on a wrong key or disallowed address. Button shrinks to an icon for 7 days after "close".
- **API for connected sites**: `GET /api/v1/manifest?site=` (manifest for that website's own address, including sub-folders; refused for addresses not on the allowed list) and a richer `config` (name, colours, icon, app page).
- **Shared worker** (`sw-core.js`): adds an **offline page** for page navigations only (images, scripts and API calls pass straight through).
- **Dashboard → Connect website**: app page link and QR code, the four steps with the two file downloads and the exact script line (key filled in), WordPress and Wix/Squarespace notes, Grav plugin zip with the key pre-filled, list of linked addresses.
- **Grav plugin** `integrations/grav/runwrk-connect`: serves `/runwrk-sw.js` and `/runwrk-manifest.webmanifest` from the site's own address (manifest fetched from Runwrk, cached an hour, last good copy used if Runwrk is unreachable), injects the manifest link and the script into every page, skips the admin, does nothing without a key. Written for Grav 2.x and 1.7.
- **Guide**: `docs/guides/CONNECT-A-WEBSITE.md`.

## Bug found and fixed during testing
Browsers send the CORS "preflight" **without** the key header, and Runwrk rejected it with no CORS headers, so every call from a customer website was blocked. My Phase 1 test sent the key on the preflight, which real browsers never do, so it passed. Preflights now answer without a key (they reveal nothing); the real request still needs a valid key and an allowed address. New tests cover this the way a browser behaves.

## Decisions and why
| Decision | Why |
|---|---|
| Manifest served from the customer's own address (file or plugin) | Browsers only accept a manifest whose start address belongs to the page's own site |
| Two small files + one line instead of a plain snippet | A worker must be a real file on the customer's domain |
| Shadow DOM for the button | Cannot break or be broken by the customer's CSS |
| Shared worker and button script updated centrally | Fix once, every connected site updated |
| Offline page only for navigations | Avoids interfering with the customer's site |
| Wix/Squarespace/Shopify get the app page link instead | They cannot host the two files |

## Files
`public/assets/{runwrk-connect.js,sw-core.js}`, `app/Services/ManifestBuilder.php`, `app/Support/Qr.php`, `app/Http/Controllers/App/ConnectController.php`, `app/Http/Controllers/Api/PushApiController.php`,
`app/Http/Middleware/ResolveApiBusiness.php`, `resources/views/app/connect.blade.php`, `integrations/grav/runwrk-connect/*`, `tests/Feature/ConnectorTest.php`, `tests/Support/sw-sim.cjs`. No new tables.

## How to test
```bash
php artisan test      # 145 tests (19 new: manifest, connect page, downloads, plugin zip, worker simulation, preflight)
```
**On your Android phone** (the hoshmint test copy is running behind its own tunnel, see my message for the address): open it, tap **Get our app**, turn notifications on, then send a message from the demo owner dashboard (`owner@hoshmint.test`) → Notifications.

## Verified
- In a real browser, over HTTPS, on a separate website address (hoshmint copy): button appears in the brand colour, manifest link added and served from the site's own address (scope `/`), all three icons load, worker `/runwrk-sw.js` registered and **activated** (cross-origin shared worker works), panel opens and reports the blocked-notifications state correctly.
- The real worker file run in a simulated worker: images/scripts ignored, online pages pass through, offline pages get the offline screen, notification shown (and still shown for a broken payload), tap opens the right link and reports the click.
- Grav plugin on your Grav 2.2.3 copy: both files served, manifest fetched from Runwrk, script injected once, admin untouched, wrong key gives a clean error with the page unharmed, empty key does nothing.
- **Not verified**: actual notification delivery to a phone from a customer website, Chrome's install prompt, iPhone behaviour (this preview browser blocks notifications and install prompts), Mobirise's "custom code" spot, Google PageSpeed impact on a customer site (script is async and small).

## Known issues
- The plugin is installed in your local Grav (`user/plugins/runwrk-connect`) with **no key set**, so it is switched off. I removed the test key.
- Subscribers belong to the website address they subscribed on; moving a website to a new address means they must subscribe again.
- The lab copy of hoshmint lacks the large `img` folder, so some pictures are missing there.
- No uninstall/"stop showing the button" control for owners yet (they can remove the script line).

## Open questions
- Does the phone test work (button, install, notification, tap)?
- Where is the Mobirise custom-code spot for hoshmint? We will confirm in Phase 6 when we connect the real site.

## Next: Phase 6 — First deploy and Hoshmint pilot
Deploy Runwrk to Hostinger (runwrk.com), connect hoshmint.com for real, push templates (announcement, reminder, last-minute sale), first real sends.

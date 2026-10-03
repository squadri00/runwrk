# Connect a website to Runwrk

For the Runwrk team. Customers get the same steps, with their own key filled in, under **Dashboard → Connect website**.

## What gets installed
| Piece | Where it lives | Purpose |
|---|---|---|
| `runwrk-sw.js` | the customer's own website, main folder | One line that loads Runwrk's shared worker. Shows messages, opens the link on tap, shows an offline page |
| `runwrk-manifest.webmanifest` | the customer's own website, main folder | Name, colours and icons for the home-screen app. **Must be served from the customer's own address** (browsers refuse it otherwise) |
| one `<script>` line | before `</body>` on every page | Adds the **Get our app** button |

The shared worker and the button script are updated centrally: deploying Runwrk updates every connected website.

## Before you start (Runwrk side)
1. The business exists in Super admin and has a plan.
2. Its website address is in **Allowed domains** (for example `joesbarber.com` and `www.joesbarber.com`). The API refuses any other address.
3. Branding is done (logo, colours). The home-screen icons come from the logo.

## Any website (static HTML, WordPress, custom)
1. Owner opens **Connect website**, picks the website, downloads `runwrk-sw.js` and `runwrk-manifest.webmanifest`.
2. Upload both to the **main folder** (next to `index.html`; for WordPress next to `wp-config.php`). Keep the names.
3. Check that `https://their-site.com/runwrk-sw.js` and `.../runwrk-manifest.webmanifest` open in a browser.
4. Paste the script line before `</body>` on every page (footer template, or a "header and footer" plugin):
   ```html
   <script src="https://runwrk.com/assets/runwrk-connect.js" data-key="pk_..." async></script>
   ```
5. Open the website on a phone. The button appears. Tap it, turn notifications on, send a test from **Notifications**.

Optional attributes: `data-position="left"`, `data-sw` and `data-manifest` (if the files are in a sub-folder; the sub-folder becomes the app's scope).

### Hoshmint (static pages built with Mobirise)
- Upload the two files to the folder that holds `index.html`.
- Add the script line once to the shared footer or custom code area of the Mobirise project (or paste it into each exported page). Re-export and upload.
- Phase 6 does this on the real site. The test copy used in Phase 5 behaved exactly as above.

## Grav websites
1. Owner downloads `runwrk-connect.zip` from **Connect website** (the key is already inside it), or copy `integrations/grav/runwrk-connect` from this repo.
2. Upload the `runwrk-connect` folder to `user/plugins/`.
3. Clear the Grav cache. Done: the plugin serves the two files itself and adds the button to every page.
4. Settings live in `user/config/plugins/runwrk-connect.yaml` (`enabled`, `key`, `api_url`, `position`).

## Wix, Squarespace, Shopify
These do not allow the two files at the main folder, so the button cannot run. Give the customer their **app page** link/QR (Connect website, section 1) and put it on their site as a normal button.

## Troubleshooting
| Symptom | Likely cause |
|---|---|
| No button | Website address not on the allowed list (check the browser Network tab for `403 origin_not_allowed`), wrong key, or the script line is missing |
| Button shows but "Turn on notifications" does nothing | Site is not on HTTPS, or the browser blocked notifications (the panel says so) |
| No "Install" option | The two files are missing or in the wrong folder; open both addresses directly |
| iPhone shows steps instead of a button | Normal: iPhones must add the app to the home screen first |
| Moved to a new domain | Add the new address in Super admin. Existing subscribers on the old address stay with the old address |

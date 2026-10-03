# Runwrk Connect (Grav plugin)

Adds your Runwrk app to a Grav website. Visitors see a small **Get our app** button where they can install the app and turn notifications on or off.

## Install
1. Copy this folder to `user/plugins/runwrk-connect` (the folder name must be exactly that).
2. Open `user/config/plugins/runwrk-connect.yaml` (or Admin → Plugins → Runwrk Connect) and paste your key:
   ```yaml
   enabled: true
   key: 'pk_...'
   api_url: 'https://runwrk.com'
   position: right
   ```
3. Clear the Grav cache.
4. Visit your website. The button appears at the bottom corner.

Your website's address must be on your Runwrk allowed list. If the button does not show, ask Runwrk to add your address.

## What it does
- Serves `/runwrk-sw.js` and `/runwrk-manifest.webmanifest` from your own address (browsers require this).
- Adds the manifest link and the button script to every page. Does nothing in the admin or when the key is empty.
- Needs HTTPS in production (browsers only allow notifications on secure sites).

## Remove
Disable or delete the plugin. Customers who already turned notifications on stay subscribed until they remove the app.

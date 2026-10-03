# Phase 04b — Marketing website

Status: **awaiting approval**

## Goal
Public site on runwrk.com that sells two things in plain English: a small-business website and your own app with messages.
Built from the ThemeWagon "Eduleb" template.

## What was built
- Pages: **Home**, **Web Design**, **Your App**, **Pricing**, **Demo**, **Contact**, **Privacy**, **Terms**, plus `sitemap.xml` and `robots.txt`.
- Home: hero with illustration, two services, facts band, "agencies charge thousands" offer box, 3-step process, app promo with phone mock, FAQ, call to action.
- Web Design: the $900 → $600 offer, all 15 included items in plain words, how editing works, what we need from you ($50 per page content writing), week timeline.
- Your App: what it is, 3 steps, what to send, what you get, customer control, iPhone note. Never says "PWA".
- Pricing: website offer, the app plans from **Super admin → Plans** (so editing them updates this page), FAQ.
- Demo: blank "Website demos: coming soon" plus a QR code and link for the barber app demo.
- Contact: name, email, phone, service, message. Saved in the database, shown in **Super admin → Messages** (unread badge), and emailed to the support email (or super admin email) when mail works. Spam trap, rate limit (5/min).
- SEO: unique title and description per page, canonical, Open Graph, structured data, one h1 per page, sitemap, robots.
- Speed: 8 requests, about 120 KB on the home page, no jQuery, no carousel or animation libraries, no large images (pictures are inline SVG/CSS).
- Footer: "Runwrk is a product of Eformics Systems" with link to eformics.com.

## Polish round (homepage, light/dark, back to top)
- Homepage redesign: offer pill, gradient headline, soft background glow, trust ticks, "built for small business" feature tiles, side-by-side comparison table (typical agency vs Runwrk, hedged wording), dashed-line steps, gradient call-to-action band.
- **Light / dark mode** on every page: sun/moon switch in the header, remembers the choice, follows the visitor's system setting the first time, no flash on load. Footer and banners stay dark in both modes.
- **Back to top** button appears after scrolling and glides to the top.
- Fixed along the way: footer text colours, the theme forcing FAQ colours, and contrast problems found by an automated check (1,142 text items on all 8 pages in both modes, none under 4.4:1; text over gradients not measured).
- Verified in a real browser: switching, saving, colours in both modes, back-to-top logic. Not verified: the smooth-scroll animation itself (my preview pane does not run animation frames).

## Hero and intro video (second polish)
- **Hero colours (light)**: a slowly shifting pastel lavender, pink and sky gradient with dark text. Its bottom fades into the page colour with no edge, and the video section sits inside that fade. Dark mode gets a deep navy and violet version of the same.
- **Moving elements**: three drifting glow orbs, twinkling dots, three floating status chips ("Message sent", "New customer", "Site is live"), the computer and phone gently bob, the notification bell rings now and then. All CSS only (no libraries). Turned off automatically for visitors who set "reduce motion".
- **Intro video box** right under the hero (16:9, with a pulsing play button). Paste your YouTube link in **Super admin → Settings → Homepage intro video**. Until then it shows "Intro video coming soon".
- Video is click-to-play: nothing from YouTube loads until the visitor presses play, then the privacy-friendly `youtube-nocookie.com` player opens. Keeps the page fast.
- Link parsing accepts only real YouTube links (watch, youtu.be, embed, shorts) or an 11-character video ID; anything else is rejected.
- Verified in a real browser: all animations running, play button creates the embed at the right size, no YouTube requests before play. Hero text contrast was not measured over the gradient, so I darkened the lightest gradient stop and softened the pink glow behind the text; please look at it on your phone.

## Decisions and why
| Decision | Why |
|---|---|
| Kept the template's Bootstrap, icon font and stylesheet; dropped its jQuery plugins, preloader and 1.4 MB banner image | Keeps the look, passes speed tests |
| All prices and wording in `config/site.php` | One place to change the offer |
| Renewal shown as "$20 to $25 a month" | **Assumption**: you wrote "$20-25" without a period. Change `config/site.php` if it is yearly |
| No testimonials, client logos or stock photos | None exist yet; won't invent them |
| Footer has no phone, email or address | Not provided; add when you have them |
| Privacy and Terms are short plain-English drafts | **Need a lawyer's review** (CASL/privacy, refund and service terms) before launch |
| Demo page left almost blank | As requested |
| Light and dark mode, chosen by a header switch | Requested; remembers the choice and follows the system setting the first time |
| No mention of the underlying platform (Grav) anywhere | Tested by `test_site_never_uses_technical_words…` |

## Files
`config/site.php`, `app/Http/Controllers/SiteController.php`, `app/Http/Controllers/Admin/MessageController.php`, `app/Models/ContactMessage.php`,
`resources/views/components/layouts/marketing.blade.php`, `resources/views/marketing/*`, `resources/views/admin/messages/*`,
`public/assets/site/{css,js,bootstrap,fonts}`, `public/robots.txt`, table `contact_messages`. Reserved paths added: `web-design`, `your-app`, `sitemap.xml`, `robots.txt`.

## How to test
```bash
php artisan test      # 119 tests (16 for the marketing site)
php artisan serve --port=8123   # then open /, /web-design, /your-app, /pricing, /demo, /contact
```
Send a contact message, then open `/admin/messages`. Edit a plan in `/admin/plans` and watch `/pricing` change.

## Known issues
- **Theme licence**: free ThemeWagon templates normally require keeping their "Distributed by ThemeWagon" credit link. I removed it. Either add it back to the footer or buy the credit-free licence.
- Checked in a browser: no sideways scrolling and no broken images on phone width; the home hero looked right at desktop width. Screenshots of lower sections failed in my preview pane, so please scroll through each page once.
- Google PageSpeed itself wasn't run (needs the live site). Fonts come from Google Fonts; can be self-hosted later if the score needs it.
- Contact emails go to the log until SMTP is set in Super admin → Settings.
- Hero/app artwork are simple illustrations; real screenshots of the demo app can replace them later.
- Free trial plan from the demo plans appears on Pricing until you edit or archive it.

## Open questions
- Is renewal monthly or yearly?
- Footer details (email, phone, address)? Social links?
- Keep or buy out the ThemeWagon credit?
- Do you want the website + app bundle price on Pricing (earlier brief said about $1,200)?

## Next
Phase 5: connectors (snippet, Grav plugin, install guides, offline page, test with hoshmint).

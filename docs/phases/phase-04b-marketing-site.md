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
| Light mode only | Dark mode is for customer sites |
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

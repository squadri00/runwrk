<?php

namespace Tests\Feature;

use App\Domain\Ops\Settings;
use App\Mail\InviteMail;
use App\Models\ContactMessage;
use App\Models\Plan;
use App\Models\Superadmin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MarketingSiteTest extends TestCase
{
    use RefreshDatabase;

    private const PAGES = ['/', '/web-design', '/your-app', '/pricing', '/demo', '/contact', '/privacy', '/terms'];

    protected function setUp(): void
    {
        parent::setUp();

        Plan::create(['name' => 'Starter', 'code' => 'starter', 'price_cents' => 2900, 'features' => ['Your own app', 'Up to 500 customers'], 'description' => 'For one shop']);
    }

    public function test_every_page_renders_with_seo_basics(): void
    {
        $titles = [];

        foreach (self::PAGES as $path) {
            $html = $this->get($path)->assertOk()->getContent();

            $this->assertMatchesRegularExpression('#<title>(.+?)</title>#', $html, $path);
            preg_match('#<title>(.+?)</title>#', $html, $t);
            $titles[] = $t[1];

            $this->assertMatchesRegularExpression('#<meta name="description" content="([^"]{40,170})">#', $html, "$path description length");
            $this->assertStringContainsString('<link rel="canonical"', $html, $path);
            $this->assertStringContainsString('property="og:title"', $html, $path);
            preg_match('#<script type="application/ld\+json">(.+?)</script>#s', $html, $ld);
            $this->assertSame('https://schema.org', json_decode($ld[1], true)['@context'] ?? null, "$path structured data");
            $this->assertStringNotContainsString('<?php', $html, $path);
            $this->assertSame(1, substr_count($html, '<h1'), "$path should have exactly one h1");
        }

        $this->assertCount(count(self::PAGES), array_unique($titles), 'page titles must be unique');
    }

    public function test_site_never_uses_technical_words_customers_should_not_see(): void
    {
        foreach (self::PAGES as $path) {
            $text = strip_tags(preg_replace('#<(script|style)\b.*?</\1>#s', '', $this->get($path)->getContent()));

            foreach (['PWA', 'progressive web', 'Grav', 'flat-file', 'flat file', 'Laravel', 'VAPID'] as $word) {
                $this->assertStringNotContainsStringIgnoringCase($word, $text, "$path mentions '$word'");
            }
        }
    }

    public function test_website_offer_comes_from_config(): void
    {
        $this->get('/web-design')->assertSee('$600')->assertSee('$900')->assertSee('$20 to $25 a month')->assertSee('$50 per page');

        config(['site.website.price' => 495]);
        $this->get('/web-design')->assertSee('$495')->assertDontSee('$600');
    }

    public function test_pricing_shows_the_website_offer_and_the_editable_app_plans(): void
    {
        $this->get('/pricing')->assertOk()->assertSee('$600')->assertSee('Starter')->assertSee('$29')->assertSee('Up to 500 customers');

        Plan::where('code', 'starter')->update(['price_cents' => 4900, 'name' => 'Real Plan']);
        $this->get('/pricing')->assertSee('Real Plan')->assertSee('$49')->assertDontSee('Starter');
    }

    public function test_pricing_plan_buttons_follow_the_signup_switch(): void
    {
        $this->get('/pricing')->assertSee('register?plan=starter', false);

        Settings::put('signups_enabled', false);
        \App\Domain\Ops\PlatformSettings::forget();
        $this->get('/pricing')->assertDontSee('register?plan=starter', false);
    }

    public function test_demo_page_is_mostly_blank_with_the_app_qr(): void
    {
        $this->get('/demo')->assertOk()->assertSee('Coming soon')->assertSee('<svg', false)->assertSee('/demo-barber', false);
    }

    public function test_contact_form_stores_the_message_and_tells_the_owner(): void
    {
        Mail::fake();
        Superadmin::create(['name' => 'Boss', 'email' => 'boss@runwrk.test', 'password' => 'secret-pass']);

        $this->post('/contact', ['name' => 'Joe', 'email' => 'joe@example.com', 'phone' => '416-555-0100', 'service' => 'both', 'message' => 'I run a barber shop and need a site.'])
            ->assertRedirect('/contact')->assertSessionHas('status');

        $m = ContactMessage::firstOrFail();
        $this->assertSame(['Joe', 'joe@example.com', 'both'], [$m->name, $m->email, $m->service]);
        $this->assertNull($m->read_at);
        $this->assertNotNull($m->ip);
    }

    public function test_contact_form_validation(): void
    {
        $this->post('/contact', [])->assertSessionHasErrors(['name', 'email', 'service', 'message']);
        $this->post('/contact', ['name' => 'J', 'email' => 'nope', 'service' => 'hack', 'message' => 'hi'])->assertSessionHasErrors(['email', 'service', 'message']);
        $this->post('/contact', ['name' => 'J', 'email' => 'j@example.com', 'service' => 'app', 'message' => str_repeat('x', 3001)])->assertSessionHasErrors('message');

        $this->assertSame(0, ContactMessage::count());
    }

    public function test_spam_trap_is_silently_ignored(): void
    {
        $this->post('/contact', ['name' => 'Bot', 'email' => 'bot@example.com', 'service' => 'app', 'message' => 'Buy cheap things now', 'company_site' => 'http://spam.example'])->assertRedirect('/contact');

        $this->assertSame(0, ContactMessage::count());
    }

    public function test_a_broken_mailer_never_loses_the_message(): void
    {
        Superadmin::create(['name' => 'Boss', 'email' => 'boss@runwrk.test', 'password' => 'secret-pass']);
        config(['mail.default' => 'smtp', 'mail.mailers.smtp.host' => '127.0.0.1', 'mail.mailers.smtp.port' => 1]);

        $this->post('/contact', ['name' => 'Joe', 'email' => 'joe@example.com', 'service' => 'app', 'message' => 'Please call me back soon.'])->assertSessionHas('status');

        $this->assertSame(1, ContactMessage::count());
    }

    public function test_contact_form_is_rate_limited(): void
    {
        $data = ['name' => 'Joe', 'email' => 'joe@example.com', 'service' => 'app', 'message' => 'Hello there, call me.'];

        foreach (range(1, 5) as $i) {
            $this->post('/contact', $data)->assertRedirect('/contact');
        }
        $this->post('/contact', $data)->assertStatus(429);
    }

    public function test_admin_sees_messages_and_reading_marks_them_read(): void
    {
        $admin = Superadmin::create(['name' => 'Boss', 'email' => 'boss@runwrk.test', 'password' => 'secret-pass']);
        $m = ContactMessage::create(['name' => 'Joe', 'email' => 'joe@example.com', 'service' => 'website', 'message' => 'Hello there!']);

        $this->get('/admin/messages')->assertRedirect('/admin/login');

        $this->actingAs($admin, 'superadmin')->get('/admin/messages')->assertOk()->assertSee('Joe');
        $this->get('/admin')->assertSee('rounded-full bg-rose-600', false);

        $this->get("/admin/messages/{$m->id}")->assertOk()->assertSee('Hello there!');
        $this->assertNotNull($m->fresh()->read_at);

        $this->delete("/admin/messages/{$m->id}")->assertRedirect('/admin/messages');
        $this->assertSame(0, ContactMessage::count());
    }

    public function test_sitemap_and_robots(): void
    {
        $xml = $this->get('/sitemap.xml')->assertOk()->getContent();
        $this->assertStringStartsWith('<?xml', trim($xml));
        foreach (['/web-design', '/your-app', '/pricing', '/demo', '/contact'] as $p) {
            $this->assertStringContainsString($p, $xml);
        }
        $this->assertStringNotContainsString('/admin', $xml);

        $robots = file_get_contents(public_path('robots.txt'));
        $this->assertStringContainsString('Disallow: /admin', $robots);
        $this->assertStringContainsString('Sitemap:', $robots);
    }

    public function test_site_is_lean_no_jquery_and_theme_assets_exist(): void
    {
        $html = $this->get('/')->getContent();

        $this->assertStringNotContainsString('jquery', strtolower($html));
        $this->assertStringContainsString('/assets/site/css/runwrk-site.css', $html);
        foreach (['css/style.css', 'css/runwrk-site.css', 'js/site.js', 'bootstrap/bootstrap.min.css', 'fonts/themify-icons.css'] as $asset) {
            $this->assertFileExists(public_path('assets/site/'.$asset));
        }
        $this->assertLessThan(300_000, filesize(public_path('assets/site/css/style.css')) + filesize(public_path('assets/site/css/runwrk-site.css')));
    }

    public function test_images_are_not_oversized(): void
    {
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(public_path('assets/site')));

        foreach ($files as $f) {
            if ($f->isFile() && preg_match('/\.(jpe?g|png|gif)$/i', $f->getFilename())) {
                $this->assertLessThan(150_000, $f->getSize(), $f->getFilename().' is too big for a fast site');
            }
        }
    }

    public function test_homepage_video_box_shows_a_placeholder_until_a_youtube_link_is_set(): void
    {
        $this->get('/')->assertSee('Intro video coming soon')->assertDontSee('data-video', false);

        Settings::put('intro_video_url', 'https://youtu.be/dQw4w9WgXcQ?si=x');
        \App\Domain\Ops\PlatformSettings::forget();

        $html = $this->get('/')->assertSee('data-video="dQw4w9WgXcQ"', false)->assertDontSee('coming soon')->getContent();
        $this->assertStringNotContainsString('<iframe', $html, 'YouTube must not load until play is pressed');
        $this->assertStringNotContainsString('youtube.com/embed', $html);
        $this->assertStringContainsString('youtube-nocookie.com/embed/', file_get_contents(public_path('assets/site/js/site.js')));
    }

    public function test_video_section_comes_right_after_the_hero(): void
    {
        $html = $this->get('/')->getContent();

        $this->assertLessThan(strpos($html, 'rw-video-section'), strpos($html, 'class="rw-hero"'));
        $this->assertLessThan(strpos($html, 'class="rw-section"'), strpos($html, 'rw-video-section'));
    }

    public function test_youtube_links_are_parsed_strictly(): void
    {
        $ok = ['dQw4w9WgXcQ', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ&t=5s', 'https://youtu.be/dQw4w9WgXcQ', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'https://youtube.com/shorts/dQw4w9WgXcQ', 'https://m.youtube.com/watch?v=dQw4w9WgXcQ'];
        foreach ($ok as $u) {
            $this->assertSame('dQw4w9WgXcQ', \App\Domain\Ops\PlatformSettings::youtubeId($u), $u);
        }
        foreach (['https://evil.com/watch?v=dQw4w9WgXcQ', 'https://youtube.com.evil.com/watch?v=dQw4w9WgXcQ', 'https://www.youtube.com/', 'javascript:alert(1)', '', '<script>', 'http://youtube.com/watch?v=short'] as $u) {
            $this->assertNull(\App\Domain\Ops\PlatformSettings::youtubeId($u), $u);
        }
    }

    public function test_admin_can_set_and_clear_the_video_link_and_bad_links_are_rejected(): void
    {
        $admin = Superadmin::create(['name' => 'Boss', 'email' => 'boss@runwrk.test', 'password' => 'secret-pass']);
        $this->actingAs($admin, 'superadmin');

        $this->put('/admin/settings', ['intro_video_url' => 'https://example.com/video.mp4'])->assertSessionHasErrors('intro_video_url');

        $this->put('/admin/settings', ['intro_video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'])->assertSessionHasNoErrors();
        $this->get('/')->assertSee('data-video="dQw4w9WgXcQ"', false);

        $this->put('/admin/settings', ['intro_video_url' => ''])->assertSessionHasNoErrors();
        $this->get('/')->assertSee('Intro video coming soon');
    }

    public function test_hero_motion_respects_reduced_motion_and_uses_cheap_animations(): void
    {
        $css = file_get_contents(public_path('assets/site/css/runwrk-site.css'));

        $this->assertStringContainsString('prefers-reduced-motion: reduce', $css);
        $this->assertStringContainsString('@keyframes rwShift', $css);
        $this->assertStringContainsString('@keyframes rwFloat1', $css);
        $html = $this->get('/')->getContent();
        $this->assertSame(3, substr_count($html, 'class="rw-float '));
        $this->assertSame(3, substr_count($html, 'class="rw-orb '));
    }

    public function test_every_page_has_the_theme_switch_back_to_top_and_no_flash_script(): void
    {
        foreach (self::PAGES as $path) {
            $html = $this->get($path)->getContent();

            $this->assertStringContainsString('class="rw-theme"', $html, $path);
            $this->assertStringContainsString('class="rw-top"', $html, $path);
            $this->assertStringContainsString("localStorage.getItem('rw-site-theme')", $html, $path);
            $this->assertLessThan(strpos($html, 'rel="stylesheet"'), strpos($html, "setAttribute('data-theme'"), "$path theme must be set before CSS loads");
        }

        $css = file_get_contents(public_path('assets/site/css/runwrk-site.css'));
        $this->assertStringContainsString('html[data-theme="dark"]', $css);
        $js = file_get_contents(public_path('assets/site/js/site.js'));
        foreach (['rw-site-theme', '.rw-top', 'scrollTo'] as $needle) {
            $this->assertStringContainsString($needle, $js);
        }
    }

    public function test_navigation_links_every_page_and_signed_in_owners_see_their_dashboard(): void
    {
        $html = $this->get('/')->getContent();
        foreach (['/web-design', '/your-app', '/pricing', '/demo', '/contact', '/login'] as $p) {
            $this->assertStringContainsString('href="'.url($p).'"', $html);
        }

        $user = \App\Models\User::factory()->create();
        $this->actingAs($user)->get('/')->assertSee('My dashboard');
    }
}

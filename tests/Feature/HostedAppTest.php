<?php

namespace Tests\Feature;

use App\Models\Business;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HostedAppTest extends TestCase
{
    use RefreshDatabase;

    private Business $b;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->b = Business::factory()->create([
            'name' => 'Joes Barber', 'slug' => 'joes-barber', 'short_name' => 'Joes', 'theme_color' => '#112233', 'background_color' => '#ffffff',
            'phone' => '+1 (416) 555-0100', 'address' => '12 King St, Toronto',
        ]);
    }

    public function test_page_shows_the_business_and_links_the_manifest(): void
    {
        $this->get('/joes-barber')->assertOk()
            ->assertSee('Joes Barber')
            ->assertSee('/joes-barber/manifest.webmanifest', false)
            ->assertSee('tel:+14165550100', false)
            ->assertSee('google.com/maps/search', false)
            ->assertSee('Turn on notifications')
            ->assertSee('pk_', false);

        $this->get('/joes-barber/')->assertOk();
    }

    public function test_page_never_exposes_the_private_key(): void
    {
        config(['runwrk.vapid.private' => 'TOP-SECRET-VAPID']);

        $this->assertStringNotContainsString('TOP-SECRET-VAPID', $this->get('/joes-barber')->getContent());
    }

    public function test_unknown_suspended_and_reserved_slugs_are_404(): void
    {
        $this->get('/nobody-here')->assertNotFound();
        $this->get('/terms')->assertNotFound();

        $this->b->update(['status' => 'suspended']);
        foreach (['/joes-barber', '/joes-barber/manifest.webmanifest', '/joes-barber/sw.js', '/joes-barber/icons/icon-192.png'] as $url) {
            $this->get($url)->assertNotFound();
        }
    }

    public function test_manifest_is_scoped_to_the_business_and_installable(): void
    {
        $res = $this->get('/joes-barber/manifest.webmanifest')->assertOk();
        $m = $res->json();

        $this->assertStringContainsString('manifest+json', $res->headers->get('Content-Type'));
        $this->assertSame('Joes Barber', $m['name']);
        $this->assertSame('Joes', $m['short_name']);
        $this->assertSame('/joes-barber/', $m['scope']);
        $this->assertSame('/joes-barber/', $m['start_url']);
        $this->assertSame('standalone', $m['display']);
        $this->assertSame('#112233', $m['theme_color']);
        $this->assertEqualsCanonicalizing(['192x192', '512x512', '512x512'], array_column($m['icons'], 'sizes'));
        $this->assertContains('maskable', array_column($m['icons'], 'purpose'));
    }

    public function test_service_worker_loads_the_shared_core(): void
    {
        $res = $this->get('/joes-barber/sw.js')->assertOk();

        $this->assertStringContainsString('javascript', $res->headers->get('Content-Type'));
        $this->assertStringContainsString("importScripts('/assets/sw-core.js')", $res->getContent());
        $this->assertFileExists(public_path('assets/sw-core.js'));
        $this->assertStringContainsString("addEventListener('push'", file_get_contents(public_path('assets/sw-core.js')));
        $this->assertStringContainsString("addEventListener('notificationclick'", file_get_contents(public_path('assets/sw-core.js')));
    }

    public function test_icons_fall_back_to_the_default_and_use_the_business_icon_when_present(): void
    {
        $default = $this->get('/joes-barber/icons/icon-192.png')->assertOk();
        $this->assertStringContainsString('image/png', $default->headers->get('Content-Type'));

        $this->get('/joes-barber/icons/evil.php')->assertNotFound();
        $this->get('/joes-barber/icons/..%2F..%2F.env')->assertNotFound();

        Storage::disk('public')->put("businesses/{$this->b->id}/icons/icon-192.png", 'CUSTOM-ICON');
        $this->b->update(['icon_path' => "businesses/{$this->b->id}/icons"]);

        $this->assertSame('CUSTOM-ICON', $this->get('/joes-barber/icons/icon-192.png')->streamedContent());
    }

    public function test_default_icon_files_exist_in_the_required_sizes(): void
    {
        foreach (['default-192.png' => 192, 'default-512.png' => 512, 'default-maskable-512.png' => 512, 'default-apple-180.png' => 180] as $file => $size) {
            $this->assertSame($size, getimagesize(public_path('assets/icons/'.$file))[0], $file);
        }
        $this->assertFileExists(public_path('assets/icons/badge.png'));
    }
}

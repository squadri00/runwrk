<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use ZipArchive;

class ConnectorTest extends TestCase
{
    use RefreshDatabase;

    private Business $a;

    private Business $b;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.url' => 'https://runwrk.example', 'runwrk.allow_local_origins' => false]);

        $this->a = Business::factory()->create(['name' => 'Joes Barber', 'slug' => 'joes', 'short_name' => 'Joes', 'theme_color' => '#112233', 'background_color' => '#ffeedd']);
        $this->a->domains()->create(['domain' => 'joes.com']);
        $this->a->domains()->create(['domain' => '*.joes.org']);
        $this->b = Business::factory()->create(['name' => 'Beta Salon', 'slug' => 'beta']);
        $this->b->domains()->create(['domain' => 'beta.com']);
        $this->owner = User::factory()->create(['business_id' => $this->a->id, 'role' => 'owner']);
    }

    private function api(Business $b, string $path, array $query = [])
    {
        return $this->getJson('/api/v1/'.$path.($query ? '?'.http_build_query($query) : ''), ['X-Runwrk-Key' => $b->public_key]);
    }

    public function test_config_gives_the_connect_script_what_it_needs(): void
    {
        $this->api($this->a, 'config')->assertOk()->assertJson([
            'name' => 'Joes Barber', 'shortName' => 'Joes', 'themeColor' => '#112233', 'backgroundColor' => '#ffeedd',
            'icon' => 'https://runwrk.example/joes/icons/icon-192.png', 'appUrl' => 'https://runwrk.example/joes',
        ]);
    }

    public function test_manifest_is_built_for_the_websites_own_address(): void
    {
        $res = $this->api($this->a, 'manifest', ['site' => 'https://joes.com'])->assertOk();
        $m = $res->json();

        $this->assertStringContainsString('manifest+json', $res->headers->get('Content-Type'));
        $this->assertSame(['/', '/', '/', 'standalone', 'Joes Barber', '#112233'], [$m['id'], $m['start_url'], $m['scope'], $m['display'], $m['name'], $m['theme_color']]);
        $this->assertCount(3, $m['icons']);
        foreach ($m['icons'] as $icon) {
            $this->assertStringStartsWith('https://runwrk.example/joes/icons/', $icon['src']);
        }
        $this->assertContains('maskable', array_column($m['icons'], 'purpose'));
    }

    public function test_manifest_for_a_site_in_a_subfolder_is_scoped_to_it(): void
    {
        $m = $this->api($this->a, 'manifest', ['site' => 'https://joes.com/shop'])->json();

        $this->assertSame(['/shop/', '/shop/'], [$m['start_url'], $m['scope']]);
        $this->assertSame('/shop/', $this->api($this->a, 'manifest', ['site' => 'https://sub.joes.org/shop/'])->json('scope'));
    }

    public function test_manifest_refuses_sites_the_business_does_not_own(): void
    {
        foreach (['https://beta.com', 'https://evil.example', 'https://joes.com.evil.example', 'ftp://joes.com', 'javascript:alert(1)', '', 'joes.com'] as $site) {
            $this->api($this->a, 'manifest', ['site' => $site])->assertStatus(403);
        }
        $this->api($this->a, 'manifest')->assertStatus(403);
        $this->getJson('/api/v1/manifest?site=https://joes.com', ['X-Runwrk-Key' => 'pk_nope'])->assertStatus(401);
    }

    public function test_manifest_never_mixes_in_another_business(): void
    {
        $json = $this->api($this->b, 'manifest', ['site' => 'https://beta.com'])->getContent();

        $this->assertStringContainsString('Beta Salon', $json);
        $this->assertStringNotContainsString('Joes', $json);
        $this->assertStringContainsString('/beta/icons/', str_replace('\/', '/', $json));
    }

    public function test_connect_page_requires_login_and_shows_only_this_businesss_key(): void
    {
        $this->get('/dashboard/connect')->assertRedirect('/login');

        $html = $this->actingAs($this->owner)->get('/dashboard/connect')->assertOk()
            ->assertSee('runwrk-connect.js', false)
            ->assertSee($this->a->public_key)
            ->assertSee('https://runwrk.example/joes')
            ->assertSee('https://joes.com')
            ->assertSee('<svg', false)
            ->getContent();

        $this->assertStringNotContainsString($this->b->public_key, $html);
        $this->assertStringNotContainsString('beta.com', $html);
        $this->assertStringNotContainsString('*.joes.org/runwrk', $html);
    }

    public function test_staff_can_open_the_connect_page_too(): void
    {
        $staff = User::factory()->create(['business_id' => $this->a->id, 'role' => 'staff']);

        $this->actingAs($staff)->get('/dashboard/connect')->assertOk();
    }

    public function test_page_asks_to_link_a_website_when_none_is_set(): void
    {
        $this->a->domains()->delete();

        $this->actingAs($this->owner)->get('/dashboard/connect')->assertOk()->assertSee('not linked your website yet');
        $this->get('/dashboard/connect/runwrk-manifest.webmanifest')->assertStatus(422);

        $this->a->update(['website_url' => 'https://joesbarber.ca/about']);
        $this->owner->unsetRelation('business');
        $this->get('/dashboard/connect')->assertSee('https://joesbarber.ca')->assertDontSee('not linked your website yet');
    }

    public function test_downloads_contain_the_right_content(): void
    {
        $this->actingAs($this->owner);

        $sw = $this->get('/dashboard/connect/runwrk-sw.js')->assertOk();
        $this->assertSame("importScripts('https://runwrk.example/assets/sw-core.js');\n", $sw->getContent());
        $this->assertStringContainsString('attachment; filename="runwrk-sw.js"', $sw->headers->get('Content-Disposition'));

        $manifest = $this->get('/dashboard/connect/runwrk-manifest.webmanifest?site=https://joes.com')->assertOk();
        $this->assertSame('Joes Barber', json_decode($manifest->getContent(), true)['name']);
        $this->assertStringContainsString('runwrk-manifest.webmanifest', $manifest->headers->get('Content-Disposition'));
    }

    public function test_manifest_download_ignores_a_site_that_is_not_in_the_list(): void
    {
        $this->actingAs($this->owner)->get('/dashboard/connect/runwrk-manifest.webmanifest?site=https://beta.com')->assertOk();

        $m = json_decode($this->get('/dashboard/connect/runwrk-manifest.webmanifest?site=https://beta.com')->getContent(), true);
        $this->assertSame('Joes Barber', $m['name']);
    }

    public function test_grav_plugin_zip_is_prefilled_with_this_businesss_key_only(): void
    {
        $response = $this->actingAs($this->owner)->get('/dashboard/connect/runwrk-connect.zip')->assertOk();
        $tmp = tempnam(sys_get_temp_dir(), 'z');
        file_put_contents($tmp, $response->baseResponse->getFile()->getContent());

        $zip = new ZipArchive;
        $this->assertTrue($zip->open($tmp) === true);
        $names = array_map(fn ($i) => $zip->getNameIndex($i), range(0, $zip->numFiles - 1));
        foreach (['runwrk-connect/runwrk-connect.php', 'runwrk-connect/runwrk-connect.yaml', 'runwrk-connect/blueprints.yaml', 'runwrk-connect/README.md'] as $file) {
            $this->assertContains($file, $names);
        }

        $yaml = $zip->getFromName('runwrk-connect/runwrk-connect.yaml');
        $this->assertStringContainsString("key: '{$this->a->public_key}'", $yaml);
        $this->assertStringContainsString("api_url: 'https://runwrk.example'", $yaml);
        $this->assertStringNotContainsString($this->b->public_key, $yaml);
        $zip->close();
        @unlink($tmp);
    }

    public function test_grav_plugin_source_is_valid_php(): void
    {
        $file = base_path('integrations/grav/runwrk-connect/runwrk-connect.php');
        exec(escapeshellarg(PHP_BINARY).' -l '.escapeshellarg($file).' 2>&1', $out, $code);

        $this->assertSame(0, $code, implode("\n", $out));
        $this->assertStringContainsString('/runwrk-sw.js', file_get_contents($file));
    }

    public function test_service_worker_only_touches_page_navigations_for_offline(): void
    {
        $sw = file_get_contents(public_path('assets/sw-core.js'));

        $this->assertStringContainsString("event.request.mode !== 'navigate'", $sw);
        $this->assertStringContainsString('You are offline', $sw);
        $this->assertStringContainsString("addEventListener('push'", $sw);
        $this->assertStringContainsString("addEventListener('notificationclick'", $sw);
    }

    public function test_service_worker_behaves_correctly_when_run_for_real(): void
    {
        exec('node --version 2>&1', $o, $code);
        if ($code !== 0) {
            $this->markTestSkipped('node is not installed');
        }

        exec('node '.escapeshellarg(base_path('tests/Support/sw-sim.cjs')).' '.escapeshellarg(public_path('assets/sw-core.js')).' 2>&1', $out, $c);
        $r = json_decode(implode("
", $out), true);

        $this->assertSame(0, $c, implode("
", $out));
        $this->assertTrue($r['nonNavigateIgnored'], 'images, scripts and API calls must pass straight through');
        $this->assertSame(200, $r['onlineNavigate']);
        $this->assertSame(503, $r['offlineStatus']);
        $this->assertTrue($r['offlineHtml']);
        $this->assertSame(['Flash sale', 'Half price', 'm7', '/sale'], array_values($r['pushShown']));
        $this->assertTrue($r['badPayloadStillNotifies']);
        $this->assertSame('https://joes.com/sale', $r['click']['openedUrl']);
        $this->assertTrue($r['click']['reported'] && $r['click']['keepalive'] && $r['click']['hasKeyHeader']);
        $this->assertSame('{"message":7}', $r['click']['body']);
    }

    public function test_notifications_attach_to_runwrks_own_worker_even_when_the_website_has_one(): void
    {
        exec('node --version 2>&1', $o, $code);
        if ($code !== 0) {
            $this->markTestSkipped('node is not installed');
        }

        foreach (['active-now', 'installing-first'] as $mode) {
            $out = [];
            exec('node '.escapeshellarg(base_path('tests/Support/lib-sim.cjs')).' '.escapeshellarg(public_path('assets/runwrk.js')).' '.$mode.' 2>&1', $out, $c);
            $r = json_decode(end($out), true);

            $this->assertSame(0, $c, implode("
", $out));
            $this->assertSame('/runwrk-app/', $r['registeredScope'], $mode);
            $this->assertSame(['runwrk-worker'], $r['subscribedThrough'], "$mode: must subscribe through Runwrk\x27s worker, never the website\x27s own");
            $this->assertTrue($r['savedToRunwrk'], $mode);
        }
    }

    public function test_connect_script_can_run_in_notifications_only_mode(): void
    {
        $js = file_get_contents(public_path('assets/runwrk-connect.js'));

        $this->assertStringContainsString("data-install", $js);
        $this->assertStringContainsString("data-label", $js);
        $this->assertStringContainsString('showInstall', $js);
    }

    public function test_connect_script_is_small_isolated_and_fails_quietly(): void
    {
        $js = file_get_contents(public_path('assets/runwrk-connect.js'));

        $this->assertLessThan(16_000, strlen($js));
        $this->assertStringContainsString('attachShadow', $js, 'styles must not leak into the customer website');
        $this->assertStringContainsString("getAttribute('data-key')", $js);
        $this->assertStringContainsString('catch(function () {', str_replace('catch (function', 'catch(function', $js));
        $this->assertStringNotContainsStringIgnoringCase('jquery', $js);
        $this->assertStringNotContainsString('PWA', $js);
    }

    public function test_javascript_files_have_valid_syntax(): void
    {
        exec('node --version 2>&1', $out, $code);
        if ($code !== 0) {
            $this->markTestSkipped('node is not installed');
        }

        foreach (['runwrk-connect.js', 'runwrk.js', 'sw-core.js'] as $file) {
            exec('node --check '.escapeshellarg(public_path('assets/'.$file)).' 2>&1', $o, $c);
            $this->assertSame(0, $c, $file.': '.implode("\n", $o));
        }
    }

    public function test_customer_facing_copy_avoids_technical_words(): void
    {
        $text = strip_tags(preg_replace('#<(script|style)\b.*?</\1>#s', '', $this->actingAs($this->owner)->get('/dashboard/connect')->getContent()));

        foreach (['PWA', 'progressive web', 'VAPID', 'Laravel'] as $word) {
            $this->assertStringNotContainsStringIgnoringCase($word, $text);
        }
    }
}

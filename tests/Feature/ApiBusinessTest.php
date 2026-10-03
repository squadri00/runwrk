<?php

namespace Tests\Feature;

use App\Domain\Tenancy\CurrentBusiness;
use App\Models\Business;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ApiBusinessTest extends TestCase
{
    use RefreshDatabase;

    private Business $a;

    private Business $b;

    protected function setUp(): void
    {
        parent::setUp();

        config(['runwrk.allow_local_origins' => false]);

        $this->a = Business::factory()->create(['name' => 'Alpha']);
        $this->a->domains()->create(['domain' => 'alpha.com']);
        $this->a->domains()->create(['domain' => '*.alpha.org']);
        $this->b = Business::factory()->create(['name' => 'Beta']);
        $this->b->domains()->create(['domain' => 'beta.com']);
    }

    private function ping(Business $business, ?string $origin = null, string $method = 'GET')
    {
        $headers = ['X-Runwrk-Key' => $business->public_key] + ($origin ? ['Origin' => $origin] : []);

        return $this->call($method, '/api/v1/ping', [], [], [], $this->transformHeadersToServerVars($headers));
    }

    public function test_valid_key_and_allowed_origin(): void
    {
        $this->ping($this->a, 'https://alpha.com')
            ->assertOk()
            ->assertJson(['business' => 'Alpha'])
            ->assertHeader('Access-Control-Allow-Origin', 'https://alpha.com');
    }

    public function test_key_resolves_only_its_own_business(): void
    {
        $this->ping($this->b, 'https://beta.com')->assertJson(['business' => 'Beta']);
    }

    public function test_other_businesses_domain_is_rejected(): void
    {
        $this->ping($this->a, 'https://beta.com')->assertStatus(403)->assertHeaderMissing('Access-Control-Allow-Origin');
    }

    public function test_wildcard_domain(): void
    {
        $this->ping($this->a, 'https://shop.alpha.org')->assertOk();
        $this->ping($this->a, 'https://alpha.org')->assertOk();
        $this->ping($this->a, 'https://evilalpha.org')->assertStatus(403);
    }

    public function test_the_platforms_own_address_is_allowed_for_hosted_apps(): void
    {
        config(['app.url' => 'https://runwrk.example']);

        $this->ping($this->a, 'https://runwrk.example')->assertOk();
        $this->ping($this->a, 'https://runwrk.example.evil.com')->assertStatus(403);
        $this->ping($this->a, 'https://other.example')->assertStatus(403);
    }

    public function test_real_browser_preflight_has_no_key_and_still_succeeds(): void
    {
        $res = $this->call('OPTIONS', '/api/v1/subscribe', [], [], [], [
            'HTTP_ORIGIN' => 'https://alpha.com', 'HTTP_ACCESS_CONTROL_REQUEST_METHOD' => 'POST', 'HTTP_ACCESS_CONTROL_REQUEST_HEADERS' => 'content-type,x-runwrk-key',
        ]);

        $res->assertStatus(204)->assertHeader('Access-Control-Allow-Origin', 'https://alpha.com');
        $this->assertStringContainsString('X-Runwrk-Key', $res->headers->get('Access-Control-Allow-Headers'));
        $this->assertSame('', $res->getContent(), 'a preflight must reveal nothing');
    }

    public function test_the_real_request_after_a_preflight_is_still_checked(): void
    {
        // Preflight is open, but data is not: wrong origin or wrong key still fails, with no CORS header.
        $this->ping($this->a, 'https://evil.com')->assertStatus(403)->assertHeaderMissing('Access-Control-Allow-Origin');
        $this->call('GET', '/api/v1/config', [], [], [], ['HTTP_ORIGIN' => 'https://alpha.com', 'HTTP_X_RUNWRK_KEY' => 'pk_nope'])->assertStatus(401);
    }

    public function test_empty_allowed_list_rejects_browsers(): void
    {
        $c = Business::factory()->create();

        $this->ping($c, 'https://anything.com')->assertStatus(403);
    }

    public function test_localhost_allowed_only_when_enabled(): void
    {
        $this->ping($this->a, 'http://localhost:8000')->assertStatus(403);

        config(['runwrk.allow_local_origins' => true]);
        $this->ping($this->a, 'http://localhost:8000')->assertOk();
    }

    public function test_bad_missing_or_suspended_key(): void
    {
        $this->getJson('/api/v1/ping')->assertStatus(401);
        $this->getJson('/api/v1/ping?key=pk_nope')->assertStatus(401);

        $this->b->update(['status' => 'suspended']);
        $this->ping($this->b)->assertStatus(401);
    }

    public function test_request_without_origin_passes(): void
    {
        $this->ping($this->a)->assertOk();
    }

    public function test_preflight(): void
    {
        $this->ping($this->a, 'https://alpha.com', 'OPTIONS')
            ->assertStatus(204)
            ->assertHeader('Access-Control-Allow-Headers', 'Content-Type, X-Runwrk-Key');
    }

    public function test_dashboard_middleware_binds_the_users_business_and_blocks_suspended(): void
    {
        Route::middleware(['web', 'business'])->get('/_probe', fn (CurrentBusiness $c) => (string) $c->id());

        $user = User::factory()->create(['business_id' => $this->a->id]);

        $this->actingAs($user)->get('/_probe')->assertOk()->assertSee((string) $this->a->id);

        $this->a->update(['status' => 'suspended']);
        $this->actingAs($user->fresh())->get('/_probe')->assertForbidden();
    }
}

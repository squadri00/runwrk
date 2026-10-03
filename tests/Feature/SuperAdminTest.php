<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Business;
use App\Models\Plan;
use App\Models\Superadmin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminTest extends TestCase
{
    use RefreshDatabase;

    private Superadmin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Superadmin::create(['name' => 'Boss', 'email' => 'boss@runwrk.test', 'password' => 'secret-pass']);
    }

    private function asAdmin()
    {
        return $this->actingAs($this->admin, 'superadmin');
    }

    private function payload(array $over = []): array
    {
        return $over + [
            'name' => 'Joes Barber', 'slug' => 'joes-barber', 'owner_name' => 'Joe', 'owner_email' => 'joe@example.com',
            'status' => 'trial', 'plan_id' => Plan::firstOrCreate(['code' => 'app'], ['name' => 'Your App'])->id,
            'domains' => "joesbarber.com\nWWW.JoesBarber.com",
        ];
    }

    public function test_guests_are_sent_to_admin_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/admin/businesses')->assertRedirect('/admin/login');
    }

    public function test_business_owner_cannot_use_admin(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'web')->get('/admin/businesses')->assertRedirect('/admin/login');
    }

    public function test_login_and_bad_password(): void
    {
        $this->post('/admin/login', ['email' => 'boss@runwrk.test', 'password' => 'wrong'])->assertSessionHasErrors('email');
        $this->assertGuest('superadmin');

        $this->post('/admin/login', ['email' => 'boss@runwrk.test', 'password' => 'secret-pass'])->assertRedirect('/admin');
        $this->assertAuthenticated('superadmin');
        $this->assertDatabaseHas('audit_logs', ['action' => 'superadmin.login']);
    }

    public function test_create_business_with_owner_domains_and_audit(): void
    {
        $this->asAdmin()->post('/admin/businesses', $this->payload())->assertSessionHas('new_login');

        $business = Business::where('slug', 'joes-barber')->firstOrFail();
        $this->assertStringStartsWith('pk_', $business->public_key);
        $this->assertEqualsCanonicalizing(['joesbarber.com', 'www.joesbarber.com'], $business->domains()->pluck('domain')->all());

        $owner = User::where('email', 'joe@example.com')->firstOrFail();
        $this->assertSame($business->id, $owner->business_id);
        $this->assertSame('owner', $owner->role);
        $this->assertDatabaseHas('audit_logs', ['action' => 'business.create', 'business_id' => $business->id]);
    }

    public function test_reserved_duplicate_and_bad_input_are_rejected(): void
    {
        $this->asAdmin()->post('/admin/businesses', $this->payload(['slug' => 'admin']))->assertSessionHasErrors('slug');
        $this->asAdmin()->post('/admin/businesses', $this->payload(['domains' => 'not a domain!']))->assertSessionHasErrors('domains');

        User::factory()->create(['email' => 'joe@example.com']);
        $this->asAdmin()->post('/admin/businesses', $this->payload())->assertSessionHasErrors('owner_email');
    }

    public function test_update_changes_status_domains_and_logs_the_diff(): void
    {
        $business = Business::factory()->create(['name' => 'Old', 'status' => 'active']);
        $business->domains()->create(['domain' => 'old.com']);

        $this->asAdmin()->put("/admin/businesses/{$business->id}", [
            'name' => 'New', 'slug' => $business->slug, 'status' => 'suspended', 'theme_color' => '#112233',
            'background_color' => '#ffffff', 'timezone' => 'America/Toronto', 'domains' => 'new.com',
        ])->assertRedirect();

        $business->refresh();
        $this->assertSame('suspended', $business->status);
        $this->assertSame(['new.com'], $business->domains()->pluck('domain')->all());

        $log = AuditLog::where('action', 'business.update')->firstOrFail();
        $this->assertSame(['from' => 'active', 'to' => 'suspended'], $log->changes['status']);
    }

    public function test_regenerating_the_key_kills_the_old_one(): void
    {
        $business = Business::factory()->create();
        $old = $business->public_key;

        $this->asAdmin()->post("/admin/businesses/{$business->id}/key")->assertRedirect();

        $this->assertNotSame($old, $business->fresh()->public_key);
        $this->getJson('/api/v1/ping?key='.$old)->assertStatus(401);
    }

    public function test_suspended_business_is_blocked_everywhere(): void
    {
        $business = Business::factory()->create(['status' => 'suspended']);
        $user = User::factory()->create(['business_id' => $business->id]);

        $this->getJson('/api/v1/ping?key='.$business->public_key)->assertStatus(401);
        $this->actingAs($user, 'web')->get('/dashboard')->assertForbidden();
    }

    public function test_impersonation_start_banner_stop_and_audit(): void
    {
        $business = Business::factory()->create();
        $owner = User::factory()->create(['business_id' => $business->id]);
        Business::factory()->create();

        $this->asAdmin()->post("/admin/businesses/{$business->id}/impersonate")->assertRedirect('/dashboard');

        $this->get('/dashboard')->assertOk()->assertSee($business->name)->assertSee('Return to admin');
        $this->assertAuthenticatedAs($owner, 'web');

        $this->post('/dashboard/stop-impersonating')->assertRedirect('/admin/businesses');
        $this->assertGuest('web');
        $this->get('/dashboard')->assertRedirect('/login');

        $this->assertDatabaseHas('audit_logs', ['action' => 'impersonate.start', 'business_id' => $business->id]);
        $this->assertDatabaseHas('audit_logs', ['action' => 'impersonate.stop', 'business_id' => $business->id]);
    }

    public function test_actions_during_impersonation_are_stamped(): void
    {
        $business = Business::factory()->create();
        User::factory()->create(['business_id' => $business->id]);

        $this->asAdmin()->post("/admin/businesses/{$business->id}/impersonate");
        \App\Domain\Ops\Audit::log('test.action');

        $this->assertSame($this->admin->id, AuditLog::where('action', 'test.action')->value('impersonated_by'));
    }

    public function test_dashboard_requires_a_business_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->asAdmin()->get('/dashboard')->assertRedirect('/login');
    }

    public function test_pages_render(): void
    {
        $business = Business::factory()->create();
        User::factory()->create(['business_id' => $business->id]);

        foreach (['/admin', '/admin/businesses', '/admin/businesses/create', "/admin/businesses/{$business->id}", "/admin/businesses/{$business->id}/edit", '/admin/audit'] as $url) {
            $this->asAdmin()->get($url)->assertOk();
        }
    }
}

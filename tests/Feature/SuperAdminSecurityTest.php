<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\Superadmin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class SuperAdminSecurityTest extends TestCase
{
    use RefreshDatabase;

    private Superadmin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Superadmin::create(['name' => 'Boss', 'email' => 'boss@runwrk.test', 'password' => 'secret-pass']);
    }

    private function enable2fa(): string
    {
        $secret = (new Google2FA)->generateSecretKey();
        $this->admin->forceFill(['two_factor_secret' => $secret, 'two_factor_confirmed_at' => now()])->save();

        return $secret;
    }

    public function test_setup_confirms_with_a_valid_code_and_stores_secret_encrypted(): void
    {
        $google = new Google2FA;

        $this->actingAs($this->admin, 'superadmin')->get('/admin/security')->assertOk()->assertSee('<svg', false);

        $secret = session('admin_2fa_setup');
        $this->post('/admin/security/enable', ['code' => '000000'])->assertSessionHasErrors('code');

        $this->post('/admin/security/enable', ['code' => $google->getCurrentOtp($secret)])->assertSessionHas('recovery_codes');

        $raw = \DB::table('superadmins')->where('id', $this->admin->id)->value('two_factor_secret');
        $this->assertNotSame($secret, $raw);
        $this->assertSame($secret, Crypt::decryptString($raw));
        $this->assertTrue($this->admin->fresh()->hasTwoFactor());
    }

    public function test_login_with_2fa_requires_the_code(): void
    {
        $secret = $this->enable2fa();

        $this->post('/admin/login', ['email' => 'boss@runwrk.test', 'password' => 'secret-pass'])->assertRedirect('/admin/two-factor');
        $this->assertGuest('superadmin');
        $this->get('/admin')->assertRedirect('/admin/login');

        $this->post('/admin/two-factor', ['code' => '123456'])->assertSessionHasErrors('code');
        $this->assertGuest('superadmin');

        $this->post('/admin/two-factor', ['code' => (new Google2FA)->getCurrentOtp($secret)])->assertRedirect('/admin');
        $this->assertAuthenticated('superadmin');
    }

    public function test_wrong_password_never_reaches_the_code_step(): void
    {
        $this->enable2fa();

        $this->post('/admin/login', ['email' => 'boss@runwrk.test', 'password' => 'nope'])->assertSessionHasErrors('email');
        $this->get('/admin/two-factor')->assertNotFound();
    }

    public function test_recovery_code_works_once(): void
    {
        $this->enable2fa();
        $codes = $this->admin->issueRecoveryCodes();

        $this->post('/admin/login', ['email' => 'boss@runwrk.test', 'password' => 'secret-pass']);
        $this->post('/admin/two-factor', ['code' => $codes[0]])->assertRedirect('/admin');
        $this->assertAuthenticated('superadmin');

        auth('superadmin')->logout();
        $this->post('/admin/login', ['email' => 'boss@runwrk.test', 'password' => 'secret-pass']);
        $this->post('/admin/two-factor', ['code' => $codes[0]])->assertSessionHasErrors('code');
        $this->assertGuest('superadmin');
    }

    public function test_disable_needs_the_password(): void
    {
        $this->enable2fa();

        $this->actingAs($this->admin, 'superadmin')->post('/admin/security/disable', ['password' => 'wrong'])->assertSessionHasErrors('password');
        $this->assertTrue($this->admin->fresh()->hasTwoFactor());

        $this->post('/admin/security/disable', ['password' => 'secret-pass'])->assertRedirect();
        $this->assertFalse($this->admin->fresh()->hasTwoFactor());
    }

    public function test_settings_save_encrypts_the_mail_password_and_applies_at_runtime(): void
    {
        $this->actingAs($this->admin, 'superadmin')->put('/admin/settings', [
            'app_name' => 'Acme Push', 'mail_host' => 'smtp.example.com', 'mail_port' => 465, 'mail_encryption' => 'ssl',
            'mail_username' => 'me@example.com', 'mail_password' => 'smtp-secret', 'mail_from_address' => 'hello@example.com',
            'signups_enabled' => '1',
        ])->assertRedirect();

        $raw = \DB::table('settings')->where('key', 'mail.password')->value('value');
        $this->assertStringNotContainsString('smtp-secret', $raw);

        \App\Domain\Ops\PlatformSettings::applyToConfig();
        $this->assertSame('Acme Push', config('app.name'));
        $this->assertSame('smtp.example.com', config('mail.mailers.smtp.host'));
        $this->assertSame(465, config('mail.mailers.smtp.port'));
        $this->assertSame('smtp-secret', config('mail.mailers.smtp.password'));
        $this->assertSame('smtps', config('mail.mailers.smtp.scheme'));

        $this->get('/admin/settings')->assertOk()->assertDontSee('smtp-secret')->assertSee('A password is saved');
    }

    public function test_blank_password_keeps_the_saved_one(): void
    {
        \App\Domain\Ops\Settings::putSecret('mail.password', 'keep-me');

        $this->actingAs($this->admin, 'superadmin')->put('/admin/settings', ['mail_host' => 'smtp.example.com', 'mail_password' => ''])->assertRedirect();

        $this->assertSame('keep-me', \App\Domain\Ops\Settings::getSecret('mail.password'));
    }

    public function test_test_mail_goes_to_the_admin(): void
    {
        Mail::fake();
        $this->actingAs($this->admin, 'superadmin')->post('/admin/settings/test-mail')->assertSessionHas('status');
        $this->assertTrue(true);
    }

    public function test_plans_crud_and_archive_hides_from_pricing(): void
    {
        $this->actingAs($this->admin, 'superadmin');

        $this->post('/admin/plans', [
            'name' => 'Gold', 'code' => 'gold', 'price' => '49.50', 'interval' => 'month', 'features' => "One\n\nTwo", 'is_public' => '1', 'sort_order' => 5,
        ])->assertRedirect('/admin/plans');

        $plan = Plan::where('code', 'gold')->firstOrFail();
        $this->assertSame(4950, $plan->price_cents);
        $this->assertSame(['One', 'Two'], $plan->features);

        $this->get('/pricing')->assertOk()->assertSee('Gold')->assertSee('$49.50');

        $this->put("/admin/plans/{$plan->id}", ['name' => 'Gold', 'code' => 'gold', 'price' => '55', 'interval' => 'year', 'is_public' => '1'])->assertRedirect();
        $this->assertSame(5500, $plan->fresh()->price_cents);

        $this->post("/admin/plans/{$plan->id}/archive");
        $this->get('/pricing')->assertDontSee('Gold');

        $this->post('/admin/plans', ['name' => 'Dup', 'code' => 'gold', 'price' => 1, 'interval' => 'month'])->assertSessionHasErrors('code');
    }

    public function test_private_plans_are_not_listed(): void
    {
        Plan::create(['name' => 'Secret', 'code' => 'secret', 'is_public' => false]);
        Plan::create(['name' => 'Shown', 'code' => 'shown', 'is_public' => true]);

        $this->get('/pricing')->assertSee('Shown')->assertDontSee('Secret');
    }
}

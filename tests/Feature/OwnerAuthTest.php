<?php

namespace Tests\Feature;

use App\Mail\InviteMail;
use App\Mail\RegistrationOtpMail;
use App\Models\Business;
use App\Models\PendingRegistration;
use App\Models\Plan;
use App\Models\Superadmin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class OwnerAuthTest extends TestCase
{
    use RefreshDatabase;

    private Plan $free;

    protected function setUp(): void
    {
        parent::setUp();

        $this->free = Plan::create(['name' => 'Free trial', 'code' => 'trial', 'price_cents' => 0]);
        Plan::create(['name' => 'Starter', 'code' => 'starter', 'price_cents' => 2900]);
    }

    private function register(array $over = [])
    {
        return $this->post('/register', $over + [
            'plan' => 'starter', 'business_name' => "Joe's Barber", 'name' => 'Joe', 'email' => 'joe@example.com',
            'password' => 'a-good-password-1', 'password_confirmation' => 'a-good-password-1',
        ]);
    }

    public function test_full_signup_flow_creates_business_owner_and_logs_in(): void
    {
        Mail::fake();

        $this->register()->assertRedirect();
        $this->assertSame(0, Business::count());
        $this->assertSame(0, User::count());

        $pending = PendingRegistration::firstOrFail();
        $code = null;
        Mail::assertSent(RegistrationOtpMail::class, function ($mail) use (&$code) {
            $code = $mail->code;

            return true;
        });

        $this->post("/register/verify/{$pending->token}", ['code' => $code])->assertRedirect('/dashboard');

        $business = Business::firstOrFail();
        $this->assertSame('joes-barber', $business->slug);
        $this->assertSame('trial', $business->status);
        $this->assertSame('starter', $business->plan->code);
        $owner = User::firstOrFail();
        $this->assertSame('owner', $owner->role);
        $this->assertSame($business->id, $owner->business_id);
        $this->assertAuthenticatedAs($owner, 'web');
        $this->assertTrue(\Hash::check('a-good-password-1', $owner->password));
        $this->assertDatabaseHas('audit_logs', ['action' => 'business.registered', 'business_id' => $business->id]);
    }

    public function test_wrong_code_five_times_locks_the_code(): void
    {
        Mail::fake();
        $this->register();
        $pending = PendingRegistration::firstOrFail();

        foreach (range(1, 5) as $i) {
            $this->post("/register/verify/{$pending->token}", ['code' => '000000'])->assertSessionHasErrors('code');
        }

        $mail = null;
        Mail::assertSent(RegistrationOtpMail::class, function ($m) use (&$mail) {
            $mail = $m;

            return true;
        });

        $this->post("/register/verify/{$pending->token}", ['code' => $mail->code])->assertSessionHasErrors('code');
        $this->assertSame(0, Business::count());
    }

    public function test_slug_is_unique_and_never_reserved(): void
    {
        Business::factory()->create(['slug' => 'joes-barber']);
        $this->assertSame('joes-barber-2', Business::uniqueSlug("Joe's Barber"));
        $this->assertSame('admin-2', Business::uniqueSlug('Admin'));
    }

    public function test_duplicate_email_and_weak_password_are_rejected(): void
    {
        User::factory()->create(['email' => 'joe@example.com']);

        $this->register()->assertSessionHasErrors('email');
        $this->register(['email' => 'new@example.com', 'password' => 'short', 'password_confirmation' => 'short'])->assertSessionHasErrors('password');
    }

    public function test_signups_can_be_switched_off(): void
    {
        \App\Domain\Ops\Settings::put('signups_enabled', false);
        \App\Domain\Ops\PlatformSettings::forget();

        $this->get('/register')->assertNotFound();
        $this->register()->assertNotFound();
    }

    public function test_login_logout_and_throttle(): void
    {
        $user = User::factory()->create(['email' => 'o@example.com', 'password' => 'right-pass-1']);

        $this->post('/login', ['email' => 'o@example.com', 'password' => 'right-pass-1'])->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user, 'web');
        $this->assertNotNull($user->fresh()->last_login_at);

        $this->post('/logout')->assertRedirect('/login');
        $this->assertGuest('web');

        foreach (range(1, 5) as $i) {
            $this->post('/login', ['email' => 'o@example.com', 'password' => 'bad']);
        }
        $this->post('/login', ['email' => 'o@example.com', 'password' => 'right-pass-1'])->assertSessionHasErrors('email');
        $this->assertGuest('web');
    }

    public function test_suspended_business_owner_cannot_use_dashboard(): void
    {
        $business = Business::factory()->create(['status' => 'suspended']);
        $user = User::factory()->create(['business_id' => $business->id]);

        $this->actingAs($user)->get('/dashboard')->assertForbidden();
    }

    public function test_password_reset_flow(): void
    {
        $user = User::factory()->create(['email' => 'o@example.com', 'password' => 'old-pass-123']);
        $token = Password::broker()->createToken($user);

        $this->post('/password/reset', ['token' => 'bad', 'email' => 'o@example.com', 'password' => 'new-pass-12345', 'password_confirmation' => 'new-pass-12345'])->assertSessionHasErrors('email');

        $this->post('/password/reset', ['token' => $token, 'email' => 'o@example.com', 'password' => 'new-pass-12345', 'password_confirmation' => 'new-pass-12345'])->assertRedirect('/login');
        $this->assertTrue(\Hash::check('new-pass-12345', $user->fresh()->password));
    }

    public function test_forgot_password_does_not_reveal_whether_an_email_exists(): void
    {
        $a = $this->post('/password/forgot', ['email' => 'nobody@example.com'])->getSession()->get('status');
        User::factory()->create(['email' => 'o@example.com']);
        $b = $this->post('/password/forgot', ['email' => 'o@example.com'])->getSession()->get('status');

        $this->assertSame($a, $b);
    }

    public function test_superadmin_created_owner_sets_their_own_password(): void
    {
        Mail::fake();
        $admin = Superadmin::create(['name' => 'Boss', 'email' => 'boss@runwrk.test', 'password' => 'secret-pass']);

        $response = $this->actingAs($admin, 'superadmin')->post('/admin/businesses', [
            'name' => 'Salon X', 'slug' => 'salon-x', 'owner_name' => 'Xena', 'owner_email' => 'xena@example.com', 'status' => 'trial',
        ]);

        Mail::assertSent(InviteMail::class, fn ($m) => $m->hasTo('xena@example.com'));
        $url = $response->getSession()->get('invite_url');
        $this->assertStringContainsString('/password/reset/', $url);
        $this->assertStringNotContainsString('password=', $url);

        auth('superadmin')->logout();
        parse_str(parse_url($url, PHP_URL_QUERY), $query);
        $token = basename(parse_url($url, PHP_URL_PATH));

        $this->post('/login', ['email' => 'xena@example.com', 'password' => 'anything'])->assertSessionHasErrors('email');

        $this->post('/password/reset', ['token' => $token, 'email' => $query['email'], 'password' => 'xenas-own-pass-1', 'password_confirmation' => 'xenas-own-pass-1'])->assertRedirect('/login');
        $this->post('/login', ['email' => 'xena@example.com', 'password' => 'xenas-own-pass-1'])->assertRedirect('/dashboard');
    }
}

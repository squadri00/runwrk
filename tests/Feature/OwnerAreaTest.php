<?php

namespace Tests\Feature;

use App\Mail\InviteMail;
use App\Models\Business;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OwnerAreaTest extends TestCase
{
    use RefreshDatabase;

    private Business $a;

    private Business $b;

    private User $owner;

    private User $staff;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->a = Business::factory()->create(['name' => 'Alpha']);
        $this->b = Business::factory()->create(['name' => 'Beta']);
        $this->owner = User::factory()->create(['business_id' => $this->a->id, 'role' => 'owner']);
        $this->staff = User::factory()->create(['business_id' => $this->a->id, 'role' => 'staff']);
    }

    private function fields(array $over = []): array
    {
        return $over + [
            'name' => 'Alpha', 'short_name' => 'Alpha', 'theme_color' => '#123456', 'background_color' => '#ffeedd',
            'timezone' => 'America/Toronto',
        ];
    }

    public function test_staff_cannot_open_owner_pages(): void
    {
        $this->actingAs($this->staff);

        foreach (['/dashboard/branding', '/dashboard/team'] as $url) {
            $this->get($url)->assertForbidden();
        }
        $this->put('/dashboard/branding', $this->fields())->assertForbidden();
        $this->post('/dashboard/team', ['name' => 'X', 'email' => 'x@example.com', 'role' => 'staff'])->assertForbidden();

        $this->get('/dashboard')->assertOk();
        $this->get('/dashboard/account')->assertOk();
    }

    public function test_owner_pages_render(): void
    {
        $this->actingAs($this->owner);

        foreach (['/dashboard', '/dashboard/branding', '/dashboard/team', '/dashboard/account'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_logo_upload_builds_square_icons_in_the_right_sizes(): void
    {
        $this->actingAs($this->owner)->put('/dashboard/branding', $this->fields([
            'logo' => UploadedFile::fake()->image('logo.png', 600, 300),
        ]))->assertRedirect('/dashboard/branding');

        $this->a->refresh();
        $this->assertSame("businesses/{$this->a->id}/logo.png", $this->a->logo_path);
        $this->assertSame("businesses/{$this->a->id}/icons", $this->a->icon_path);

        foreach (['icon-192.png' => 192, 'icon-512.png' => 512, 'maskable-512.png' => 512, 'apple-touch-180.png' => 180] as $file => $size) {
            $path = "businesses/{$this->a->id}/icons/$file";
            Storage::disk('public')->assertExists($path);
            $this->assertSame([$size, $size], array_slice(getimagesizefromstring(Storage::disk('public')->get($path)), 0, 2));
        }

        $this->assertDatabaseHas('audit_logs', ['action' => 'branding.update', 'business_id' => $this->a->id]);
    }

    public function test_changing_background_colour_regenerates_icons_and_removing_logo_cleans_up(): void
    {
        $this->actingAs($this->owner)->put('/dashboard/branding', $this->fields(['logo' => UploadedFile::fake()->image('l.png', 400, 400)]));
        $before = Storage::disk('public')->get("businesses/{$this->a->id}/icons/icon-192.png");

        $this->put('/dashboard/branding', $this->fields(['background_color' => '#000000']));
        $this->assertNotSame($before, Storage::disk('public')->get("businesses/{$this->a->id}/icons/icon-192.png"));

        $this->put('/dashboard/branding', $this->fields(['remove_logo' => '1']));
        Storage::disk('public')->assertMissing("businesses/{$this->a->id}/logo.png");
        $this->assertNull($this->a->fresh()->icon_path);
    }

    public function test_bad_uploads_are_rejected(): void
    {
        $this->actingAs($this->owner);

        $this->put('/dashboard/branding', $this->fields(['logo' => UploadedFile::fake()->image('tiny.png', 50, 50)]))->assertSessionHasErrors('logo');
        $this->put('/dashboard/branding', $this->fields(['logo' => UploadedFile::fake()->create('x.pdf', 10, 'application/pdf')]))->assertSessionHasErrors('logo');
        $this->put('/dashboard/branding', $this->fields(['theme_color' => 'red']))->assertSessionHasErrors('theme_color');
    }

    public function test_owner_edits_only_their_own_business(): void
    {
        $this->actingAs($this->owner)->put('/dashboard/branding', $this->fields(['name' => 'Renamed']));

        $this->assertSame('Renamed', $this->a->fresh()->name);
        $this->assertSame('Beta', $this->b->fresh()->name);
    }

    public function test_team_invite_lists_and_removal_are_scoped_to_the_business(): void
    {
        Mail::fake();
        $otherUser = User::factory()->create(['business_id' => $this->b->id, 'role' => 'owner', 'name' => 'Beta Boss']);
        $this->actingAs($this->owner);

        $this->get('/dashboard/team')->assertOk()->assertDontSee('Beta Boss');

        $this->post('/dashboard/team', ['name' => 'New Hire', 'email' => 'hire@example.com', 'role' => 'staff'])->assertRedirect();
        Mail::assertSent(InviteMail::class, fn ($m) => $m->hasTo('hire@example.com'));
        $this->assertSame($this->a->id, User::where('email', 'hire@example.com')->value('business_id'));

        $this->delete("/dashboard/team/{$otherUser->id}")->assertNotFound();
        $this->assertNotNull(User::find($otherUser->id));
        $this->post("/dashboard/team/{$otherUser->id}/invite")->assertNotFound();

        $this->delete('/dashboard/team/'.$this->staff->id)->assertSessionHas('status');
        $this->assertNull(User::find($this->staff->id));
    }

    public function test_cannot_remove_yourself_or_the_last_owner(): void
    {
        $this->actingAs($this->owner)->delete("/dashboard/team/{$this->owner->id}")->assertSessionHas('error');
        $this->assertNotNull(User::find($this->owner->id));

        $second = User::factory()->create(['business_id' => $this->a->id, 'role' => 'owner']);
        $this->actingAs($second)->delete("/dashboard/team/{$this->owner->id}")->assertSessionHas('status');
        $this->assertNull(User::find($this->owner->id));

        $this->actingAs($second)->delete("/dashboard/team/{$second->id}")->assertSessionHas('error');
    }

    public function test_password_change_needs_the_current_password(): void
    {
        $this->actingAs($this->owner)->put('/dashboard/account', ['name' => 'Owner', 'current_password' => 'wrong', 'password' => 'brand-new-pass-1', 'password_confirmation' => 'brand-new-pass-1'])
            ->assertSessionHasErrors('current_password');
        $this->assertTrue(\Hash::check('password', $this->owner->fresh()->password));

        $this->put('/dashboard/account', ['name' => 'Owner', 'current_password' => 'password', 'password' => 'brand-new-pass-1', 'password_confirmation' => 'brand-new-pass-1'])->assertSessionHasNoErrors();
        $this->assertTrue(\Hash::check('brand-new-pass-1', $this->owner->fresh()->password));
    }
}

<?php

namespace Tests\Feature;

use App\Domain\Push\PushTransport;
use App\Domain\Tenancy\CurrentBusiness;
use App\Models\Business;
use App\Models\PushMessage;
use App\Models\PushSubscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Support\FakeTransport;
use Tests\TestCase;

class NotificationsUiTest extends TestCase
{
    use RefreshDatabase;

    private FakeTransport $fake;

    private Business $a;

    private Business $b;

    private User $owner;

    private User $staff;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->fake = new FakeTransport;
        $this->app->instance(PushTransport::class, $this->fake);

        $this->a = Business::factory()->create(['timezone' => 'America/Toronto']);
        $this->b = Business::factory()->create();
        $this->owner = User::factory()->create(['business_id' => $this->a->id, 'role' => 'owner']);
        $this->staff = User::factory()->create(['business_id' => $this->a->id, 'role' => 'staff']);
    }

    private function subscribe(Business $business, int $n = 2): void
    {
        app(CurrentBusiness::class)->run($business, function () use ($n) {
            foreach (range(1, $n) as $i) {
                PushSubscription::create(['endpoint' => 'https://fcm.googleapis.com/x/'.uniqid(), 'endpoint_hash' => bin2hex(random_bytes(32)), 'p256dh' => 'p', 'auth' => 'a']);
            }
        });
    }

    private function form(array $over = []): array
    {
        return $over + ['title' => 'Flash sale', 'body' => 'Half price today only', 'when' => 'now'];
    }

    public function test_send_now_delivers_and_records_stats(): void
    {
        $this->subscribe($this->a, 3);
        $this->subscribe($this->b, 2);

        $this->actingAs($this->owner)->post('/dashboard/notifications', $this->form(['url' => 'https://example.com/sale']))->assertRedirect();

        $m = PushMessage::withoutBusinessScope()->firstOrFail();
        $this->assertSame($this->a->id, $m->business_id);
        $this->assertSame($this->owner->id, $m->created_by);
        $this->assertSame('sent', $m->status);
        $this->assertSame(3, $m->success_count);
        $this->assertCount(3, $this->fake->sentIds());
        $this->assertSame('https://example.com/sale', $this->fake->calls[0]['payload']['url']);
        $this->assertDatabaseHas('audit_logs', ['action' => 'push.create', 'business_id' => $this->a->id]);
    }

    public function test_staff_can_send_too(): void
    {
        $this->subscribe($this->a);

        $this->actingAs($this->staff)->post('/dashboard/notifications', $this->form())->assertRedirect();

        $this->assertSame($this->staff->id, PushMessage::withoutBusinessScope()->value('created_by'));
    }

    public function test_cannot_send_with_no_subscribers(): void
    {
        $this->actingAs($this->owner)->post('/dashboard/notifications', $this->form())->assertSessionHas('error');

        $this->assertSame(0, PushMessage::withoutBusinessScope()->count());
    }

    public function test_validation(): void
    {
        $this->subscribe($this->a);
        $this->actingAs($this->owner);

        $this->post('/dashboard/notifications', $this->form(['title' => '']))->assertSessionHasErrors('title');
        $this->post('/dashboard/notifications', $this->form(['title' => str_repeat('x', 66)]))->assertSessionHasErrors('title');
        $this->post('/dashboard/notifications', $this->form(['body' => str_repeat('x', 201)]))->assertSessionHasErrors('body');
        $this->post('/dashboard/notifications', $this->form(['url' => 'javascript:alert(1)']))->assertSessionHasErrors('url');
        $this->post('/dashboard/notifications', $this->form(['image' => UploadedFile::fake()->create('a.pdf', 10, 'application/pdf')]))->assertSessionHasErrors('image');
        $this->post('/dashboard/notifications', $this->form(['when' => 'later']))->assertSessionHasErrors('send_at');

        $this->assertSame(0, PushMessage::withoutBusinessScope()->count());
    }

    public function test_scheduled_time_is_read_in_the_business_timezone(): void
    {
        $this->subscribe($this->a);
        $future = now()->addDays(2)->setTimezone('America/Toronto')->setTime(10, 0);

        $this->actingAs($this->owner)->post('/dashboard/notifications', $this->form(['when' => 'later', 'send_at' => $future->format('Y-m-d\TH:i')]))->assertRedirect();

        $m = PushMessage::withoutBusinessScope()->firstOrFail();
        $this->assertSame('scheduled', $m->status);
        $this->assertTrue($m->scheduled_at->equalTo($future->copy()->utc()));
        $this->assertSame([], $this->fake->calls);
    }

    public function test_past_or_too_far_times_are_rejected(): void
    {
        $this->subscribe($this->a);
        $this->actingAs($this->owner);

        $this->post('/dashboard/notifications', $this->form(['when' => 'later', 'send_at' => now()->setTimezone('America/Toronto')->subHour()->format('Y-m-d\TH:i')]))->assertSessionHasErrors('send_at');
        $this->post('/dashboard/notifications', $this->form(['when' => 'later', 'send_at' => now()->addYears(2)->format('Y-m-d\TH:i')]))->assertSessionHasErrors('send_at');
    }

    public function test_image_is_stored_and_sent(): void
    {
        $this->subscribe($this->a, 1);

        $this->actingAs($this->owner)->post('/dashboard/notifications', $this->form(['image' => UploadedFile::fake()->image('sale.jpg', 800, 400)]));

        $m = PushMessage::withoutBusinessScope()->firstOrFail();
        Storage::disk('public')->assertExists($m->image_path);
        $this->assertStringStartsWith("push/{$this->a->id}/", $m->image_path);
        $this->assertArrayHasKey('image', $this->fake->calls[0]['payload']);
    }

    public function test_cancel_a_scheduled_message(): void
    {
        $this->subscribe($this->a);
        $this->actingAs($this->owner)->post('/dashboard/notifications', $this->form(['when' => 'later', 'send_at' => now()->addDay()->format('Y-m-d\TH:i')]));
        $m = PushMessage::withoutBusinessScope()->firstOrFail();

        $this->post("/dashboard/notifications/{$m->id}/cancel")->assertRedirect();

        $this->assertSame('cancelled', $m->fresh()->status);
        $this->post("/dashboard/notifications/{$m->id}/cancel")->assertStatus(422);
    }

    public function test_other_businesses_messages_are_not_viewable_or_cancellable(): void
    {
        $theirs = app(CurrentBusiness::class)->run($this->b, fn () => PushMessage::create(['title' => 'Secret', 'body' => 'Plan', 'status' => 'scheduled', 'scheduled_at' => now()->addDay()]));

        $this->actingAs($this->owner)->get("/dashboard/notifications/{$theirs->id}")->assertNotFound();
        $this->post("/dashboard/notifications/{$theirs->id}/cancel")->assertNotFound();
        $this->get('/dashboard/notifications')->assertOk()->assertDontSee('Secret');

        $this->assertSame('scheduled', $theirs->fresh()->status);
    }

    public function test_pages_render_with_data(): void
    {
        $this->subscribe($this->a, 2);
        $this->actingAs($this->owner)->post('/dashboard/notifications', $this->form());
        $m = PushMessage::withoutBusinessScope()->firstOrFail();

        foreach (['/dashboard', '/dashboard/notifications', '/dashboard/notifications/new', "/dashboard/notifications/{$m->id}", '/dashboard/subscribers'] as $url) {
            $this->get($url)->assertOk();
        }

        $this->get('/dashboard/subscribers')->assertSee('Latest sign-ups');
    }

    public function test_guests_are_sent_to_login(): void
    {
        $this->get('/dashboard/notifications')->assertRedirect('/login');
        $this->post('/dashboard/notifications', $this->form())->assertRedirect('/login');
    }
}

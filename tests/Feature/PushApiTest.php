<?php

namespace Tests\Feature;

use App\Domain\Tenancy\CurrentBusiness;
use App\Models\Business;
use App\Models\PushMessage;
use App\Models\PushSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PushApiTest extends TestCase
{
    use RefreshDatabase;

    private Business $a;

    private Business $b;

    protected function setUp(): void
    {
        parent::setUp();

        config(['runwrk.allow_local_origins' => false, 'runwrk.vapid.public' => 'BPublicKeyForTests']);
        $this->a = Business::factory()->create();
        $this->a->domains()->create(['domain' => 'alpha.com']);
        $this->b = Business::factory()->create();
        $this->b->domains()->create(['domain' => 'beta.com']);
    }

    private function sub(string $endpoint = 'https://fcm.googleapis.com/fcm/send/abc123'): array
    {
        return ['endpoint' => $endpoint, 'keys' => ['p256dh' => str_repeat('p', 87), 'auth' => str_repeat('a', 22)], 'contentEncoding' => 'aes128gcm', 'source' => 'hosted'];
    }

    private function api(Business $business, string $path, array $data, ?string $origin = null)
    {
        $headers = ['X-Runwrk-Key' => $business->public_key, 'Accept' => 'application/json'] + ($origin ? ['Origin' => $origin] : []);

        return $this->postJson("/api/v1/$path", $data, $headers);
    }

    private function subCount(Business $business): int
    {
        return app(CurrentBusiness::class)->run($business, fn () => PushSubscription::count());
    }

    public function test_subscribe_stores_for_the_keys_business_and_is_idempotent(): void
    {
        $this->api($this->a, 'subscribe', $this->sub(), 'https://alpha.com')->assertCreated();
        $this->api($this->a, 'subscribe', $this->sub(), 'https://alpha.com')->assertOk();

        $this->assertSame(1, $this->subCount($this->a));
        $this->assertSame(0, $this->subCount($this->b));

        $row = PushSubscription::withoutBusinessScope()->first();
        $this->assertSame('https://fcm.googleapis.com/fcm/send/abc123', $row->endpoint);
        $this->assertSame('hosted', $row->source);
    }

    public function test_platform_is_detected_from_the_user_agent(): void
    {
        $this->withHeaders(['User-Agent' => 'Mozilla/5.0 (Linux; Android 14; Pixel 8) Chrome/120'])->api($this->a, 'subscribe', $this->sub());

        $this->assertSame('android', PushSubscription::withoutBusinessScope()->first()->platform);
    }

    public function test_subscribe_refuses_endpoints_that_are_not_push_services(): void
    {
        foreach (['https://169.254.169.254/latest/meta-data', 'https://evil.example/push', 'http://fcm.googleapis.com/x', 'https://fcm.googleapis.com.evil.example/x', 'https://127.0.0.1/x', 'ftp://fcm.googleapis.com/x'] as $endpoint) {
            $this->api($this->a, 'subscribe', $this->sub($endpoint))->assertStatus(422);
        }

        foreach (['https://fcm.googleapis.com/fcm/send/x', 'https://updates.push.services.mozilla.com/wpush/v2/x', 'https://web.push.apple.com/x', 'https://wns2-par02p.notify.windows.com/w/x'] as $endpoint) {
            $this->api($this->a, 'subscribe', $this->sub($endpoint))->assertSuccessful();
        }

        $this->assertSame(4, $this->subCount($this->a));
    }

    public function test_subscribe_validates_keys(): void
    {
        $bad = $this->sub();
        $bad['keys']['auth'] = 'short';
        $this->api($this->a, 'subscribe', $bad)->assertStatus(422);

        $bad = $this->sub();
        unset($bad['keys']);
        $this->api($this->a, 'subscribe', $bad)->assertStatus(422);

        $this->assertSame(0, $this->subCount($this->a));
    }

    public function test_foreign_origin_cannot_subscribe(): void
    {
        $this->api($this->a, 'subscribe', $this->sub(), 'https://beta.com')->assertStatus(403);
        $this->api($this->a, 'subscribe', $this->sub(), 'https://evil.com')->assertStatus(403);

        $this->assertSame(0, $this->subCount($this->a));
    }

    public function test_bad_key_cannot_subscribe(): void
    {
        $this->postJson('/api/v1/subscribe', $this->sub(), ['X-Runwrk-Key' => 'pk_nope'])->assertStatus(401);
    }

    public function test_same_device_moves_to_the_new_business_without_duplicates(): void
    {
        $this->api($this->a, 'subscribe', $this->sub());
        $this->api($this->b, 'subscribe', $this->sub());

        $this->assertSame(0, $this->subCount($this->a));
        $this->assertSame(1, $this->subCount($this->b));
    }

    public function test_unsubscribe_only_removes_from_the_calling_business(): void
    {
        $this->api($this->a, 'subscribe', $this->sub());

        $this->api($this->b, 'unsubscribe', ['endpoint' => $this->sub()['endpoint']])->assertOk();
        $this->assertSame(1, $this->subCount($this->a));

        $this->api($this->a, 'unsubscribe', ['endpoint' => $this->sub()['endpoint']])->assertOk();
        $this->assertSame(0, $this->subCount($this->a));
    }

    public function test_click_counts_only_the_calling_businesss_message(): void
    {
        $mine = app(CurrentBusiness::class)->run($this->a, fn () => PushMessage::create(['title' => 't', 'body' => 'b']));
        $theirs = app(CurrentBusiness::class)->run($this->b, fn () => PushMessage::create(['title' => 't', 'body' => 'b']));

        $this->api($this->a, 'click', ['message' => $mine->id])->assertOk();
        $this->api($this->a, 'click', ['message' => $theirs->id])->assertOk();

        $this->assertSame(1, $mine->fresh()->click_count);
        $this->assertSame(0, $theirs->fresh()->click_count);
    }

    public function test_phone_receipts_count_only_the_calling_businesss_message(): void
    {
        $mine = app(CurrentBusiness::class)->run($this->a, fn () => PushMessage::create(['title' => 't', 'body' => 'b']));
        $theirs = app(CurrentBusiness::class)->run($this->b, fn () => PushMessage::create(['title' => 't', 'body' => 'b']));

        $this->api($this->a, 'received', ['message' => $mine->id])->assertOk();
        $this->api($this->a, 'received', ['message' => $mine->id])->assertOk();
        $this->api($this->a, 'received', ['message' => $theirs->id])->assertOk();
        $this->api($this->a, 'received', ['message' => 0])->assertOk();

        $this->assertSame(2, $mine->fresh()->received_count);
        $this->assertSame(0, $theirs->fresh()->received_count);
    }

    public function test_a_phone_that_cannot_show_a_notification_is_logged_with_the_reason(): void
    {
        \Log::spy();

        $this->api($this->a, 'received', ['message' => 0, 'error' => 'notifications are blocked by the device'])->assertOk();

        \Log::shouldHaveReceived('warning')->withArgs(fn ($m, $ctx) => str_contains($m, 'could not show') && $ctx['error'] === 'notifications are blocked by the device')->once();
    }

    public function test_a_subscriber_can_send_a_test_to_their_own_device_only(): void
    {
        $fake = new \Tests\Support\FakeTransport;
        $this->app->instance(\App\Domain\Push\PushTransport::class, $fake);
        $this->api($this->a, 'subscribe', $this->sub());
        $mineId = PushSubscription::withoutBusinessScope()->value('id');

        $res = $this->api($this->a, 'test', ['endpoint' => $this->sub()['endpoint']])->assertOk();
        $this->assertTrue($res->json('accepted'));
        $this->assertSame([$mineId], $fake->sentIds());
        $this->assertTrue($fake->calls[0]['payload']['test']);
        $this->assertArrayNotHasKey('msg', $fake->calls[0]['payload'], 'a test must not count as a real message');

        // another business cannot trigger a test to this device, and unknown devices get 404
        $this->api($this->b, 'test', ['endpoint' => $this->sub()['endpoint']])->assertStatus(404);
        $this->api($this->a, 'test', ['endpoint' => 'https://fcm.googleapis.com/fcm/send/unknown'])->assertStatus(404);
        $this->assertCount(1, $fake->calls);
    }

    public function test_a_test_to_a_dead_device_removes_it_and_says_so(): void
    {
        $fake = new \Tests\Support\FakeTransport;
        $this->app->instance(\App\Domain\Push\PushTransport::class, $fake);
        $this->api($this->a, 'subscribe', $this->sub());
        $id = PushSubscription::withoutBusinessScope()->value('id');
        $fake->results[$id] = ['ok' => false, 'expired' => true, 'reason' => 'Gone'];

        $res = $this->api($this->a, 'test', ['endpoint' => $this->sub()['endpoint']])->assertOk();

        $this->assertFalse($res->json('accepted'));
        $this->assertTrue($res->json('expired'));
        $this->assertSame(0, $this->subCount($this->a));
    }

    public function test_test_messages_are_rate_limited(): void
    {
        $this->app->instance(\App\Domain\Push\PushTransport::class, new \Tests\Support\FakeTransport);
        $this->api($this->a, 'subscribe', $this->sub());

        foreach (range(1, 4) as $i) {
            $this->api($this->a, 'test', ['endpoint' => $this->sub()['endpoint']])->assertOk();
        }
        $this->api($this->a, 'test', ['endpoint' => $this->sub()['endpoint']])->assertStatus(429);
    }

    public function test_config_returns_the_public_vapid_key_only(): void
    {
        config(['runwrk.vapid.private' => 'SECRET-PRIVATE']);

        $res = $this->getJson('/api/v1/config', ['X-Runwrk-Key' => $this->a->public_key]);

        $res->assertOk()->assertJson(['vapidPublicKey' => 'BPublicKeyForTests']);
        $this->assertStringNotContainsString('SECRET-PRIVATE', $res->getContent());
    }
}

<?php

namespace Tests\Feature;

use App\Domain\Push\PushDispatcher;
use App\Domain\Push\PushTransport;
use App\Domain\Tenancy\CurrentBusiness;
use App\Jobs\SendPushBatch;
use App\Models\Business;
use App\Models\PushMessage;
use App\Models\PushSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\Support\FakeTransport;
use Tests\TestCase;

class PushEngineTest extends TestCase
{
    use RefreshDatabase;

    private FakeTransport $fake;

    private Business $a;

    private Business $b;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fake = new FakeTransport;
        $this->app->instance(PushTransport::class, $this->fake);
        config(['runwrk.push.batch_size' => 100]);

        $this->a = Business::factory()->create(['name' => 'Alpha', 'website_url' => 'https://alpha.example']);
        $this->b = Business::factory()->create(['name' => 'Beta']);
    }

    private function subs(Business $business, int $n): array
    {
        return app(CurrentBusiness::class)->run($business, fn () => collect(range(1, $n))->map(fn ($i) => PushSubscription::create([
            'endpoint' => "https://fcm.googleapis.com/fcm/send/{$business->id}-".uniqid(), 'endpoint_hash' => bin2hex(random_bytes(32)),
            'p256dh' => str_repeat('p', 87), 'auth' => str_repeat('a', 22),
        ])->id)->all());
    }

    private function message(Business $business, array $over = []): PushMessage
    {
        return app(CurrentBusiness::class)->run($business, fn () => PushMessage::create($over + [
            'title' => 'Hello', 'body' => 'World', 'status' => 'scheduled', 'scheduled_at' => now()->subMinute(),
        ]));
    }

    private function deliver(PushMessage $m, int $times = 1): PushMessage
    {
        $dispatcher = app(PushDispatcher::class);
        $dispatcher->promoteDue();

        foreach (range(1, $times) as $i) {
            $dispatcher->process($m->id, 0);
        }

        return PushMessage::withoutBusinessScope()->find($m->id);
    }

    public function test_message_only_reaches_its_own_businesses_subscribers(): void
    {
        $aSubs = $this->subs($this->a, 3);
        $bSubs = $this->subs($this->b, 2);
        $m = $this->message($this->a);

        $done = $this->deliver($m);

        $this->assertEqualsCanonicalizing($aSubs, $this->fake->sentIds());
        $this->assertEmpty(array_intersect($bSubs, $this->fake->sentIds()));
        $this->assertSame('sent', $done->status);
        $this->assertSame([3, 3, 0, 0], [$done->target_count, $done->success_count, $done->failure_count, $done->expired_count]);
    }

    public function test_two_businesses_sending_at_once_stay_separate(): void
    {
        $aSubs = $this->subs($this->a, 2);
        $bSubs = $this->subs($this->b, 2);
        $ma = $this->message($this->a, ['title' => 'A msg']);
        $mb = $this->message($this->b, ['title' => 'B msg']);

        $d = app(PushDispatcher::class);
        $d->promoteDue();
        $d->process($ma->id, 0);
        $d->process($mb->id, 0);

        foreach ($this->fake->calls as $call) {
            $expected = $call['payload']['title'] === 'A msg' ? $aSubs : $bSubs;
            $this->assertEqualsCanonicalizing($expected, $call['ids']);
        }
        $this->assertCount(2, $this->fake->calls);
    }

    public function test_payload_has_branding_link_and_click_info(): void
    {
        $this->subs($this->a, 1);
        $this->deliver($this->message($this->a, ['title' => str_repeat('T', 95), 'url' => null]));

        $p = $this->fake->calls[0]['payload'];
        $this->assertLessThanOrEqual(80, mb_strlen($p['title']));
        $this->assertSame('https://alpha.example', $p['url']);
        $this->assertSame($this->a->public_key, $p['key']);
        $this->assertStringEndsWith('/api/v1', $p['api']);
        $this->assertStringContainsString('default-192.png', $p['icon']);
    }

    public function test_expired_subscriptions_are_removed_and_counted(): void
    {
        [$ok, $gone, $bad] = $this->subs($this->a, 3);
        $this->fake->results[$gone] = ['ok' => false, 'expired' => true, 'reason' => 'gone'];
        $this->fake->results[$bad] = ['ok' => false, 'expired' => false, 'reason' => 'timeout'];

        $done = $this->deliver($this->message($this->a));

        $this->assertSame([1, 1, 1], [$done->success_count, $done->failure_count, $done->expired_count]);
        $this->assertNull(PushSubscription::withoutBusinessScope()->find($gone));
        $this->assertSame(1, PushSubscription::withoutBusinessScope()->find($bad)->fail_count);
        $this->assertNotNull(PushSubscription::withoutBusinessScope()->find($ok)->last_success_at);
    }

    public function test_repeatedly_failing_subscriptions_are_dropped(): void
    {
        [$bad] = $this->subs($this->a, 1);
        $this->fake->results[$bad] = ['ok' => false, 'expired' => false, 'reason' => 'boom'];

        foreach (range(1, 5) as $i) {
            $this->deliver($this->message($this->a));
        }

        $this->assertNull(PushSubscription::withoutBusinessScope()->find($bad));
    }

    public function test_batches_resume_from_the_cursor(): void
    {
        config(['runwrk.push.batch_size' => 2]);
        $ids = $this->subs($this->a, 5);
        $m = $this->message($this->a);

        $first = $this->deliver($m, 1);
        $this->assertSame('sending', $first->status);
        $this->assertSame(2, $first->success_count);

        $last = $this->deliver($m, 3);
        $this->assertSame('sent', $last->status);
        $this->assertSame(5, $last->success_count);
        $this->assertSame($ids, $this->fake->sentIds());
    }

    public function test_audience_is_frozen_when_sending_starts(): void
    {
        $this->subs($this->a, 2);
        $m = $this->message($this->a);
        app(PushDispatcher::class)->promoteDue();

        $late = $this->subs($this->a, 1)[0];
        app(PushDispatcher::class)->process($m->id, 0);
        app(PushDispatcher::class)->process($m->id, 0);

        $this->assertNotContains($late, $this->fake->sentIds());
        $this->assertSame(2, PushMessage::withoutBusinessScope()->find($m->id)->success_count);
    }

    public function test_future_messages_wait_and_cancelled_ones_stop(): void
    {
        $this->subs($this->a, 2);
        $future = $this->message($this->a, ['scheduled_at' => now()->addHour()]);

        $this->assertSame(0, app(PushDispatcher::class)->promoteDue());
        $this->assertSame('scheduled', $future->fresh()->status);

        $m = $this->message($this->a);
        app(PushDispatcher::class)->promoteDue();
        PushMessage::withoutBusinessScope()->whereKey($m->id)->update(['status' => 'cancelled']);
        app(PushDispatcher::class)->process($m->id, 0);

        $this->assertSame([], $this->fake->calls);
    }

    public function test_a_locked_message_is_not_processed_twice(): void
    {
        $this->subs($this->a, 2);
        $m = $this->message($this->a);
        app(PushDispatcher::class)->promoteDue();
        PushMessage::withoutBusinessScope()->whereKey($m->id)->update(['locked_until' => now()->addMinute()]);

        app(PushDispatcher::class)->process($m->id, 0);

        $this->assertSame([], $this->fake->calls);
    }

    public function test_message_with_no_subscribers_finishes_cleanly(): void
    {
        $done = $this->deliver($this->message($this->a), 2);

        $this->assertSame('sent', $done->status);
        $this->assertSame(0, $done->target_count);
    }

    public function test_dispatch_command_queues_a_job_per_sending_message(): void
    {
        Queue::fake();
        $this->subs($this->a, 1);
        $m = $this->message($this->a);

        $this->artisan('runwrk:push-dispatch')->assertSuccessful();

        Queue::assertPushed(SendPushBatch::class, fn ($job) => $job->messageId === $m->id);
        $this->assertSame('sending', $m->fresh()->status);
    }

    public function test_push_run_command_sends_end_to_end(): void
    {
        $this->subs($this->a, 2);
        $m = $this->message($this->a);

        $this->artisan('runwrk:push-run')->assertSuccessful();

        $this->assertSame('sent', PushMessage::withoutBusinessScope()->find($m->id)->status);
    }

    public function test_subscriptions_and_messages_are_invisible_without_a_business(): void
    {
        $this->subs($this->a, 2);
        $this->message($this->a);

        $this->assertSame(0, PushSubscription::count());
        $this->assertSame(0, PushMessage::count());
    }
}

<?php

namespace App\Domain\Push;

use App\Domain\Tenancy\CurrentBusiness;
use App\Models\PushMessage;
use App\Models\PushSubscription;

/**
 * Sends a message in resumable batches. State lives on the message row (cursor + counters),
 * so it works the same from a per-minute cron, an after-response call, or a long-running worker.
 */
class PushDispatcher
{
    public function __construct(private PushTransport $transport, private CurrentBusiness $current) {}

    /** Moves due scheduled messages to "sending" and freezes their audience. */
    public function promoteDue(): int
    {
        $count = 0;

        PushMessage::withoutBusinessScope()->where('status', 'scheduled')->where('scheduled_at', '<=', now())->orderBy('id')->each(function (PushMessage $m) use (&$count) {
            $this->current->run($m->business, function () use ($m, &$count) {
                $audience = PushSubscription::query();
                $claimed = PushMessage::withoutBusinessScope()->where('id', $m->id)->where('status', 'scheduled')->update([
                    'status' => 'sending', 'started_at' => now(),
                    'max_id' => (int) $audience->max('id'), 'target_count' => $audience->count(),
                ]);
                $count += $claimed;
            });
        });

        return $count;
    }

    /** @return array<int, int> ids of messages still being sent */
    public function sendingIds(): array
    {
        return PushMessage::withoutBusinessScope()->where('status', 'sending')->pluck('id')->all();
    }

    public function process(int $messageId, int $budgetSeconds = 45): void
    {
        $claimed = PushMessage::withoutBusinessScope()->where('id', $messageId)->where('status', 'sending')
            ->where(fn ($q) => $q->whereNull('locked_until')->orWhere('locked_until', '<', now()))
            ->update(['locked_until' => now()->addSeconds($budgetSeconds + 30)]);

        if (! $claimed) {
            return;
        }

        $message = PushMessage::withoutBusinessScope()->with('business')->findOrFail($messageId);
        $deadline = microtime(true) + $budgetSeconds;

        try {
            $this->current->run($message->business, function () use ($message, $deadline) {
                do {
                    $message->refresh();

                    if ($message->status !== 'sending') {
                        return;
                    }

                    $batch = PushSubscription::where('id', '>', $message->cursor_id)->where('id', '<=', $message->max_id)
                        ->orderBy('id')->limit(config('runwrk.push.batch_size'))->get();

                    if ($batch->isEmpty()) {
                        $message->forceFill(['status' => 'sent', 'finished_at' => now()])->save();

                        return;
                    }

                    $this->sendBatch($message, $batch);

                    if ($batch->count() < config('runwrk.push.batch_size')) {
                        $message->forceFill(['status' => 'sent', 'finished_at' => now()])->save();

                        return;
                    }
                } while (microtime(true) < $deadline);
            });
        } finally {
            PushMessage::withoutBusinessScope()->where('id', $messageId)->update(['locked_until' => null]);
        }
    }

    private function sendBatch(PushMessage $message, $batch): void
    {
        $results = $this->transport->send($batch, Payload::build($message, $message->business), config('runwrk.push.ttl'));

        $ok = $failed = $expired = 0;
        $deleteIds = [];

        foreach ($batch as $sub) {
            $r = $results[$sub->id] ?? ['ok' => false, 'expired' => false, 'reason' => 'no result'];

            if ($r['ok']) {
                $ok++;
                $sub->forceFill(['last_success_at' => now(), 'fail_count' => 0])->save();
            } elseif ($r['expired']) {
                $expired++;
                $deleteIds[] = $sub->id;
            } else {
                $failed++;
                $sub->increment('fail_count');

                if ($sub->fail_count >= config('runwrk.push.drop_after_failures')) {
                    $deleteIds[] = $sub->id;
                }
            }
        }

        PushSubscription::whereIn('id', $deleteIds)->delete();

        PushMessage::withoutBusinessScope()->where('id', $message->id)->update([
            'cursor_id' => $batch->last()->id,
            'success_count' => \DB::raw('success_count + '.$ok),
            'failure_count' => \DB::raw('failure_count + '.$failed),
            'expired_count' => \DB::raw('expired_count + '.$expired),
        ]);
    }
}

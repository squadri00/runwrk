<?php

namespace Tests\Support;

use App\Domain\Push\PushTransport;
use Illuminate\Support\Collection;

class FakeTransport implements PushTransport
{
    /** @var array<int, array{ids: array<int,int>, payload: array}> */
    public array $calls = [];

    /** @var array<int, array{ok: bool, expired: bool, reason: ?string}> overrides keyed by subscription id */
    public array $results = [];

    public function send(Collection $subscriptions, array $payload, int $ttl): array
    {
        $this->calls[] = ['ids' => $subscriptions->pluck('id')->all(), 'payload' => $payload];

        return $subscriptions->mapWithKeys(fn ($s) => [$s->id => $this->results[$s->id] ?? ['ok' => true, 'expired' => false, 'reason' => null]])->all();
    }

    public function sentIds(): array
    {
        return collect($this->calls)->pluck('ids')->flatten()->all();
    }
}

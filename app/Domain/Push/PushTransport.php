<?php

namespace App\Domain\Push;

use Illuminate\Support\Collection;

interface PushTransport
{
    /**
     * @param  Collection<int, \App\Models\PushSubscription>  $subscriptions
     * @return array<int, array{ok: bool, expired: bool, reason: ?string}> keyed by subscription id
     */
    public function send(Collection $subscriptions, array $payload, int $ttl): array;
}

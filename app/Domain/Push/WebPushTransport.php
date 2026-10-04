<?php

namespace App\Domain\Push;

use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use Minishlink\WebPush\Subscription;

class WebPushTransport implements PushTransport
{
    public function send(Collection $subscriptions, array $payload, int $ttl): array
    {
        $vapid = config('runwrk.vapid');

        if (! $vapid['public'] || ! $vapid['private']) {
            throw new \RuntimeException('VAPID keys are not set. Run: php artisan runwrk:vapid-generate');
        }

        $http = new Client(['timeout' => 15, 'connect_timeout' => 5, 'http_errors' => false]);

        $webPush = new ConcurrentWebPush(
            ['VAPID' => ['subject' => $vapid['subject'], 'publicKey' => $vapid['public'], 'privateKey' => $vapid['private']]],
            ['TTL' => $ttl, 'urgency' => config('runwrk.push.urgency', 'high')],
            $http,
        );

        $byEndpoint = [];
        $json = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        foreach ($subscriptions as $sub) {
            $byEndpoint[$sub->endpoint] = $sub->id;
            $webPush->queueNotification(Subscription::create([
                'endpoint' => $sub->endpoint,
                'publicKey' => $sub->p256dh,
                'authToken' => $sub->auth,
                'contentEncoding' => $sub->content_encoding,
            ]), $json);
        }

        $results = [];

        foreach ($webPush->flushConcurrently($http) as $report) {
            $id = $byEndpoint[$report->getEndpoint()] ?? null;

            if ($id !== null) {
                $results[$id] = [
                    'ok' => $report->isSuccess(),
                    'expired' => $report->isSubscriptionExpired(),
                    'reason' => $report->isSuccess() ? null : substr((string) $report->getReason(), 0, 200),
                ];
            }
        }

        return $results;
    }
}

<?php

namespace App\Domain\Push;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\Utils;
use Minishlink\WebPush\MessageSentReport;
use Minishlink\WebPush\WebPush;

/** Sends the queued notifications in parallel with Guzzle's curl multi handler. */
class ConcurrentWebPush extends WebPush
{
    /** @return array<int, MessageSentReport> */
    public function flushConcurrently(Client $http): array
    {
        $requests = $this->prepare($this->notifications);
        $this->notifications = [];

        $promises = array_map(
            fn ($request) => $http->sendAsync($request, ['http_errors' => false])->then(
                fn ($response) => $this->createReport($request, $response),
                fn (\Throwable $e) => $this->createRejectedReport($request, $e),
            ),
            $requests,
        );

        return Utils::unwrap($promises);
    }
}

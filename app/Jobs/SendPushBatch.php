<?php

namespace App\Jobs;

use App\Domain\Push\PushDispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendPushBatch implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public function __construct(public int $messageId, public int $budget = 45) {}

    public function handle(PushDispatcher $dispatcher): void
    {
        $dispatcher->process($this->messageId, $this->budget);
    }
}

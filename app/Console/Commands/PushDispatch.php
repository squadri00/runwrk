<?php

namespace App\Console\Commands;

use App\Domain\Push\PushDispatcher;
use App\Jobs\SendPushBatch;
use Illuminate\Console\Command;

class PushDispatch extends Command
{
    protected $signature = 'runwrk:push-dispatch';

    protected $description = 'Start due scheduled messages and queue a sender job for every message being sent (runs every minute)';

    public function handle(PushDispatcher $dispatcher): int
    {
        $started = $dispatcher->promoteDue();

        foreach ($dispatcher->sendingIds() as $id) {
            SendPushBatch::dispatch($id);
        }

        $this->info("Started {$started} message(s).");

        return self::SUCCESS;
    }
}

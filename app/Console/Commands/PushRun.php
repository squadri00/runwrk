<?php

namespace App\Console\Commands;

use App\Domain\Push\PushDispatcher;
use Illuminate\Console\Command;

class PushRun extends Command
{
    protected $signature = 'runwrk:push-run {--budget=45}';

    protected $description = 'Start due messages and send them right now in this process (local testing, no cron needed)';

    public function handle(PushDispatcher $dispatcher): int
    {
        $this->info('Started '.$dispatcher->promoteDue().' message(s).');

        foreach ($dispatcher->sendingIds() as $id) {
            $dispatcher->process($id, (int) $this->option('budget'));
            $this->line("Message {$id} processed.");
        }

        return self::SUCCESS;
    }
}

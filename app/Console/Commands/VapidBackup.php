<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class VapidBackup extends Command
{
    protected $signature = 'runwrk:vapid-backup {--show : Print the keys instead of saving a file}';

    protected $description = 'Save a copy of the VAPID keys (and APP_KEY) outside .env';

    public function handle(): int
    {
        $public = config('runwrk.vapid.public');
        $private = config('runwrk.vapid.private');

        if (! $public || ! $private) {
            $this->error('No VAPID keys configured.');

            return self::FAILURE;
        }

        $text = "VAPID_PUBLIC_KEY={$public}\nVAPID_PRIVATE_KEY={$private}\nAPP_KEY=".config('app.key')."\n";

        if ($this->option('show')) {
            $this->line($text);

            return self::SUCCESS;
        }

        $dir = storage_path('backups');
        is_dir($dir) || mkdir($dir, 0700, true);
        $file = $dir.'/vapid-'.now()->format('Ymd-His').'.txt';
        file_put_contents($file, $text);

        $this->info("Saved: {$file}");
        $this->warn('Move this file somewhere safe (password manager or private drive) and keep it off the server and out of git.');

        return self::SUCCESS;
    }
}

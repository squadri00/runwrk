<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Minishlink\WebPush\VAPID;

class VapidGenerate extends Command
{
    protected $signature = 'runwrk:vapid-generate';

    protected $description = 'Create the platform VAPID key pair and write it to .env (refuses to overwrite)';

    public function handle(): int
    {
        if (config('runwrk.vapid.public') || config('runwrk.vapid.private')) {
            $this->error('VAPID keys already exist. Replacing them would break every subscriber, so this command will not overwrite them.');
            $this->line('Run `php artisan runwrk:vapid-backup` to save a copy.');

            return self::FAILURE;
        }

        $keys = VAPID::createVapidKeys();
        $path = base_path('.env');

        if (! is_file($path)) {
            $this->error('.env not found.');

            return self::FAILURE;
        }

        $env = file_get_contents($path);

        foreach (['VAPID_PUBLIC_KEY' => $keys['publicKey'], 'VAPID_PRIVATE_KEY' => $keys['privateKey']] as $name => $value) {
            $env = preg_match("/^{$name}=.*/m", $env)
                ? preg_replace("/^{$name}=.*/m", "{$name}={$value}", $env)
                : rtrim($env)."\n{$name}={$value}\n";
        }

        file_put_contents($path, $env);

        $this->info('VAPID keys written to .env.');
        $this->warn('Back them up now: php artisan runwrk:vapid-backup. If they are lost, every subscriber must re-subscribe.');

        return self::SUCCESS;
    }
}

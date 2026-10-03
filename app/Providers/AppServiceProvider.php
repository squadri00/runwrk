<?php

namespace App\Providers;

use App\Domain\Ops\PlatformSettings;
use App\Domain\Tenancy\CurrentBusiness;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CurrentBusiness::class);
        $this->app->bind(\App\Domain\Push\PushTransport::class, \App\Domain\Push\WebPushTransport::class);
    }

    public function boot(): void
    {
        PlatformSettings::applyToConfig();
    }
}

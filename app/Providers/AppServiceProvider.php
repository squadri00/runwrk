<?php

namespace App\Providers;

use App\Domain\Tenancy\CurrentBusiness;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CurrentBusiness::class);
    }

    public function boot(): void
    {
        //
    }
}

<?php

use App\Domain\Tenancy\CurrentBusiness;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['api.business', 'throttle:120,1'])->group(function () {
    Route::match(['get', 'options'], 'ping', fn (CurrentBusiness $current) => [
        'ok' => true,
        'business' => $current->getOrFail()->name,
    ]);
});

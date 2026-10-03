<?php

use App\Domain\Tenancy\CurrentBusiness;
use App\Http\Controllers\Api\PushApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['api.business', 'throttle:120,1'])->group(function () {
    Route::match(['get', 'options'], 'ping', fn (CurrentBusiness $current) => [
        'ok' => true,
        'business' => $current->getOrFail()->name,
    ]);

    Route::match(['get', 'options'], 'config', [PushApiController::class, 'config']);
    Route::match(['get', 'options'], 'manifest', [PushApiController::class, 'manifest']);
    Route::match(['post', 'options'], 'subscribe', [PushApiController::class, 'subscribe'])->middleware('throttle:20,1');
    Route::match(['post', 'options'], 'unsubscribe', [PushApiController::class, 'unsubscribe'])->middleware('throttle:20,1');
    Route::match(['post', 'options'], 'click', [PushApiController::class, 'click'])->middleware('throttle:60,1');
});

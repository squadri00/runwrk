<?php

use App\Domain\Tenancy\CurrentBusiness;
use App\Http\Controllers\Api\PushApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['api.business', 'throttle:120,1,api'])->group(function () {
    Route::match(['get', 'options'], 'ping', fn (CurrentBusiness $current) => [
        'ok' => true,
        'business' => $current->getOrFail()->name,
    ]);

    Route::match(['get', 'options'], 'config', [PushApiController::class, 'config']);
    Route::match(['get', 'options'], 'manifest', [PushApiController::class, 'manifest']);
    Route::match(['post', 'options'], 'subscribe', [PushApiController::class, 'subscribe'])->middleware('throttle:20,1,subscribe');
    Route::match(['post', 'options'], 'unsubscribe', [PushApiController::class, 'unsubscribe'])->middleware('throttle:20,1,unsubscribe');
    Route::match(['post', 'options'], 'click', [PushApiController::class, 'click'])->middleware('throttle:60,1,click');
    Route::match(['post', 'options'], 'received', [PushApiController::class, 'received'])->middleware('throttle:120,1,received');
    Route::match(['post', 'options'], 'test', [PushApiController::class, 'test'])->middleware('throttle:4,1,pushtest');
});

<?php

use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BusinessController;
use App\Http\Controllers\Admin\ImpersonationController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AuthController::class, 'show'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:5,1');

    Route::middleware('auth:superadmin')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/', [BusinessController::class, 'home'])->name('home');
        Route::get('audit', [AuditController::class, 'index'])->name('audit');

        Route::get('businesses', [BusinessController::class, 'index'])->name('businesses.index');
        Route::get('businesses/create', [BusinessController::class, 'create'])->name('businesses.create');
        Route::post('businesses', [BusinessController::class, 'store'])->name('businesses.store');
        Route::get('businesses/{business}', [BusinessController::class, 'show'])->name('businesses.show');
        Route::get('businesses/{business}/edit', [BusinessController::class, 'edit'])->name('businesses.edit');
        Route::put('businesses/{business}', [BusinessController::class, 'update'])->name('businesses.update');
        Route::post('businesses/{business}/key', [BusinessController::class, 'regenerateKey'])->name('businesses.key');
        Route::post('businesses/{business}/impersonate', [ImpersonationController::class, 'start'])->name('businesses.impersonate');
    });
});

Route::middleware(['auth:web', 'business'])->prefix('dashboard')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('stop-impersonating', [ImpersonationController::class, 'stop'])->name('impersonation.stop');
});

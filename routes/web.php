<?php

use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\BusinessController;
use App\Http\Controllers\Admin\ImpersonationController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\SecurityController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\TwoFactorChallengeController;
use App\Http\Controllers\App\AccountController;
use App\Http\Controllers\App\BrandingController;
use App\Http\Controllers\App\ConnectController;
use App\Http\Controllers\App\TeamController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\RegistrationOtpController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HostedAppController;
use App\Http\Controllers\App\NotificationController;
use App\Http\Controllers\App\SubscriberController;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SiteController::class, 'home'])->name('home');
Route::get('web-design', [SiteController::class, 'webDesign'])->name('web-design');
Route::get('your-app', [SiteController::class, 'yourApp'])->name('your-app');
Route::get('pricing', [SiteController::class, 'pricing'])->name('pricing');
Route::get('demo', [SiteController::class, 'demo'])->name('demo');
Route::get('contact', [SiteController::class, 'contact'])->name('contact');
Route::post('contact', [SiteController::class, 'sendContact'])->middleware('throttle:5,1,contact')->name('contact.send');
Route::get('privacy', [SiteController::class, 'privacy'])->name('privacy');
Route::get('terms', [SiteController::class, 'terms'])->name('terms');
Route::get('sitemap.xml', [SiteController::class, 'sitemap'])->name('sitemap');

Route::middleware('guest:web')->group(function () {
    Route::get('login', [LoginController::class, 'show'])->name('login');
    Route::post('login', [LoginController::class, 'store']);

    Route::get('register', [RegisterController::class, 'show'])->name('register');
    Route::post('register', [RegisterController::class, 'store'])->middleware('throttle:10,1,register');
    Route::get('register/verify/{token}', [RegistrationOtpController::class, 'show'])->name('register.verify');
    Route::post('register/verify/{token}', [RegistrationOtpController::class, 'verify'])->middleware('throttle:10,1,verify')->name('register.verify.submit');
    Route::post('register/verify/{token}/resend', [RegistrationOtpController::class, 'resend'])->middleware('throttle:3,1,resend')->name('register.verify.resend');

    Route::get('password/forgot', [PasswordResetController::class, 'request'])->name('password.request');
    Route::post('password/forgot', [PasswordResetController::class, 'email'])->middleware('throttle:5,1,pwforgot')->name('password.email');
    Route::get('password/reset/{token}', [PasswordResetController::class, 'reset'])->name('password.reset');
    Route::post('password/reset', [PasswordResetController::class, 'update'])->middleware('throttle:10,1,pwreset')->name('password.update');
});

Route::post('logout', [LoginController::class, 'destroy'])->middleware('auth:web')->name('logout');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:superadmin')->group(function () {
        Route::get('login', [AdminAuthController::class, 'show'])->name('login');
        Route::post('login', [AdminAuthController::class, 'login']);
        Route::get('two-factor', [TwoFactorChallengeController::class, 'show'])->name('2fa');
        Route::post('two-factor', [TwoFactorChallengeController::class, 'verify'])->middleware('throttle:10,1,admin2fa');
    });

    Route::middleware('auth:superadmin')->group(function () {
        Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/', [BusinessController::class, 'home'])->name('home');
        Route::get('messages', [MessageController::class, 'index'])->name('messages.index');
        Route::get('messages/{message}', [MessageController::class, 'show'])->name('messages.show');
        Route::delete('messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');
        Route::get('audit', [AuditController::class, 'index'])->name('audit');

        Route::get('businesses', [BusinessController::class, 'index'])->name('businesses.index');
        Route::get('businesses/create', [BusinessController::class, 'create'])->name('businesses.create');
        Route::post('businesses', [BusinessController::class, 'store'])->name('businesses.store');
        Route::get('businesses/{business}', [BusinessController::class, 'show'])->name('businesses.show');
        Route::get('businesses/{business}/edit', [BusinessController::class, 'edit'])->name('businesses.edit');
        Route::put('businesses/{business}', [BusinessController::class, 'update'])->name('businesses.update');
        Route::post('businesses/{business}/key', [BusinessController::class, 'regenerateKey'])->name('businesses.key');
        Route::post('businesses/{business}/impersonate', [ImpersonationController::class, 'start'])->name('businesses.impersonate');
        Route::post('businesses/{business}/users/{user}/invite', [BusinessController::class, 'resendInvite'])->name('businesses.invite');

        Route::get('plans', [PlanController::class, 'index'])->name('plans.index');
        Route::get('plans/create', [PlanController::class, 'create'])->name('plans.create');
        Route::post('plans', [PlanController::class, 'store'])->name('plans.store');
        Route::get('plans/{plan}/edit', [PlanController::class, 'edit'])->name('plans.edit');
        Route::put('plans/{plan}', [PlanController::class, 'update'])->name('plans.update');
        Route::post('plans/{plan}/archive', [PlanController::class, 'archive'])->name('plans.archive');

        Route::get('settings', [SettingsController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
        Route::post('settings/test-mail', [SettingsController::class, 'testMail'])->middleware('throttle:5,1,testmail')->name('settings.test-mail');

        Route::get('security', [SecurityController::class, 'show'])->name('security');
        Route::post('security/enable', [SecurityController::class, 'enable'])->name('security.enable');
        Route::post('security/codes', [SecurityController::class, 'regenerate'])->name('security.codes');
        Route::post('security/disable', [SecurityController::class, 'disable'])->name('security.disable');
    });
});

Route::middleware(['auth:web', 'business'])->prefix('dashboard')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('stop-impersonating', [ImpersonationController::class, 'stop'])->name('impersonation.stop');

    Route::get('account', [AccountController::class, 'edit'])->name('account.edit');
    Route::put('account', [AccountController::class, 'update'])->name('account.update');

    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/new', [NotificationController::class, 'create'])->name('notifications.create');
    Route::post('notifications', [NotificationController::class, 'store'])->middleware('throttle:30,1,sendpush')->name('notifications.store');
    Route::get('notifications/{id}', [NotificationController::class, 'show'])->whereNumber('id')->name('notifications.show');
    Route::post('notifications/{id}/cancel', [NotificationController::class, 'cancel'])->whereNumber('id')->name('notifications.cancel');
    Route::get('subscribers', [SubscriberController::class, 'index'])->name('subscribers.index');

    Route::get('connect', [ConnectController::class, 'index'])->name('connect.index');
    Route::get('connect/runwrk-sw.js', [ConnectController::class, 'worker'])->name('connect.worker');
    Route::get('connect/runwrk-manifest.webmanifest', [ConnectController::class, 'manifest'])->name('connect.manifest');
    Route::get('connect/runwrk-connect.zip', [ConnectController::class, 'gravPlugin'])->name('connect.grav');

    Route::middleware('owner')->group(function () {
        Route::get('branding', [BrandingController::class, 'edit'])->name('branding.edit');
        Route::put('branding', [BrandingController::class, 'update'])->name('branding.update');

        Route::get('team', [TeamController::class, 'index'])->name('team.index');
        Route::post('team', [TeamController::class, 'store'])->name('team.store');
        Route::post('team/{id}/invite', [TeamController::class, 'resend'])->name('team.invite');
        Route::delete('team/{id}', [TeamController::class, 'destroy'])->name('team.destroy');
    });
});

// Hosted app for each business. Keep last: any unmatched single segment is treated as a slug.
Route::prefix('{business:slug}')->where(['business' => '[a-z0-9]+(-[a-z0-9]+)*'])->group(function () {
    Route::get('/', [HostedAppController::class, 'page'])->name('hosted.page');
    Route::get('manifest.webmanifest', [HostedAppController::class, 'manifest'])->name('hosted.manifest');
    Route::get('sw.js', [HostedAppController::class, 'serviceWorker'])->name('hosted.sw');
    Route::get('icons/{file}', [HostedAppController::class, 'icon'])->name('hosted.icon');
});

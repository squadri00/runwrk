<?php

namespace App\Domain\Ops;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

/**
 * Platform-wide settings edited in the super admin. Mail values override .env at runtime.
 */
class PlatformSettings
{
    public const MAIL = ['host', 'port', 'encryption', 'username', 'from_address', 'from_name'];

    public static function all(): array
    {
        try {
            return Cache::remember('platform.settings', 300, fn () => [
                'mail' => collect(self::MAIL)->mapWithKeys(fn ($k) => [$k => Settings::get("mail.$k")])->all(),
                'mail_password_set' => Settings::get('mail.password') !== null,
                'app_name' => Settings::get('app_name'),
                'support_email' => Settings::get('support_email'),
                'signups_enabled' => Settings::get('signups_enabled', true),
            ]);
        } catch (\Throwable) {
            return [];
        }
    }

    public static function forget(): void
    {
        Cache::forget('platform.settings');
    }

    public static function signupsEnabled(): bool
    {
        return (bool) (self::all()['signups_enabled'] ?? true);
    }

    public static function applyToConfig(): void
    {
        $s = self::all();

        if ($s === []) {
            return;
        }

        if ($name = $s['app_name']) {
            Config::set('app.name', $name);
            Config::set('mail.from.name', $name);
        }

        $m = $s['mail'];

        if (! empty($m['host'])) {
            $password = null;
            try {
                $password = Settings::getSecret('mail.password');
            } catch (\Throwable) {
            }

            Config::set('mail.default', 'smtp');
            Config::set('mail.mailers.smtp', array_merge(config('mail.mailers.smtp'), [
                'host' => $m['host'],
                'port' => (int) ($m['port'] ?: 587),
                'scheme' => ($m['encryption'] ?? '') === 'ssl' ? 'smtps' : 'smtp',
                'username' => $m['username'] ?: null,
                'password' => $password,
            ]));
        }

        if (! empty($m['from_address'])) {
            Config::set('mail.from.address', $m['from_address']);
        }

        if (! empty($m['from_name'])) {
            Config::set('mail.from.name', $m['from_name']);
        }
    }
}

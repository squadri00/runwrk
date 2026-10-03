<?php

namespace App\Domain\Ops;

use App\Models\Setting;
use Illuminate\Support\Facades\Crypt;

/**
 * Global settings use business_id = null. Secrets (Stripe, SMTP) are stored
 * encrypted with APP_KEY: Settings::putSecret / getSecret.
 */
class Settings
{
    public static function get(string $key, mixed $default = null, ?int $businessId = null): mixed
    {
        $row = Setting::where('key', $key)->where('business_id', $businessId)->first();

        if (! $row && $businessId !== null) {
            return self::get($key, $default);
        }

        return $row ? json_decode($row->value, true) : $default;
    }

    public static function put(string $key, mixed $value, ?int $businessId = null): void
    {
        Setting::updateOrCreate(['key' => $key, 'business_id' => $businessId], ['value' => json_encode($value)]);
    }

    public static function putSecret(string $key, string $value, ?int $businessId = null): void
    {
        self::put($key, Crypt::encryptString($value), $businessId);
    }

    public static function getSecret(string $key, ?int $businessId = null): ?string
    {
        $value = self::get($key, null, $businessId);

        return $value ? Crypt::decryptString($value) : null;
    }
}

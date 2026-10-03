<?php

namespace App\Models;

use App\Domain\Tenancy\BelongsToBusiness;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['business_id', 'endpoint', 'endpoint_hash', 'p256dh', 'auth', 'content_encoding', 'platform', 'source', 'origin', 'user_agent', 'last_success_at', 'fail_count'])]
#[Hidden(['endpoint', 'p256dh', 'auth'])]
class PushSubscription extends Model
{
    use BelongsToBusiness;

    protected function casts(): array
    {
        return ['last_success_at' => 'datetime'];
    }

    public static function hashEndpoint(string $endpoint): string
    {
        return hash('sha256', $endpoint);
    }

    public static function platformFromUserAgent(?string $ua): string
    {
        return match (true) {
            (bool) preg_match('/iPhone|iPad|iPod/i', (string) $ua) => 'ios',
            (bool) preg_match('/Android/i', (string) $ua) => 'android',
            (bool) preg_match('/Windows|Macintosh|Linux|CrOS/i', (string) $ua) => 'desktop',
            default => 'other',
        };
    }
}

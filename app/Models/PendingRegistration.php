<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

#[Fillable(['token', 'name', 'business_name', 'email', 'password', 'plan_id', 'otp_code', 'otp_expires_at', 'otp_attempts', 'finalized_at', 'business_id'])]
#[Hidden(['password', 'otp_code'])]
class PendingRegistration extends Model
{
    protected function casts(): array
    {
        return ['otp_expires_at' => 'datetime', 'finalized_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::creating(fn (self $p) => $p->token ??= Str::random(48));
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function isFinalized(): bool
    {
        return $this->finalized_at !== null;
    }

    public function issueOtp(): string
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $this->forceFill(['otp_code' => Hash::make($code), 'otp_expires_at' => now()->addMinutes(15), 'otp_attempts' => 0])->save();

        return $code;
    }

    public function otpMatches(string $code): bool
    {
        return $this->otp_code !== null
            && $this->otp_expires_at?->isFuture()
            && $this->otp_attempts < 5
            && Hash::check(trim($code), $this->otp_code);
    }
}

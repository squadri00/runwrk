<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable(['name', 'slug', 'status', 'plan_id', 'short_name', 'logo_path', 'icon_path', 'theme_color', 'background_color', 'phone', 'address', 'website_url', 'hours', 'timezone'])]
class Business extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUSES = ['trial', 'active', 'suspended', 'cancelled'];

    protected static function booted(): void
    {
        static::creating(function (Business $business) {
            $business->public_key ??= self::newPublicKey();
        });
    }

    public static function newPublicKey(): string
    {
        return 'pk_'.Str::random(32);
    }

    protected function casts(): array
    {
        return ['hours' => 'array'];
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function domains(): HasMany
    {
        return $this->hasMany(BusinessDomain::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function syncDomains(array $domains): void
    {
        $origins = app(\App\Domain\Api\OriginChecker::class);

        $clean = collect($domains)->map(fn ($d) => $origins->normalize((string) $d))->filter()->unique()->values();

        $this->domains()->whereNotIn('domain', $clean)->delete();
        foreach ($clean as $domain) {
            $this->domains()->firstOrCreate(['domain' => $domain]);
        }
    }

    public function isLive(): bool
    {
        return in_array($this->status, ['trial', 'active'], true);
    }
}

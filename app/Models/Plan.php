<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'code', 'description', 'price_cents', 'currency', 'interval', 'stripe_price_id', 'features', 'is_public', 'sort_order', 'archived_at'])]
class Plan extends Model
{
    protected function casts(): array
    {
        return ['features' => 'array', 'is_public' => 'boolean', 'archived_at' => 'datetime'];
    }

    public function businesses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Business::class);
    }

    public function scopeAvailable(Builder $query): void
    {
        $query->whereNull('archived_at');
    }

    public function scopePublic(Builder $query): void
    {
        $query->whereNull('archived_at')->where('is_public', true)->orderBy('sort_order')->orderBy('price_cents');
    }

    public function isFree(): bool
    {
        return $this->price_cents === 0;
    }

    public function priceLabel(): string
    {
        return $this->isFree() ? 'Free' : '$'.number_format($this->price_cents / 100, $this->price_cents % 100 ? 2 : 0);
    }
}

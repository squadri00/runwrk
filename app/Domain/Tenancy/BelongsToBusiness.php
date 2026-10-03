<?php

namespace App\Domain\Tenancy;

use App\Models\Business;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToBusiness
{
    public static function bootBelongsToBusiness(): void
    {
        static::addGlobalScope(new BusinessScope());

        static::creating(function (Model $model) {
            if (! $model->getAttribute('business_id')) {
                $current = app(CurrentBusiness::class);
                if ($current->has()) {
                    $model->setAttribute('business_id', $current->id());
                }
            }
        });
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}

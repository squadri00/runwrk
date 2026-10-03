<?php

namespace App\Domain\Tenancy;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Fails closed: with no business resolved, tenant queries return nothing.
 * Crossing tenants must be explicit: ->withoutBusinessScope().
 */
class BusinessScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $current = app(CurrentBusiness::class);

        if ($current->has()) {
            $builder->where($model->getTable().'.business_id', $current->id());
        } else {
            $builder->whereRaw('1 = 0');
        }
    }

    public function extend(Builder $builder): void
    {
        $builder->macro('withoutBusinessScope', fn (Builder $builder) => $builder->withoutGlobalScope(static::class));
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'code', 'features', 'active'])]
class Plan extends Model
{
    protected function casts(): array
    {
        return ['features' => 'array', 'active' => 'boolean'];
    }
}

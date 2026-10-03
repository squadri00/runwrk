<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Foundation\Auth\User as Authenticatable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token', 'two_factor_secret'])]
class Superadmin extends Authenticatable
{
    protected function casts(): array
    {
        return ['password' => 'hashed', 'two_factor_confirmed_at' => 'datetime'];
    }
}

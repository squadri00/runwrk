<?php

namespace Tests\Support;

use App\Domain\Tenancy\BelongsToBusiness;
use Illuminate\Database\Eloquent\Model;

class TenantNote extends Model
{
    use BelongsToBusiness;

    protected $guarded = [];
}

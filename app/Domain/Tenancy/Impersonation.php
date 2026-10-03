<?php

namespace App\Domain\Tenancy;

use App\Models\Superadmin;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Impersonation
{
    private const KEY = 'impersonator_superadmin_id';

    public function start(User $asUser, Superadmin $by): void
    {
        Auth::guard('web')->login($asUser);
        Session::put(self::KEY, $by->getKey());
    }

    public function stop(): void
    {
        Auth::guard('web')->logout();
        Session::forget(self::KEY);
    }

    public function isActive(): bool
    {
        return Session::has(self::KEY);
    }

    public function impersonatorId(): ?int
    {
        return Session::get(self::KEY);
    }
}

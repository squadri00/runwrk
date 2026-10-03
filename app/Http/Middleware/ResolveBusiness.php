<?php

namespace App\Http\Middleware;

use App\Domain\Tenancy\CurrentBusiness;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ResolveBusiness
{
    public function __construct(private CurrentBusiness $current) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('web')->user();

        if ($user && $user->business) {
            abort_if(! $user->business->isLive(), 403, 'This account is not active.');
            $this->current->set($user->business);
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use App\Domain\Api\OriginChecker;
use App\Domain\Tenancy\CurrentBusiness;
use App\Models\Business;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveApiBusiness
{
    public function __construct(private CurrentBusiness $current, private OriginChecker $origins) {}

    public function handle(Request $request, Closure $next): Response
    {
        $key = $request->header('X-Runwrk-Key') ?: $request->query('key');

        $business = is_string($key) && $key !== ''
            ? Business::where('public_key', $key)->first()
            : null;

        if (! $business || ! $business->isLive()) {
            return response()->json(['error' => 'invalid_key'], 401);
        }

        $origin = $request->headers->get('Origin');

        if (! $this->origins->check($business, $origin, $request->headers->get('Referer'))) {
            return response()->json(['error' => 'origin_not_allowed'], 403);
        }

        $this->current->set($business);

        $response = $request->isMethod('OPTIONS') ? response('', 204) : $next($request);

        if ($origin) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, X-Runwrk-Key');
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
            $response->headers->set('Vary', 'Origin');
        }

        return $response;
    }
}

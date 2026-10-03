<?php

namespace App\Domain\Ops;

use App\Domain\Tenancy\CurrentBusiness;
use App\Models\AuditLog;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Audit
{
    public static function log(string $action, ?Model $subject = null, array $changes = [], ?int $businessId = null): AuditLog
    {
        $actor = null;
        foreach (['superadmin', 'web'] as $guard) {
            if (Auth::guard($guard)->check()) {
                $actor = Auth::guard($guard)->user();
                break;
            }
        }

        $request = request();

        return AuditLog::create([
            'business_id' => $businessId ?? app(CurrentBusiness::class)->id() ?? ($subject->business_id ?? null),
            'actor_type' => $actor ? $actor::class : null,
            'actor_id' => $actor?->getAuthIdentifier(),
            'actor_label' => self::label($actor),
            'action' => $action,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->getKey(),
            'changes' => $changes ?: null,
            'ip' => $request?->ip(),
            'user_agent' => $request ? substr((string) $request->userAgent(), 0, 255) : null,
            'created_at' => now(),
        ]);
    }

    private static function label(?Authenticatable $actor): string
    {
        return $actor ? trim(($actor->name ?? '?').' <'.($actor->email ?? '?').'>') : 'system';
    }
}

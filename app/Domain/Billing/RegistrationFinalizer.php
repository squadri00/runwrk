<?php

namespace App\Domain\Billing;

use App\Domain\Ops\Audit;
use App\Models\Business;
use App\Models\PendingRegistration;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RegistrationFinalizer
{
    public function finalize(PendingRegistration $pending): Business
    {
        return DB::transaction(function () use ($pending) {
            $business = Business::create([
                'name' => $pending->business_name,
                'slug' => Business::uniqueSlug($pending->business_name),
                'status' => 'trial',
                'plan_id' => $pending->plan_id,
                'short_name' => Str::limit($pending->business_name, 30, ''),
            ]);

            User::create(['business_id' => $business->id, 'name' => $pending->name, 'email' => $pending->email, 'role' => 'owner', 'password' => $pending->password]);

            $pending->forceFill(['finalized_at' => now(), 'business_id' => $business->id, 'otp_code' => null])->save();

            Audit::log('business.registered', $business, ['plan' => $pending->plan?->code], $business->id);

            return $business;
        });
    }
}

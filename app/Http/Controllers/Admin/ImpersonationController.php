<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Ops\Audit;
use App\Domain\Tenancy\Impersonation;
use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    public function start(Business $business, Impersonation $impersonation)
    {
        $owner = $business->users()->orderByRaw("role = 'owner' desc")->orderBy('id')->first();

        abort_unless($owner, 422, 'This business has no users.');

        Audit::log('impersonate.start', $owner, ['user' => $owner->email], $business->id);
        $impersonation->start($owner, Auth::guard('superadmin')->user());

        return redirect()->route('dashboard');
    }

    public function stop(Impersonation $impersonation)
    {
        abort_unless($impersonation->isActive(), 403);

        $user = Auth::guard('web')->user();
        Audit::log('impersonate.stop', $user, [], $user?->business_id);
        $impersonation->stop();

        return redirect()->route('admin.businesses.index');
    }
}

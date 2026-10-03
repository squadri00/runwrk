<?php

namespace App\Http\Controllers;

use App\Domain\Tenancy\CurrentBusiness;
use App\Models\PushMessage;
use App\Models\PushSubscription;

class DashboardController extends Controller
{
    public function index(CurrentBusiness $current)
    {
        $business = $current->getOrFail()->loadCount('domains', 'users');

        return view('app.dashboard', [
            'business' => $business,
            'subscribers' => PushSubscription::count(),
            'sent' => PushMessage::where('status', 'sent')->count(),
            'clicks' => PushMessage::where('created_at', '>=', now()->subDays(30))->sum('click_count'),
            'recent' => PushMessage::latest('id')->limit(5)->get(),
        ]);
    }
}

<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\PushSubscription;

class SubscriberController extends Controller
{
    public function index()
    {
        return view('app.subscribers', [
            'total' => PushSubscription::count(),
            'week' => PushSubscription::where('created_at', '>=', now()->subDays(7))->count(),
            'byPlatform' => PushSubscription::selectRaw('platform, count(*) c')->groupBy('platform')->pluck('c', 'platform'),
            'bySource' => PushSubscription::selectRaw('source, count(*) c')->groupBy('source')->pluck('c', 'source'),
            'recent' => PushSubscription::latest('id')->limit(25)->get(['id', 'platform', 'source', 'created_at', 'last_success_at']),
        ]);
    }
}

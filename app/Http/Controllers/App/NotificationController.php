<?php

namespace App\Http\Controllers\App;

use App\Domain\Ops\Audit;
use App\Domain\Push\PushDispatcher;
use App\Domain\Tenancy\CurrentBusiness;
use App\Http\Controllers\Controller;
use App\Jobs\SendPushBatch;
use App\Models\PushMessage;
use App\Models\PushSubscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        return view('app.notifications.index', [
            'messages' => PushMessage::with('author')->latest('id')->paginate(15),
            'subscribers' => PushSubscription::count(),
        ]);
    }

    public function create(CurrentBusiness $current)
    {
        return view('app.notifications.create', [
            'subscribers' => PushSubscription::count(),
            'business' => $current->getOrFail(),
        ]);
    }

    public function store(Request $request, CurrentBusiness $current, PushDispatcher $dispatcher)
    {
        $business = $current->getOrFail();

        $data = $request->validate([
            'title' => 'required|string|max:65',
            'body' => 'required|string|max:200',
            'url' => 'nullable|url:http,https|max:500',
            'image' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:1024',
            'when' => 'required|in:now,later',
            'send_at' => 'required_if:when,later|nullable|date_format:Y-m-d\TH:i',
        ]);

        if (PushSubscription::count() === 0) {
            return back()->withInput()->with('error', 'Nobody has turned on notifications yet, so there is no one to send to.');
        }

        $at = now();

        if ($data['when'] === 'later') {
            $at = Carbon::createFromFormat('Y-m-d\TH:i', $data['send_at'], $business->timezone)->utc();

            if ($at->lt(now()->addMinute()) || $at->gt(now()->addYear())) {
                return back()->withInput()->withErrors(['send_at' => 'Pick a time between a minute from now and a year from now.']);
            }
        }

        $message = PushMessage::create([
            'created_by' => Auth::guard('web')->id(),
            'title' => $data['title'],
            'body' => $data['body'],
            'url' => $data['url'] ?? null,
            'image_path' => $request->hasFile('image') ? $request->file('image')->store("push/{$business->id}", 'public') : null,
            'status' => 'scheduled',
            'scheduled_at' => $at,
        ]);

        Audit::log('push.create', $message, ['when' => $data['when'], 'at' => $at->toIso8601String()]);

        if ($data['when'] === 'now') {
            $dispatcher->promoteDue();
            SendPushBatch::dispatchAfterResponse($message->id);
        }

        return redirect()->route('notifications.show', $message)->with('status', $data['when'] === 'now' ? 'Sending now.' : 'Scheduled.');
    }

    public function show(int $id)
    {
        return view('app.notifications.show', ['message' => PushMessage::with('author')->findOrFail($id)]);
    }

    public function cancel(int $id)
    {
        $message = PushMessage::findOrFail($id);
        abort_unless($message->isCancellable(), 422);

        $message->forceFill(['status' => 'cancelled', 'finished_at' => now()])->save();
        Audit::log('push.cancel', $message);

        return back()->with('status', 'Cancelled.');
    }
}

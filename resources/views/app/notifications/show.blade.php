<x-layouts.app :title="$message->title">
    @if (in_array($message->status, ['sending']) || ($message->status === 'scheduled' && $message->scheduled_at->lte(now()->addMinute())))
        <meta http-equiv="refresh" content="5">
    @endif

    @php($tz = auth('web')->user()->business->timezone)

    <div class="max-w-2xl space-y-4">
        <div class="rounded-xl bg-white p-5 ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-base font-semibold">{{ $message->title }}</h2>
                    <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">{{ $message->body }}</p>
                    @if ($message->url)<p class="mt-2 break-all text-xs text-slate-400">{{ $message->url }}</p>@endif
                </div>
                <x-message-status :status="$message->status" />
            </div>
            @if ($message->image_path)<img src="{{ Storage::disk('public')->url($message->image_path) }}" alt="" class="mt-3 max-h-40 rounded-lg">@endif
            <p class="mt-3 text-xs text-slate-400">
                By {{ $message->author?->name ?? 'unknown' }} ·
                @if ($message->status === 'scheduled') scheduled for {{ $message->scheduled_at->timezone($tz)->format('M j, g:i a') }}
                @else started {{ $message->started_at?->timezone($tz)->format('M j, g:i a') }}@if ($message->finished_at), finished {{ $message->finished_at->timezone($tz)->format('g:i a') }}@endif
                @endif
            </p>
        </div>

        @if ($message->started_at)
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                @foreach ([['Audience', $message->target_count], ['Delivered', $message->success_count], ['Opened', $message->click_count], ['Not delivered', $message->failure_count + $message->expired_count]] as [$label, $value])
                    <div class="rounded-xl bg-white p-4 ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
                        <div class="text-2xl font-semibold">{{ number_format($value) }}</div>
                        <div class="mt-1 text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ $label }}</div>
                    </div>
                @endforeach
            </div>
            @if ($message->target_count)
                <div class="h-2 overflow-hidden rounded-full bg-slate-200 dark:bg-slate-800"><div class="h-full bg-emerald-600" style="width: {{ min(100, round($message->processed() / $message->target_count * 100)) }}%"></div></div>
            @endif
            @if ($message->expired_count)<p class="text-xs text-slate-400">{{ $message->expired_count }} customer(s) removed the app or turned notifications off, so they were taken off the list.</p>@endif
        @endif

        <div class="flex gap-3">
            @if ($message->isCancellable())
                <form method="POST" action="{{ route('notifications.cancel', $message->id) }}" onsubmit="return confirm('Cancel this message?')">@csrf
                    <button class="rounded-lg border border-rose-300 px-4 py-2 text-sm font-medium text-rose-600 dark:border-rose-800">Cancel message</button>
                </form>
            @endif
            <a href="{{ route('notifications.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm dark:border-slate-700">Back</a>
        </div>
    </div>
</x-layouts.app>

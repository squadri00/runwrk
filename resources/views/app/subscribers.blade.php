<x-layouts.app title="Subscribers">
    <div class="grid max-w-4xl grid-cols-2 gap-4 sm:grid-cols-4">
        @foreach ([['Total', $total], ['New this week', $week], ['Android', $byPlatform['android'] ?? 0], ['iPhone', $byPlatform['ios'] ?? 0]] as [$label, $value])
            <div class="rounded-xl bg-white p-4 ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
                <div class="text-2xl font-semibold">{{ number_format($value) }}</div>
                <div class="mt-1 text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ $label }}</div>
            </div>
        @endforeach
    </div>

    <p class="mt-4 max-w-2xl text-sm text-slate-500 dark:text-slate-400">Customers join when they tap "Turn on notifications" in your app. We keep no names or phone numbers, only what is needed to deliver your messages. Customers who remove the app drop off automatically.</p>

    <div class="mt-4 max-w-2xl overflow-hidden rounded-xl bg-white ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
        <div class="border-b border-slate-200 px-4 py-3 text-sm font-semibold dark:border-slate-800">Latest sign-ups</div>
        <div class="divide-y divide-slate-200 text-sm dark:divide-slate-800">
            @forelse ($recent as $s)
                <div class="flex items-center justify-between px-4 py-2.5">
                    <span class="capitalize">{{ $s->platform === 'ios' ? 'iPhone' : $s->platform }} <span class="text-xs text-slate-400">· via {{ $s->source === 'hosted' ? 'your app page' : 'your website' }}</span></span>
                    <span class="text-xs text-slate-500 dark:text-slate-400">{{ $s->created_at->diffForHumans() }}</span>
                </div>
            @empty
                <div class="px-4 py-8 text-center text-slate-500">No subscribers yet.</div>
            @endforelse
        </div>
    </div>
</x-layouts.app>

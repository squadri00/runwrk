<x-layouts.app title="Dashboard">
    @php
        $steps = [
            ['Add your logo', 'Your logo becomes the icon on your customers\' home screens.', (bool) $business->logo_path, route('branding.edit'), 'Open branding'],
            ['Connect your website', 'We link your website so it can offer your app to visitors.', $business->domains_count > 0, null, 'We set this up with you'],
            ['Invite your team', 'Staff can send messages to your customers.', $business->users_count > 1, route('team.index'), 'Open team'],
            ['Get your first customers', 'Share your app page so customers can turn on notifications.', $subscribers > 0, route('subscribers.index'), 'Open subscribers'],
        ];
        $isOwner = auth('web')->user()->role === 'owner';
    @endphp

    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        @foreach ([['Subscribers', $subscribers], ['Messages sent', $sent], ['Opens (30 days)', $clicks]] as [$label, $value])
            <div class="rounded-xl bg-white p-4 ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
                <div class="text-2xl font-semibold">{{ number_format($value) }}</div>
                <div class="mt-1 text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ $label }}</div>
            </div>
        @endforeach
        <a href="{{ route('notifications.create') }}" class="flex items-center justify-center rounded-xl bg-slate-900 p-4 text-sm font-semibold text-white hover:bg-slate-800 dark:bg-white dark:text-slate-900">Send a message</a>
    </div>

    <div class="mt-4 rounded-xl bg-white p-5 ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
        <h2 class="text-sm font-semibold">Your app page</h2>
        <p class="mt-1 break-all font-mono text-sm text-slate-600 dark:text-slate-300">{{ url($business->slug) }}</p>
        <p class="mt-1 text-xs text-slate-400">Share this link or put it on a poster. Customers open it on their phone, add it to their home screen and turn on notifications.</p>
    </div>

    <div class="mt-4 rounded-xl bg-white ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
        <div class="border-b border-slate-200 px-5 py-3 text-sm font-semibold dark:border-slate-800">Get set up</div>
        <div class="divide-y divide-slate-200 dark:divide-slate-800">
            @foreach ($steps as [$title, $text, $done, $href, $cta])
                <div class="flex items-center gap-4 px-5 py-3.5">
                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-xs font-bold {{ $done ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-500 dark:bg-slate-800' }}">{{ $done ? '✓' : '' }}</span>
                    <div class="min-w-0 flex-1">
                        <div class="text-sm font-medium">{{ $title }}</div>
                        <div class="text-xs text-slate-500 dark:text-slate-400">{{ $text }}</div>
                    </div>
                    @unless ($done)
                        @if ($href && ($isOwner || ! in_array($href, [route('branding.edit'), route('team.index')])))
                            <a href="{{ $href }}" class="shrink-0 text-sm font-medium underline">{{ $cta }}</a>
                        @elseif (! $href)
                            <span class="shrink-0 text-xs text-slate-400">{{ $cta }}</span>
                        @endif
                    @endunless
                </div>
            @endforeach
        </div>
    </div>

    @if ($recent->isNotEmpty())
        <div class="mt-4 rounded-xl bg-white ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
            <div class="border-b border-slate-200 px-5 py-3 text-sm font-semibold dark:border-slate-800">Recent messages</div>
            <div class="divide-y divide-slate-200 text-sm dark:divide-slate-800">
                @foreach ($recent as $m)
                    <a href="{{ route('notifications.show', $m->id) }}" class="flex items-center justify-between px-5 py-2.5 hover:bg-slate-50 dark:hover:bg-slate-800/50">
                        <span class="truncate">{{ $m->title }}</span><x-message-status :status="$m->status" />
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</x-layouts.app>

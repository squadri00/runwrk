<x-layouts.app title="Dashboard">
    @php
        $steps = [
            ['Add your logo', 'Your logo becomes the icon on your customers\' home screens.', (bool) $business->logo_path, route('branding.edit'), 'Open branding'],
            ['Connect your website', 'We link your website so it can offer your app to visitors.', $business->domains_count > 0, null, 'We set this up with you'],
            ['Invite your team', 'Staff can send messages to your customers.', $business->users_count > 1, route('team.index'), 'Open team'],
        ];
    @endphp

    <div class="rounded-xl bg-white p-5 ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
        <h2 class="text-sm font-semibold">Welcome, {{ auth('web')->user()->name }}</h2>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Your app address: <span class="font-mono">{{ url($business->slug) }}</span> (goes live in the next update)</p>
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
                        @if ($href && auth('web')->user()->role === 'owner')
                            <a href="{{ $href }}" class="shrink-0 text-sm font-medium underline">{{ $cta }}</a>
                        @elseif (! $href)
                            <span class="shrink-0 text-xs text-slate-400">{{ $cta }}</span>
                        @endif
                    @endunless
                </div>
            @endforeach
        </div>
    </div>

    <p class="mt-4 text-sm text-slate-500 dark:text-slate-400">Sending messages to your customers' phones arrives in the next update.</p>
</x-layouts.app>

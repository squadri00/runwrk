<x-layouts.superadmin :title="$business->name">
    @if (session('invite_url'))
        <div class="mb-4 rounded-lg bg-amber-50 px-4 py-3 text-sm text-amber-900 ring-1 ring-amber-200 dark:bg-amber-500/15 dark:text-amber-200 dark:ring-amber-500/30">
            A set-password email has been sent. If mail isn't set up yet, send this link to the owner yourself (works for 3 days):
            <code class="mt-1 block break-all font-mono text-xs">{{ session('invite_url') }}</code>
        </div>
    @endif

    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-500 dark:text-slate-400">runwrk.com/{{ $business->slug }} · <span class="capitalize">{{ $business->status }}</span> · {{ $business->plan?->name ?? 'No plan' }}</p>
        <div class="flex gap-2">
            <form method="POST" action="{{ route('admin.businesses.impersonate', $business) }}">@csrf
                <button class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm font-medium dark:border-slate-700">Log in as owner</button>
            </form>
            <a href="{{ route('admin.businesses.edit', $business) }}" class="rounded-lg bg-violet-700 px-3 py-1.5 text-sm font-semibold text-white hover:bg-violet-800">Edit</a>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <section class="rounded-xl bg-white p-5 ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
            <h2 class="mb-3 text-sm font-semibold">Public key</h2>
            <code class="block break-all rounded-lg bg-slate-100 px-3 py-2 text-sm dark:bg-slate-800">{{ $business->public_key }}</code>
            <form method="POST" action="{{ route('admin.businesses.key', $business) }}" class="mt-3" onsubmit="return confirm('The current key stops working immediately. Continue?')">@csrf
                <button class="text-sm text-rose-600 hover:underline">Generate a new key</button>
            </form>
        </section>
        <section class="rounded-xl bg-white p-5 ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
            <h2 class="mb-3 text-sm font-semibold">Allowed domains</h2>
            @forelse ($business->domains as $d)<div class="text-sm">{{ $d->domain }}</div>@empty<p class="text-sm text-slate-400">None. Hosted app only.</p>@endforelse
        </section>
        <section class="rounded-xl bg-white p-5 ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
            <h2 class="mb-3 text-sm font-semibold">Users</h2>
            @foreach ($business->users as $u)
                <div class="flex items-center justify-between gap-2 py-1 text-sm">
                    <span>{{ $u->name }} <span class="text-slate-400">· {{ $u->email }} · {{ $u->role }}</span></span>
                    <form method="POST" action="{{ route('admin.businesses.invite', [$business, $u]) }}">@csrf
                        <button class="text-xs text-slate-500 underline hover:text-slate-800 dark:hover:text-slate-200">Send password link</button>
                    </form>
                </div>
            @endforeach
        </section>
        <section class="rounded-xl bg-white p-5 text-sm ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
            <h2 class="mb-3 font-semibold">Branding</h2>
            <div class="flex items-center gap-2"><span class="inline-block h-4 w-4 rounded border" style="background: {{ $business->theme_color }}"></span> {{ $business->theme_color }} / {{ $business->background_color }}</div>
            <div class="mt-1 text-slate-500 dark:text-slate-400">{{ $business->phone }} {{ $business->address }}</div>
            <div class="text-slate-500 dark:text-slate-400">{{ $business->website_url }}</div>
        </section>
    </div>

    <h2 class="mb-3 mt-8 text-sm font-semibold">Activity</h2>
    @include('admin._logs')
</x-layouts.superadmin>

<x-layouts.superadmin title="Security (2FA)">
    <div class="max-w-xl space-y-4">
        @if (session('recovery_codes'))
            <div class="rounded-xl bg-amber-50 p-5 ring-1 ring-amber-200 dark:bg-amber-500/15 dark:ring-amber-500/30">
                <h2 class="text-sm font-semibold text-amber-900 dark:text-amber-200">Save your recovery codes</h2>
                <p class="mt-1 text-xs text-amber-800 dark:text-amber-300">Each works once if you lose your phone. They are not shown again.</p>
                <div class="mt-3 grid grid-cols-2 gap-1 font-mono text-sm">
                    @foreach (session('recovery_codes') as $code)<div>{{ $code }}</div>@endforeach
                </div>
            </div>
        @endif

        @if ($enabled)
            <section class="space-y-4 rounded-xl bg-white p-6 ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
                <p class="text-sm">Two-factor authentication is <strong class="text-emerald-600">on</strong>.</p>
                <form method="POST" action="{{ route('admin.security.codes') }}" class="flex items-end gap-2">
                    @csrf
                    <div class="flex-1"><x-field name="password" label="Password" type="password" /></div>
                    <button class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium dark:border-slate-700">New recovery codes</button>
                </form>
                <form method="POST" action="{{ route('admin.security.disable') }}" class="flex items-end gap-2" onsubmit="return confirm('Turn off two-factor authentication?')">
                    @csrf
                    <div class="flex-1"><x-field name="password" label="Password" type="password" /></div>
                    <button class="rounded-lg border border-rose-300 px-3 py-2 text-sm font-medium text-rose-600 dark:border-rose-800">Turn off 2FA</button>
                </form>
            </section>
        @else
            <section class="space-y-4 rounded-xl bg-white p-6 ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
                <p class="text-sm">Scan this code with an authenticator app (Google Authenticator, Authy, 1Password), then enter the 6-digit code it shows.</p>
                <div class="inline-block rounded-lg bg-white p-2">{!! $qr !!}</div>
                <p class="text-xs text-slate-400">Can't scan? Enter this key manually: <code class="font-mono">{{ $secret }}</code></p>
                <form method="POST" action="{{ route('admin.security.enable') }}" class="flex items-end gap-2">
                    @csrf
                    <div class="flex-1"><x-field name="code" label="6-digit code" inputmode="numeric" maxlength="6" autocomplete="one-time-code" /></div>
                    <button class="rounded-lg bg-violet-700 px-4 py-2 text-sm font-semibold text-white hover:bg-violet-800">Turn on</button>
                </form>
            </section>
        @endif
    </div>
</x-layouts.superadmin>

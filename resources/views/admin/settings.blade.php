<x-layouts.superadmin title="Settings">
    @php($m = $s['mail'])
    <form method="POST" action="{{ route('admin.settings.update') }}" class="max-w-2xl space-y-6">
        @csrf @method('PUT')

        <section class="space-y-4 rounded-xl bg-white p-6 ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
            <h2 class="text-sm font-semibold">General</h2>
            <x-field name="app_name" label="Platform name" :value="$s['app_name'] ?? null" hint="Shown in emails and page titles. Leave empty for the default." />
            <x-field name="support_email" label="Support email" type="email" :value="$s['support_email'] ?? null" />
            <x-field name="intro_video_url" label="Homepage intro video (YouTube link)" :value="$s['intro_video_url'] ?? null" hint="Shown under the homepage hero. Leave empty to show a coming-soon box." />
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="signups_enabled" value="1" @checked(old('signups_enabled', $s['signups_enabled'] ?? true)) class="rounded border-slate-300"> Allow new businesses to sign up</label>
        </section>

        <section class="space-y-4 rounded-xl bg-white p-6 ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
            <div>
                <h2 class="text-sm font-semibold">Email (SMTP)</h2>
                <p class="text-xs text-slate-400">Overrides the values in .env. Leave the host empty to keep using .env. Hostinger: smtp.hostinger.com, port 465, SSL.</p>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div class="col-span-2"><x-field name="mail_host" label="Host" :value="$m['host'] ?? null" /></div>
                <x-field name="mail_port" label="Port" type="number" :value="$m['port'] ?? null" />
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Encryption</label>
                <select name="mail_encryption" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                    @foreach (['tls' => 'TLS (587)', 'ssl' => 'SSL (465)', 'none' => 'None'] as $v => $l)<option value="{{ $v }}" @selected(old('mail_encryption', $m['encryption'] ?? 'tls') === $v)>{{ $l }}</option>@endforeach
                </select>
            </div>
            <x-field name="mail_username" label="Username" :value="$m['username'] ?? null" />
            <x-field name="mail_password" label="Password" type="password" :hint="$s['mail_password_set'] ? 'A password is saved. Leave blank to keep it.' : null" autocomplete="new-password" />
            <div class="grid grid-cols-2 gap-4">
                <x-field name="mail_from_address" label="From address" type="email" :value="$m['from_address'] ?? null" />
                <x-field name="mail_from_name" label="From name" :value="$m['from_name'] ?? null" />
            </div>
        </section>

        <button class="rounded-lg bg-violet-700 px-5 py-2 text-sm font-semibold text-white hover:bg-violet-800">Save settings</button>
    </form>

    <form method="POST" action="{{ route('admin.settings.test-mail') }}" class="mt-4 max-w-2xl">
        @csrf
        <button class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium dark:border-slate-700">Send a test email to me</button>
        <span class="ml-2 text-xs text-slate-400">Save first, then test.</span>
    </form>
</x-layouts.superadmin>

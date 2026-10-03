<x-layouts.app title="Team">
    <div class="max-w-2xl space-y-6">
        <div class="overflow-hidden rounded-xl bg-white ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
            <div class="divide-y divide-slate-200 dark:divide-slate-800">
                @foreach ($users as $u)
                    <div class="flex items-center justify-between gap-3 px-5 py-3 text-sm">
                        <div>
                            <div class="font-medium">{{ $u->name }} <span class="ml-1 rounded-full bg-slate-100 px-2 py-0.5 text-xs capitalize dark:bg-slate-800">{{ $u->role }}</span></div>
                            <div class="text-xs text-slate-500 dark:text-slate-400">{{ $u->email }} · {{ $u->last_login_at ? 'last sign-in '.$u->last_login_at->diffForHumans() : 'has not signed in yet' }}</div>
                        </div>
                        @if ($u->id !== auth('web')->id())
                            <div class="flex shrink-0 gap-3 text-xs">
                                @unless ($u->last_login_at)
                                    <form method="POST" action="{{ route('team.invite', $u->id) }}">@csrf<button class="underline">Resend invite</button></form>
                                @endunless
                                <form method="POST" action="{{ route('team.destroy', $u->id) }}" onsubmit="return confirm('Remove {{ $u->name }}?')">@csrf @method('DELETE')<button class="text-rose-600 underline">Remove</button></form>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <form method="POST" action="{{ route('team.store') }}" class="space-y-4 rounded-xl bg-white p-6 ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
            @csrf
            <h2 class="text-sm font-semibold">Invite someone</h2>
            <x-field name="name" label="Name" />
            <x-field name="email" label="Email" type="email" />
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Role</label>
                <select name="role" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                    <option value="staff">Staff: sends messages and sees customers</option>
                    <option value="owner">Owner: also changes branding and manages the team</option>
                </select>
            </div>
            <p class="text-xs text-slate-400">They get an email to choose their own password.</p>
            <button class="rounded-lg bg-slate-900 px-5 py-2 text-sm font-semibold text-white hover:bg-slate-800 dark:bg-white dark:text-slate-900">Send invite</button>
        </form>
    </div>
</x-layouts.app>

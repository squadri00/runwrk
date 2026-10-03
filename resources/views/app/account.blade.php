<x-layouts.app title="My account">
    <form method="POST" action="{{ route('account.update') }}" class="max-w-xl space-y-4 rounded-xl bg-white p-6 ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
        @csrf @method('PUT')
        <x-field name="name" label="Name" :value="$user->name" />
        <div>
            <div class="text-sm font-medium text-slate-700 dark:text-slate-300">Email</div>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $user->email }}</p>
        </div>
        <hr class="border-slate-200 dark:border-slate-800">
        <h2 class="text-sm font-semibold">Change password</h2>
        <x-field name="current_password" label="Current password" type="password" autocomplete="current-password" />
        <x-field name="password" label="New password" type="password" autocomplete="new-password" />
        <x-field name="password_confirmation" label="Confirm new password" type="password" autocomplete="new-password" />
        <button class="rounded-lg bg-slate-900 px-5 py-2 text-sm font-semibold text-white hover:bg-slate-800 dark:bg-white dark:text-slate-900">Save</button>
    </form>
</x-layouts.app>

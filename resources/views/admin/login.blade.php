<x-layouts.guest heading="Superadmin" subheading="Platform control panel">
    <form method="POST" action="{{ route('admin.login') }}" class="space-y-4">
        @csrf
        <x-flash />
        <x-field name="email" label="Email" type="email" required autofocus />
        <x-field name="password" label="Password" type="password" required />
        <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
            <input type="checkbox" name="remember" class="rounded border-slate-300 dark:border-slate-700"> Remember me
        </label>
        <button class="w-full rounded-lg bg-slate-900 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-200">Sign in</button>
    </form>
</x-layouts.guest>

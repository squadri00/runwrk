<x-layouts.guest heading="Two-factor code" subheading="Enter the 6-digit code from your authenticator app, or a recovery code">
    <form method="POST" action="{{ route('admin.2fa') }}" class="space-y-4">
        @csrf
        <x-flash />
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Code</label>
            <input name="code" required autofocus autocomplete="one-time-code"
                   class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-center text-lg tracking-widest text-slate-900 focus:border-slate-900 focus:ring-slate-900 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
        </div>
        <button class="w-full rounded-lg bg-slate-900 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-200">Verify</button>
    </form>
    <x-slot:footer><a href="{{ route('admin.login') }}" class="font-medium text-slate-900 underline dark:text-slate-100">Back to sign in</a></x-slot:footer>
</x-layouts.guest>

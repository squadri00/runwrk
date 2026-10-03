<x-layouts.guest heading="Sign in" subheading="Business dashboard">
    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        <x-flash />
        <x-field name="email" label="Email" type="email" required autofocus />
        <div>
            <div class="flex items-center justify-between">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Password</label>
                <a href="{{ route('password.request') }}" class="text-xs font-medium text-slate-500 underline hover:text-slate-700 dark:hover:text-slate-300">Forgot password?</a>
            </div>
            <input name="password" type="password" required class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-900 focus:ring-slate-900 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
        </div>
        <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
            <input type="checkbox" name="remember" class="rounded border-slate-300 dark:border-slate-700"> Remember me
        </label>
        <button class="w-full rounded-lg bg-slate-900 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-200">Sign in</button>
    </form>

    <x-slot:footer>
        New here? <a href="{{ route('pricing') }}" class="font-medium text-slate-900 underline dark:text-slate-100">Create an account</a>
    </x-slot:footer>
</x-layouts.guest>

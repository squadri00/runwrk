<x-layouts.guest heading="Set your password" subheading="Choose a password to sign in with">
    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <x-flash />
        <x-field name="email" label="Email" type="email" :value="$email" required />
        <x-field name="password" label="New password" type="password" required autofocus />
        <x-field name="password_confirmation" label="Confirm password" type="password" required />
        <button class="w-full rounded-lg bg-slate-900 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-200">Save password</button>
    </form>
</x-layouts.guest>

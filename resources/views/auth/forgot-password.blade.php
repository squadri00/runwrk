<x-layouts.guest heading="Forgot password" subheading="We'll email you a link to set a new one">
    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf
        <x-flash />
        <x-field name="email" label="Email" type="email" required autofocus />
        <button class="w-full rounded-lg bg-slate-900 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-200">Send link</button>
    </form>
    <x-slot:footer><a href="{{ route('login') }}" class="font-medium text-slate-900 underline dark:text-slate-100">Back to sign in</a></x-slot:footer>
</x-layouts.guest>

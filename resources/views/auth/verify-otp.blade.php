<x-layouts.guest heading="Check your email" subheading="We sent a 6-digit code to {{ $pending->email }}">
    <x-flash />

    <form method="POST" action="{{ route('register.verify.submit', $pending->token) }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Verification code</label>
            <input name="code" inputmode="numeric" pattern="[0-9]*" maxlength="6" required autofocus autocomplete="one-time-code"
                   class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-center text-lg tracking-[0.5em] text-slate-900 focus:border-slate-900 focus:ring-slate-900 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
        </div>
        <button class="w-full rounded-lg bg-slate-900 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-200">Verify &amp; continue</button>
    </form>

    <form method="POST" action="{{ route('register.verify.resend', $pending->token) }}" class="mt-3 text-center">
        @csrf
        <button class="text-sm font-medium text-slate-500 underline hover:text-slate-700 dark:text-slate-400">Resend code</button>
    </form>

    <x-slot:footer>
        Wrong email? <a href="{{ route('register') }}" class="font-medium text-slate-900 underline dark:text-slate-100">Start over</a>
    </x-slot:footer>
</x-layouts.guest>

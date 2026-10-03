<x-layouts.guest heading="Create your account" :subheading="$plan->name.' plan · '.$plan->priceLabel().($plan->isFree() ? '' : '/'.$plan->interval)">
    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="plan" value="{{ $plan->code }}">
        <x-flash />

        <div class="rounded-lg bg-slate-50 px-3 py-2 text-sm text-slate-600 ring-1 ring-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:ring-slate-700">
            @if ($plan->isFree())
                <strong>{{ $plan->name }}</strong>. You'll confirm your email with a short code.
            @else
                <strong>{{ $plan->name }}</strong>, {{ $plan->priceLabel() }}/{{ $plan->interval }}. Online checkout isn't enabled yet. You'll confirm your email with a short code and we'll follow up about billing.
            @endif
        </div>

        <x-field name="business_name" label="Business name" required autofocus />
        <x-field name="name" label="Your name" required />
        <x-field name="email" label="Email" type="email" required />
        <x-field name="password" label="Password" type="password" required />
        <x-field name="password_confirmation" label="Confirm password" type="password" required />

        <button class="w-full rounded-lg bg-slate-900 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-200">Create account</button>
    </form>

    <x-slot:footer>
        Already have an account? <a href="{{ route('login') }}" class="font-medium text-slate-900 underline dark:text-slate-100">Sign in</a>
    </x-slot:footer>
</x-layouts.guest>

<x-layouts.site title="Pricing">
    <main class="mx-auto max-w-6xl px-4 py-16">
        <div class="mb-12 text-center">
            <h1 class="text-3xl font-bold tracking-tight">Simple pricing. No surprises.</h1>
            <p class="mt-2 text-slate-500 dark:text-slate-400">Your own app and a message to your customers' phones, anytime.</p>
        </div>

        @if ($plans->isEmpty())
            <p class="text-center text-slate-500">Pricing isn't available right now. Please check back soon.</p>
        @else
            @php($cols = [1 => 'lg:grid-cols-1', 2 => 'lg:grid-cols-2', 3 => 'lg:grid-cols-3', 4 => 'lg:grid-cols-4'][min($plans->count(), 4)])
            <div class="grid gap-6 sm:grid-cols-2 {{ $cols }}">
                @foreach ($plans as $plan)
                    <div class="flex flex-col rounded-2xl bg-white p-6 ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
                        <h2 class="text-lg font-semibold">{{ $plan->name }}</h2>
                        <p class="mt-3 text-4xl font-bold">{{ $plan->priceLabel() }}@unless ($plan->isFree())<span class="text-base font-normal text-slate-500">/{{ $plan->interval }}</span>@endunless</p>
                        @if ($plan->description)<p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ $plan->description }}</p>@endif
                        <ul class="mt-5 flex-1 space-y-2 text-sm">
                            @foreach ($plan->features ?? [] as $feature)
                                <li class="flex gap-2"><span class="text-emerald-600">✓</span> {{ $feature }}</li>
                            @endforeach
                        </ul>
                        @if ($signups)
                            <a href="{{ route('register', ['plan' => $plan->code]) }}" class="mt-6 block rounded-lg bg-slate-900 px-4 py-2.5 text-center text-sm font-semibold text-white hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-200">
                                {{ $plan->isFree() ? 'Start free' : 'Get started' }}
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </main>
</x-layouts.site>

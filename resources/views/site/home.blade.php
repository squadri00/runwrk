<x-layouts.site>
    <main class="mx-auto max-w-3xl px-4 py-24 text-center">
        <h1 class="text-4xl font-bold tracking-tight">Your own app. A message to every customer's phone, anytime.</h1>
        <p class="mt-4 text-lg text-slate-500 dark:text-slate-400">Put your business on your customers' home screens and tell them about your latest offer in one tap.</p>
        <div class="mt-8 flex justify-center gap-3">
            <a href="{{ route('pricing') }}" class="rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-800 dark:bg-white dark:text-slate-900">See pricing</a>
            <a href="{{ route('login') }}" class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-semibold dark:border-slate-700">Sign in</a>
        </div>
    </main>
</x-layouts.site>

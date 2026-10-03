@props(['heading' => null, 'subheading' => null, 'wide' => false])

<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $heading ? $heading.' · '.config('app.name') : config('app.name') }}</title>
    @include('partials.theme-head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-100 text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
    <div class="absolute left-4 top-4">
        <a href="{{ url('/') }}" class="text-sm font-medium text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200">&larr; Back to website</a>
    </div>
    <div class="absolute right-4 top-4"><x-theme-toggle /></div>
    <div class="flex min-h-full flex-col items-center justify-center px-4 py-12">
        <div class="w-full {{ $wide ? 'max-w-md' : 'max-w-sm' }}">
            <div class="mb-8 text-center">
                <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-slate-900 text-lg font-bold text-white dark:bg-white dark:text-slate-900">{{ mb_substr(config('app.name'), 0, 1) }}</span>
                <h1 class="mt-3 text-xl font-semibold">{{ $heading ?? config('app.name') }}</h1>
                @if($subheading)<p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $subheading }}</p>@endif
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
                {{ $slot }}
            </div>

            @isset($footer)
                <div class="mt-6 text-center text-sm text-slate-500 dark:text-slate-400">{{ $footer }}</div>
            @endisset
        </div>
    </div>
</body>
</html>

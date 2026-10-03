@props(['title' => null])

<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title.' · ' : '' }}{{ config('app.name') }}</title>
    @include('partials.theme-head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-slate-100 text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
<header class="border-b border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900">
    <div class="mx-auto flex h-14 max-w-6xl items-center justify-between px-4">
        <a href="{{ url('/') }}" class="flex items-center gap-2 font-semibold">
            <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-slate-900 text-sm font-bold text-white dark:bg-white dark:text-slate-900">{{ mb_substr(config('app.name'), 0, 1) }}</span>
            {{ config('app.name') }}
        </a>
        <nav class="flex items-center gap-4 text-sm font-medium">
            <a href="{{ route('pricing') }}" class="text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white">Pricing</a>
            @auth('web')
                <a href="{{ route('dashboard') }}" class="rounded-lg bg-slate-900 px-3 py-1.5 text-white dark:bg-white dark:text-slate-900">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white">Sign in</a>
            @endauth
            <x-theme-toggle />
        </nav>
    </div>
</header>
{{ $slot }}
</body>
</html>

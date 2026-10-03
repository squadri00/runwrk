<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Runwrk')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 antialiased">
@if (app(\App\Domain\Tenancy\Impersonation::class)->isActive())
    <form method="POST" action="{{ route('impersonation.stop') }}" class="flex items-center justify-center gap-3 bg-amber-400 px-4 py-2 text-sm font-medium text-amber-950">
        @csrf
        <span>You are viewing this account as support.</span>
        <button class="rounded bg-amber-950 px-3 py-1 text-white">Return to admin</button>
    </form>
@endif
@yield('body')
</body>
</html>

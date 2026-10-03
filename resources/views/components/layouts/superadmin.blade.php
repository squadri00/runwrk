@props(['title' => 'Dashboard'])

@php($admin = auth('superadmin')->user())

<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} · Superadmin</title>
    @include('partials.theme-head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-100 text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
<div class="min-h-full">
    <div class="mx-auto flex max-w-7xl">
        <div id="mobile-nav-backdrop" class="fixed inset-0 z-30 hidden bg-black/50 md:hidden" onclick="toggleMobileNav()"></div>

        <aside id="mobile-nav-sidebar" class="fixed inset-y-0 left-0 z-40 w-64 -translate-x-full overflow-y-auto border-r border-slate-200 bg-white transition-transform duration-200 md:static md:z-auto md:w-60 md:shrink-0 md:translate-x-0 dark:border-slate-800 dark:bg-slate-900">
            <div class="flex h-14 items-center gap-2 border-b border-slate-200 px-5 dark:border-slate-800">
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-violet-700 text-sm font-bold text-white">{{ mb_substr(config('app.name'), 0, 1) }}</span>
                <span class="font-semibold">{{ config('app.name') }}</span>
                <button onclick="toggleMobileNav()" class="ml-auto rounded-lg p-1 text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 md:hidden" aria-label="Close menu">✕</button>
            </div>
            <nav class="space-y-1 p-3 text-sm">
                @php($saLink = fn ($route) => request()->routeIs($route)
                    ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900'
                    : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800')
                <a href="{{ route('admin.home') }}" class="block rounded-lg px-3 py-2 font-medium {{ $saLink('admin.home') }}">Dashboard</a>
                <a href="{{ route('admin.businesses.index') }}" class="block rounded-lg px-3 py-2 font-medium {{ $saLink('admin.businesses.*') }}">Businesses</a>
                <a href="{{ route('admin.plans.index') }}" class="block rounded-lg px-3 py-2 font-medium {{ $saLink('admin.plans.*') }}">Plans &amp; pricing</a>
                <a href="{{ route('admin.audit') }}" class="block rounded-lg px-3 py-2 font-medium {{ $saLink('admin.audit') }}">Audit log</a>
                <a href="{{ route('admin.settings.edit') }}" class="block rounded-lg px-3 py-2 font-medium {{ $saLink('admin.settings.*') }}">Settings</a>
                <a href="{{ route('admin.security') }}" class="block rounded-lg px-3 py-2 font-medium {{ $saLink('admin.security*') }}">Security (2FA)</a>
            </nav>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="flex h-14 items-center justify-between border-b border-slate-200 bg-white px-4 md:px-6 dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center gap-2 md:gap-3">
                    <button onclick="toggleMobileNav()" class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 md:hidden dark:text-slate-300 dark:hover:bg-slate-800" aria-label="Open menu">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    </button>
                    <h1 class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $title }}</h1>
                    <span class="inline-flex items-center gap-1 rounded-full bg-violet-700 px-3 py-1 text-xs font-bold uppercase tracking-wide text-white dark:bg-violet-600">Superadmin</span>
                </div>
                <div class="flex items-center gap-3 text-sm">
                    <x-theme-toggle />
                    <span class="hidden text-slate-500 sm:inline dark:text-slate-400">{{ $admin?->name }}</span>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button class="rounded-lg border border-slate-200 px-3 py-1.5 font-medium text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">Sign out</button>
                    </form>
                </div>
            </header>

            <main class="flex-1 p-4 md:p-6">
                <x-flash />
                {{ $slot }}
            </main>
        </div>
    </div>
</div>
<script>
    function toggleMobileNav() {
        document.getElementById('mobile-nav-sidebar').classList.toggle('-translate-x-full');
        document.getElementById('mobile-nav-backdrop').classList.toggle('hidden');
    }
</script>
</body>
</html>

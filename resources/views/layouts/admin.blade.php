@extends('layouts.app')

@section('body')
<header class="border-b border-gray-200 bg-white">
    <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3">
        <nav class="flex items-center gap-6 text-sm font-medium">
            <a href="{{ route('admin.home') }}" class="text-lg font-bold">Runwrk <span class="text-xs font-normal text-gray-400">admin</span></a>
            <a href="{{ route('admin.businesses.index') }}" class="hover:text-indigo-600">Businesses</a>
            <a href="{{ route('admin.audit') }}" class="hover:text-indigo-600">Audit log</a>
        </nav>
        <form method="POST" action="{{ route('admin.logout') }}">@csrf
            <button class="text-sm text-gray-500 hover:text-gray-900">Sign out</button>
        </form>
    </div>
</header>
<main class="mx-auto max-w-6xl px-4 py-8">
    @if (session('status'))
        <div class="mb-6 rounded border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('status') }}</div>
    @endif
    @yield('content')
</main>
@endsection

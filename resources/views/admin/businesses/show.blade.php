@extends('layouts.admin')

@section('title', $business->name.' · Runwrk')

@section('content')
@if (session('new_login'))
    <div class="mb-6 rounded border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900">
        Owner login created. Copy it now, the password is not shown again.<br>
        <span class="font-mono">{{ session('new_login.email') }} / {{ session('new_login.password') }}</span>
    </div>
@endif
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-2xl font-bold">{{ $business->name }}</h1>
        <p class="text-sm text-gray-500">runwrk.com/{{ $business->slug }} · <span class="capitalize">{{ $business->status }}</span> · {{ $business->plan?->name ?? 'No plan' }}</p>
    </div>
    <div class="flex gap-2">
        <form method="POST" action="{{ route('admin.businesses.impersonate', $business) }}">@csrf
            <button class="rounded border border-gray-300 bg-white px-4 py-2 text-sm">Log in as owner</button>
        </form>
        <a href="{{ route('admin.businesses.edit', $business) }}" class="rounded bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Edit</a>
    </div>
</div>

<div class="grid gap-6 md:grid-cols-2">
    <section class="rounded-lg border border-gray-200 bg-white p-5">
        <h2 class="mb-3 font-semibold">Public key</h2>
        <code class="block break-all rounded bg-gray-100 px-3 py-2 text-sm">{{ $business->public_key }}</code>
        <form method="POST" action="{{ route('admin.businesses.key', $business) }}" class="mt-3" onsubmit="return confirm('The current key stops working immediately. Continue?')">@csrf
            <button class="text-sm text-red-600 hover:underline">Generate a new key</button>
        </form>
    </section>
    <section class="rounded-lg border border-gray-200 bg-white p-5">
        <h2 class="mb-3 font-semibold">Allowed domains</h2>
        @forelse ($business->domains as $d)<div class="text-sm">{{ $d->domain }}</div>@empty<p class="text-sm text-gray-400">None. Hosted app only.</p>@endforelse
    </section>
    <section class="rounded-lg border border-gray-200 bg-white p-5">
        <h2 class="mb-3 font-semibold">Users</h2>
        @foreach ($business->users as $u)<div class="text-sm">{{ $u->name }} <span class="text-gray-400">· {{ $u->email }} · {{ $u->role }}</span></div>@endforeach
    </section>
    <section class="rounded-lg border border-gray-200 bg-white p-5 text-sm">
        <h2 class="mb-3 font-semibold">Branding</h2>
        <div class="flex items-center gap-2"><span class="inline-block h-4 w-4 rounded border" style="background: {{ $business->theme_color }}"></span> {{ $business->theme_color }}</div>
        <div class="text-gray-500">{{ $business->phone }} {{ $business->address }}</div>
        <div class="text-gray-500">{{ $business->website_url }}</div>
    </section>
</div>

<h2 class="mb-3 mt-10 font-semibold">Activity</h2>
@include('admin._logs')
@endsection

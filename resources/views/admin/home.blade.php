@extends('layouts.admin')

@section('title', 'Admin · Runwrk')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <h1 class="text-2xl font-bold">Overview</h1>
    <a href="{{ route('admin.businesses.create') }}" class="rounded bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">New business</a>
</div>
<div class="mb-10 grid grid-cols-2 gap-4 sm:grid-cols-4">
    @foreach (\App\Models\Business::STATUSES as $s)
        <a href="{{ route('admin.businesses.index', ['status' => $s]) }}" class="rounded-lg border border-gray-200 bg-white p-4">
            <div class="text-3xl font-bold">{{ $counts[$s] ?? 0 }}</div>
            <div class="text-sm capitalize text-gray-500">{{ $s }}</div>
        </a>
    @endforeach
</div>
<h2 class="mb-3 font-semibold">Recent activity</h2>
@include('admin._logs', ['logs' => $recent])
@endsection

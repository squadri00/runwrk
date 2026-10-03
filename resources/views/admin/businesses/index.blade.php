@extends('layouts.admin')

@section('title', 'Businesses · Runwrk')

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <h1 class="text-2xl font-bold">Businesses</h1>
    <a href="{{ route('admin.businesses.create') }}" class="rounded bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">New business</a>
</div>
<form class="mb-4 flex gap-2">
    <input name="q" value="{{ request('q') }}" placeholder="Search name or slug" class="w-64 rounded border border-gray-300 px-3 py-2 text-sm">
    <select name="status" class="rounded border border-gray-300 px-3 py-2 text-sm">
        <option value="">All statuses</option>
        @foreach (\App\Models\Business::STATUSES as $s)<option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>@endforeach
    </select>
    <button class="rounded border border-gray-300 bg-white px-4 py-2 text-sm">Filter</button>
</form>
<div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
    <table class="w-full text-left text-sm">
        <thead class="border-b border-gray-200 bg-gray-50 text-xs uppercase text-gray-500">
            <tr><th class="px-4 py-2">Business</th><th class="px-4 py-2">Slug</th><th class="px-4 py-2">Plan</th><th class="px-4 py-2">Users</th><th class="px-4 py-2">Status</th></tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($businesses as $b)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 font-medium"><a href="{{ route('admin.businesses.show', $b) }}" class="text-indigo-600">{{ $b->name }}</a></td>
                    <td class="px-4 py-2 text-gray-500">{{ $b->slug }}</td>
                    <td class="px-4 py-2">{{ $b->plan?->name ?? '—' }}</td>
                    <td class="px-4 py-2">{{ $b->users_count }}</td>
                    <td class="px-4 py-2"><span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs capitalize">{{ $b->status }}</span></td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">No businesses.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $businesses->links() }}</div>
@endsection

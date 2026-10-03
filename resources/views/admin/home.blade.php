<x-layouts.superadmin title="Dashboard">
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        @foreach (\App\Models\Business::STATUSES as $s)
            <a href="{{ route('admin.businesses.index', ['status' => $s]) }}" class="rounded-xl bg-white p-4 ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
                <div class="text-2xl font-semibold text-slate-900 dark:text-white">{{ $counts[$s] ?? 0 }}</div>
                <div class="mt-1 text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ $s }}</div>
            </a>
        @endforeach
    </div>

    <div class="mt-6 flex items-center justify-between">
        <h2 class="text-sm font-semibold">Recent activity</h2>
        <a href="{{ route('admin.businesses.create') }}" class="rounded-lg bg-violet-700 px-3 py-1.5 text-sm font-semibold text-white hover:bg-violet-800">New business</a>
    </div>
    <div class="mt-3">@include('admin._logs', ['logs' => $recent])</div>
</x-layouts.superadmin>

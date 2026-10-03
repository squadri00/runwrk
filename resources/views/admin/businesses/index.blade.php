<x-layouts.superadmin title="Businesses">
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <form class="flex gap-2">
            <input name="q" value="{{ request('q') }}" placeholder="Search name or slug" class="w-56 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
            <select name="status" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">All statuses</option>
                @foreach (\App\Models\Business::STATUSES as $s)<option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>@endforeach
            </select>
            <button class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium dark:border-slate-700">Filter</button>
        </form>
        <a href="{{ route('admin.businesses.create') }}" class="rounded-lg bg-violet-700 px-3 py-2 text-sm font-semibold text-white hover:bg-violet-800">New business</a>
    </div>
    <div class="overflow-x-auto rounded-xl bg-white ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-500 dark:border-slate-800 dark:text-slate-400">
                <tr><th class="px-4 py-2.5">Business</th><th class="px-4 py-2.5">Slug</th><th class="px-4 py-2.5">Plan</th><th class="px-4 py-2.5">Users</th><th class="px-4 py-2.5">Status</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                @forelse ($businesses as $b)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                        <td class="px-4 py-2.5 font-medium"><a href="{{ route('admin.businesses.show', $b) }}" class="hover:underline">{{ $b->name }}</a></td>
                        <td class="px-4 py-2.5 text-slate-500 dark:text-slate-400">{{ $b->slug }}</td>
                        <td class="px-4 py-2.5">{{ $b->plan?->name ?? '—' }}</td>
                        <td class="px-4 py-2.5">{{ $b->users_count }}</td>
                        <td class="px-4 py-2.5"><span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs capitalize dark:bg-slate-800">{{ $b->status }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">No businesses.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $businesses->links() }}</div>
</x-layouts.superadmin>

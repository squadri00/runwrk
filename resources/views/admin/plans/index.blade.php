<x-layouts.superadmin title="Plans & pricing">
    <div class="mb-4 flex items-center justify-between">
        <p class="text-sm text-slate-500 dark:text-slate-400">These appear on the public pricing page. The current plans are demo placeholders: edit them with your real pricing.</p>
        <a href="{{ route('admin.plans.create') }}" class="shrink-0 rounded-lg bg-violet-700 px-3 py-2 text-sm font-semibold text-white hover:bg-violet-800">New plan</a>
    </div>
    <div class="overflow-x-auto rounded-xl bg-white ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-500 dark:border-slate-800 dark:text-slate-400">
                <tr><th class="px-4 py-2.5">Plan</th><th class="px-4 py-2.5">Price</th><th class="px-4 py-2.5">Businesses</th><th class="px-4 py-2.5">Visibility</th><th class="px-4 py-2.5"></th></tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                @foreach ($plans as $plan)
                    <tr class="{{ $plan->archived_at ? 'opacity-50' : '' }}">
                        <td class="px-4 py-2.5 font-medium">{{ $plan->name }} <span class="font-mono text-xs text-slate-400">{{ $plan->code }}</span></td>
                        <td class="px-4 py-2.5">{{ $plan->priceLabel() }}@unless ($plan->isFree())/{{ $plan->interval }}@endunless</td>
                        <td class="px-4 py-2.5">{{ $plan->businesses_count }}</td>
                        <td class="px-4 py-2.5 text-xs">{{ $plan->archived_at ? 'Archived' : ($plan->is_public ? 'Public' : 'Hidden') }}</td>
                        <td class="flex justify-end gap-3 px-4 py-2.5 text-sm">
                            <a href="{{ route('admin.plans.edit', $plan) }}" class="underline">Edit</a>
                            <form method="POST" action="{{ route('admin.plans.archive', $plan) }}">@csrf
                                <button class="text-slate-500 underline">{{ $plan->archived_at ? 'Restore' : 'Archive' }}</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-layouts.superadmin>

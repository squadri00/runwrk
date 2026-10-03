<div class="overflow-x-auto rounded-xl bg-white ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
    <table class="w-full text-left text-sm">
        <thead class="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-500 dark:border-slate-800 dark:text-slate-400">
            <tr><th class="px-4 py-2.5">When</th><th class="px-4 py-2.5">Action</th><th class="px-4 py-2.5">By</th><th class="px-4 py-2.5">Details</th></tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
            @forelse ($logs as $log)
                <tr>
                    <td class="whitespace-nowrap px-4 py-2.5 text-slate-500 dark:text-slate-400">{{ $log->created_at?->format('M j, H:i') }}</td>
                    <td class="px-4 py-2.5 font-mono text-xs text-emerald-600 dark:text-emerald-400">{{ $log->action }}</td>
                    <td class="px-4 py-2.5">{{ $log->actor_label }}@if ($log->impersonated_by) <span class="text-amber-600">(support)</span>@endif</td>
                    <td class="px-4 py-2.5 text-xs text-slate-500 dark:text-slate-400">{{ $log->changes ? json_encode($log->changes) : '' }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">Nothing yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

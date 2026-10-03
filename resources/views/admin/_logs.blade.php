<div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
    <table class="w-full text-left text-sm">
        <thead class="border-b border-gray-200 bg-gray-50 text-xs uppercase text-gray-500">
            <tr><th class="px-4 py-2">When</th><th class="px-4 py-2">Action</th><th class="px-4 py-2">By</th><th class="px-4 py-2">Details</th></tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($logs as $log)
                <tr>
                    <td class="whitespace-nowrap px-4 py-2 text-gray-500">{{ $log->created_at?->format('M j, H:i') }}</td>
                    <td class="px-4 py-2 font-medium">{{ $log->action }}</td>
                    <td class="px-4 py-2">{{ $log->actor_label }}@if ($log->impersonated_by) <span class="text-amber-600">(support)</span>@endif</td>
                    <td class="px-4 py-2 text-xs text-gray-500">{{ $log->changes ? json_encode($log->changes) : '' }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">Nothing yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

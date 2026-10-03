<x-layouts.superadmin title="Messages">
    <div class="overflow-x-auto rounded-xl bg-white ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-500 dark:border-slate-800 dark:text-slate-400">
                <tr><th class="px-4 py-2.5">From</th><th class="px-4 py-2.5">Interested in</th><th class="px-4 py-2.5">Message</th><th class="px-4 py-2.5">When</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                @forelse ($messages as $m)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 {{ $m->read_at ? '' : 'font-semibold' }}">
                        <td class="px-4 py-2.5"><a href="{{ route('admin.messages.show', $m) }}" class="hover:underline">{{ $m->name }}</a><div class="text-xs font-normal text-slate-500">{{ $m->email }}</div></td>
                        <td class="px-4 py-2.5">{{ \App\Models\ContactMessage::SERVICES[$m->service] ?? $m->service }}</td>
                        <td class="max-w-xs truncate px-4 py-2.5 font-normal text-slate-500">{{ $m->message }}</td>
                        <td class="whitespace-nowrap px-4 py-2.5 font-normal text-slate-500">{{ $m->created_at->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-10 text-center text-slate-500">No messages yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $messages->links() }}</div>
</x-layouts.superadmin>

<x-layouts.app title="Notifications">
    <div class="mb-4 flex items-center justify-between">
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ number_format($subscribers) }} {{ Str::plural('customer', $subscribers) }} can receive your messages.</p>
        <a href="{{ route('notifications.create') }}" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800 dark:bg-white dark:text-slate-900">New message</a>
    </div>

    <div class="overflow-x-auto rounded-xl bg-white ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-500 dark:border-slate-800 dark:text-slate-400">
                <tr><th class="px-4 py-2.5">Message</th><th class="px-4 py-2.5">Status</th><th class="px-4 py-2.5">Delivered</th><th class="px-4 py-2.5">Opened</th><th class="px-4 py-2.5">When</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                @forelse ($messages as $m)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                        <td class="px-4 py-2.5"><a href="{{ route('notifications.show', $m->id) }}" class="font-medium hover:underline">{{ $m->title }}</a><div class="max-w-xs truncate text-xs text-slate-500">{{ $m->body }}</div></td>
                        <td class="px-4 py-2.5"><x-message-status :status="$m->status" /></td>
                        <td class="px-4 py-2.5">{{ $m->success_count }} / {{ $m->target_count }}</td>
                        <td class="px-4 py-2.5">{{ $m->click_count }}</td>
                        <td class="whitespace-nowrap px-4 py-2.5 text-slate-500 dark:text-slate-400">{{ ($m->started_at ?? $m->scheduled_at)?->timezone(auth('web')->user()->business->timezone)->format('M j, g:i a') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-10 text-center text-slate-500">No messages yet. Send your first one.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $messages->links() }}</div>
</x-layouts.app>

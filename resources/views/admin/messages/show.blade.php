<x-layouts.superadmin :title="'Message from '.$message->name">
    <div class="max-w-2xl space-y-4">
        <div class="rounded-xl bg-white p-5 ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
            <dl class="grid grid-cols-3 gap-y-2 text-sm">
                <dt class="text-slate-500">Name</dt><dd class="col-span-2">{{ $message->name }}</dd>
                <dt class="text-slate-500">Email</dt><dd class="col-span-2"><a class="underline" href="mailto:{{ $message->email }}">{{ $message->email }}</a></dd>
                <dt class="text-slate-500">Phone</dt><dd class="col-span-2">{{ $message->phone ?: '—' }}</dd>
                <dt class="text-slate-500">Interested in</dt><dd class="col-span-2">{{ \App\Models\ContactMessage::SERVICES[$message->service] ?? $message->service }}</dd>
                <dt class="text-slate-500">Received</dt><dd class="col-span-2">{{ $message->created_at->format('M j, Y g:i a') }}</dd>
            </dl>
            <hr class="my-4 border-slate-200 dark:border-slate-800">
            <p class="whitespace-pre-line text-sm">{{ $message->message }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.messages.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm dark:border-slate-700">Back</a>
            <form method="POST" action="{{ route('admin.messages.destroy', $message) }}" onsubmit="return confirm('Delete this message?')">@csrf @method('DELETE')
                <button class="rounded-lg border border-rose-300 px-4 py-2 text-sm font-medium text-rose-600 dark:border-rose-800">Delete</button>
            </form>
        </div>
    </div>
</x-layouts.superadmin>

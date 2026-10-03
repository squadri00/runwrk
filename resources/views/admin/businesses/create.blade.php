<x-layouts.superadmin title="New business">
    <form method="POST" action="{{ route('admin.businesses.store') }}" class="max-w-xl space-y-4 rounded-xl bg-white p-6 ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
        @csrf
        <x-field name="name" label="Business name" />
        <x-field name="slug" label="Slug" placeholder="joes-barber" hint="Used in the hosted app address: runwrk.com/slug" />
        <x-field name="owner_name" label="Owner name" />
        <x-field name="owner_email" label="Owner email" type="email" />
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Plan</label>
                <select name="plan_id" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                    @foreach ($plans as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Status</label>
                <select name="status" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                    @foreach (\App\Models\Business::STATUSES as $s)<option value="{{ $s }}" @selected(old('status', 'trial') === $s)>{{ ucfirst($s) }}</option>@endforeach
                </select>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Allowed domains</label>
            <textarea name="domains" rows="3" placeholder="joesbarber.com&#10;www.joesbarber.com" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">{{ old('domains') }}</textarea>
            <p class="mt-1 text-xs text-slate-400">One per line. Websites that may connect to this business. Leave empty for the hosted app only.</p>
        </div>
        <p class="text-sm text-slate-500 dark:text-slate-400">The owner gets an email to set their own password. You'll also get the link here.</p>
        <button class="rounded-lg bg-violet-700 px-5 py-2 text-sm font-semibold text-white hover:bg-violet-800">Create</button>
    </form>
</x-layouts.superadmin>

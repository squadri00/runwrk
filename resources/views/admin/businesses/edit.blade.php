<x-layouts.superadmin :title="'Edit '.$business->name">
    <form method="POST" action="{{ route('admin.businesses.update', $business) }}" class="max-w-xl space-y-4 rounded-xl bg-white p-6 ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
        @csrf @method('PUT')
        <x-field name="name" label="Business name" :value="$business->name" />
        <x-field name="slug" label="Slug" :value="$business->slug" hint="Changing the slug changes the hosted app address." />
        <x-field name="short_name" label="App name (under the home-screen icon)" :value="$business->short_name" />
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Plan</label>
                <select name="plan_id" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                    <option value="">None</option>
                    @foreach ($plans as $p)<option value="{{ $p->id }}" @selected(old('plan_id', $business->plan_id) == $p->id)>{{ $p->name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Status</label>
                <select name="status" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                    @foreach (\App\Models\Business::STATUSES as $s)<option value="{{ $s }}" @selected(old('status', $business->status) === $s)>{{ ucfirst($s) }}</option>@endforeach
                </select>
                <p class="mt-1 text-xs text-slate-400">Suspended or cancelled stops the API and owner logins at once.</p>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <x-field name="theme_color" label="Theme colour" :value="$business->theme_color" hint="Hex, e.g. #111827" />
            <x-field name="background_color" label="Background colour" :value="$business->background_color" hint="Hex, e.g. #ffffff" />
        </div>
        <x-field name="phone" label="Phone" :value="$business->phone" />
        <x-field name="address" label="Address" :value="$business->address" />
        <x-field name="website_url" label="Website" type="url" :value="$business->website_url" />
        <x-field name="timezone" label="Timezone" :value="$business->timezone" hint="e.g. America/Toronto" />
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Allowed domains</label>
            <textarea name="domains" rows="4" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">{{ old('domains', $business->domains->pluck('domain')->implode("\n")) }}</textarea>
            <p class="mt-1 text-xs text-slate-400">One per line. Use *.example.com to allow subdomains. Removing a domain blocks it immediately.</p>
        </div>
        <div class="flex gap-3">
            <button class="rounded-lg bg-violet-700 px-5 py-2 text-sm font-semibold text-white hover:bg-violet-800">Save</button>
            <a href="{{ route('admin.businesses.show', $business) }}" class="rounded-lg border border-slate-300 px-5 py-2 text-sm dark:border-slate-700">Cancel</a>
        </div>
    </form>
</x-layouts.superadmin>

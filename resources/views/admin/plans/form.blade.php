<x-layouts.superadmin :title="$plan->exists ? 'Edit '.$plan->name : 'New plan'">
    <form method="POST" action="{{ $plan->exists ? route('admin.plans.update', $plan) : route('admin.plans.store') }}" class="max-w-xl space-y-4 rounded-xl bg-white p-6 ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
        @csrf
        @if ($plan->exists) @method('PUT') @endif
        <x-field name="name" label="Name" :value="$plan->name" />
        <x-field name="code" label="Code" :value="$plan->code" hint="Lowercase letters, numbers, hyphens. Used in signup links: /register?plan=code" />
        <x-field name="description" label="Short description" :value="$plan->description" />
        <div class="grid grid-cols-2 gap-4">
            <x-field name="price" label="Price (USD)" type="number" step="0.01" min="0" :value="$plan->exists ? $plan->price_cents / 100 : 0" hint="0 makes it a free plan" />
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Billed</label>
                <select name="interval" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                    <option value="month" @selected(old('interval', $plan->interval) === 'month')>Monthly</option>
                    <option value="year" @selected(old('interval', $plan->interval) === 'year')>Yearly</option>
                </select>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Features</label>
            <textarea name="features" rows="6" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">{{ old('features', implode("\n", $plan->features ?? [])) }}</textarea>
            <p class="mt-1 text-xs text-slate-400">One per line. Shown as ticks on the pricing page.</p>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <x-field name="sort_order" label="Sort order" type="number" min="0" :value="$plan->sort_order ?? 0" />
            <x-field name="stripe_price_id" label="Stripe price ID" :value="$plan->stripe_price_id" hint="Added when billing is connected" />
        </div>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_public" value="1" @checked(old('is_public', $plan->is_public)) class="rounded border-slate-300"> Show on the public pricing page</label>
        <div class="flex gap-3">
            <button class="rounded-lg bg-violet-700 px-5 py-2 text-sm font-semibold text-white hover:bg-violet-800">Save</button>
            <a href="{{ route('admin.plans.index') }}" class="rounded-lg border border-slate-300 px-5 py-2 text-sm dark:border-slate-700">Cancel</a>
        </div>
    </form>
</x-layouts.superadmin>

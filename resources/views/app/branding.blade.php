<x-layouts.app title="Branding">
    <form method="POST" action="{{ route('branding.update') }}" enctype="multipart/form-data" class="max-w-xl space-y-4 rounded-xl bg-white p-6 ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
        @csrf @method('PUT')

        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Logo</label>
            <div class="mt-1 flex items-center gap-4">
                @if ($business->logo_path)
                    <img src="{{ Storage::disk('public')->url($business->logo_path) }}" alt="" class="h-16 w-16 rounded-lg border border-slate-200 object-contain p-1 dark:border-slate-700" style="background: {{ $business->background_color }}">
                @endif
                <input type="file" name="logo" accept="image/png,image/jpeg,image/webp" class="text-sm">
            </div>
            <p class="mt-1 text-xs text-slate-400">Square works best, at least 256 × 256 pixels (PNG, JPG or WebP, up to 2 MB). We make the app icons from it.</p>
            @error('logo')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
            @if ($business->logo_path)<label class="mt-2 flex items-center gap-2 text-sm"><input type="checkbox" name="remove_logo" value="1" class="rounded border-slate-300"> Remove logo</label>@endif
        </div>

        <x-field name="name" label="Business name" :value="$business->name" />
        <x-field name="short_name" label="App name" :value="$business->short_name" hint="Shown under the icon on the home screen. Keep it short." />
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Brand colour</label>
                <input type="color" name="theme_color" value="{{ old('theme_color', $business->theme_color) }}" class="mt-1 h-10 w-full rounded-lg border border-slate-300 bg-white p-1 dark:border-slate-700 dark:bg-slate-900">
                @error('theme_color')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Icon background</label>
                <input type="color" name="background_color" value="{{ old('background_color', $business->background_color) }}" class="mt-1 h-10 w-full rounded-lg border border-slate-300 bg-white p-1 dark:border-slate-700 dark:bg-slate-900">
                @error('background_color')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>
        </div>
        <x-field name="phone" label="Phone" :value="$business->phone" />
        <x-field name="address" label="Address" :value="$business->address" />
        <x-field name="website_url" label="Website" type="url" :value="$business->website_url" />
        <x-field name="timezone" label="Timezone" :value="$business->timezone" hint="e.g. America/Toronto. Used for scheduled messages." />

        <div>
            <div class="text-sm font-medium text-slate-700 dark:text-slate-300">Connected website addresses</div>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $business->domains->pluck('domain')->implode(', ') ?: 'None yet. We set this up with you.' }}</p>
        </div>

        <button class="rounded-lg bg-slate-900 px-5 py-2 text-sm font-semibold text-white hover:bg-slate-800 dark:bg-white dark:text-slate-900">Save</button>
    </form>
</x-layouts.app>

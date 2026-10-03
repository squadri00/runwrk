<x-layouts.app title="New message">
    <div class="grid max-w-4xl gap-6 lg:grid-cols-5">
        <form method="POST" action="{{ route('notifications.store') }}" enctype="multipart/form-data" class="space-y-4 rounded-xl bg-white p-6 ring-1 ring-slate-200 lg:col-span-3 dark:bg-slate-900 dark:ring-slate-800"
              onsubmit="return document.getElementById('when').value !== 'now' || confirm('Send this to {{ number_format($subscribers) }} {{ Str::plural('customer', $subscribers) }} now?')">
            @csrf
            <x-field name="title" label="Title" maxlength="65" id="f-title" oninput="pv()" hint="Short and clear. Up to 65 characters." />
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Message</label>
                <textarea name="body" id="f-body" rows="3" maxlength="200" oninput="pv()" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">{{ old('body') }}</textarea>
                @error('body')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>
            <x-field name="url" label="Link (optional)" type="url" placeholder="https://" hint="Where the customer lands when they tap. Leave empty to open your website or app." />
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Picture (optional)</label>
                <input type="file" name="image" accept="image/png,image/jpeg,image/webp" class="mt-1 text-sm">
                <p class="mt-1 text-xs text-slate-400">Wide pictures work best (PNG, JPG or WebP, up to 1 MB). Shown on Android.</p>
                @error('image')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Send</label>
                    <select name="when" id="when" onchange="document.getElementById('later').hidden = this.value !== 'later'" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                        <option value="now" @selected(old('when', 'now') === 'now')>Now</option>
                        <option value="later" @selected(old('when') === 'later')>Later</option>
                    </select>
                </div>
                <div id="later" @if (old('when') !== 'later') hidden @endif>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Date and time</label>
                    <input type="datetime-local" name="send_at" value="{{ old('send_at') }}" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                    <p class="mt-1 text-xs text-slate-400">{{ $business->timezone }} time</p>
                    @error('send_at')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>
            <button class="rounded-lg bg-slate-900 px-5 py-2 text-sm font-semibold text-white hover:bg-slate-800 dark:bg-white dark:text-slate-900">Send message</button>
            <span class="ml-2 text-xs text-slate-400">to {{ number_format($subscribers) }} {{ Str::plural('customer', $subscribers) }}</span>
        </form>

        <div class="lg:col-span-2">
            <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Preview</div>
            <div class="mt-2 flex gap-3 rounded-2xl bg-white p-3 shadow-sm ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
                <img src="{{ $business->icon_path ? Storage::disk('public')->url($business->icon_path.'/icon-192.png') : asset('assets/icons/default-192.png') }}" alt="" class="h-10 w-10 rounded-lg">
                <div class="min-w-0">
                    <div class="truncate text-sm font-semibold" id="pv-title">Your title</div>
                    <div class="text-sm text-slate-600 dark:text-slate-300" id="pv-body">Your message appears here.</div>
                    <div class="mt-1 text-xs text-slate-400">{{ $business->short_name ?: $business->name }} · now</div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function pv() {
            document.getElementById('pv-title').textContent = document.getElementById('f-title').value || 'Your title';
            document.getElementById('pv-body').textContent = document.getElementById('f-body').value || 'Your message appears here.';
        }
    </script>
</x-layouts.app>

@props(['name', 'label', 'type' => 'text', 'value' => null, 'hint' => null])

<div>
    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ $label }}</label>
    <input type="{{ $type }}" name="{{ $name }}" @if($type !== 'password') value="{{ old($name, $value) }}" @endif
           {{ $attributes->merge(['class' => 'mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-900 focus:ring-slate-900 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100']) }}>
    @if($hint)<p class="mt-1 text-xs text-slate-400">{{ $hint }}</p>@endif
    @error($name)<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
</div>

<div>
    <label class="mb-1 block text-sm font-medium">{{ $label }}</label>
    <input type="{{ $type ?? 'text' }}" name="{{ $name }}" value="{{ old($name, $value ?? '') }}" placeholder="{{ $placeholder ?? '' }}" class="w-full rounded border border-gray-300 px-3 py-2">
    @isset($hint)<p class="mt-1 text-xs text-gray-500">{{ $hint }}</p>@endisset
    @error($name)<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>

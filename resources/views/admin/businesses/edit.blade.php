@extends('layouts.admin')

@section('title', 'Edit '.$business->name.' · Runwrk')

@section('content')
<h1 class="mb-6 text-2xl font-bold">Edit {{ $business->name }}</h1>
<form method="POST" action="{{ route('admin.businesses.update', $business) }}" class="max-w-xl space-y-4 rounded-lg border border-gray-200 bg-white p-6">
    @csrf @method('PUT')
    @include('admin._input', ['name' => 'name', 'label' => 'Business name', 'value' => $business->name])
    @include('admin._input', ['name' => 'slug', 'label' => 'Slug', 'value' => $business->slug, 'hint' => 'Changing the slug changes the hosted app address.'])
    @include('admin._input', ['name' => 'short_name', 'label' => 'App name (under the home-screen icon)', 'value' => $business->short_name])
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="mb-1 block text-sm font-medium">Plan</label>
            <select name="plan_id" class="w-full rounded border border-gray-300 px-3 py-2">
                <option value="">None</option>
                @foreach ($plans as $p)<option value="{{ $p->id }}" @selected(old('plan_id', $business->plan_id) == $p->id)>{{ $p->name }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium">Status</label>
            <select name="status" class="w-full rounded border border-gray-300 px-3 py-2">
                @foreach (\App\Models\Business::STATUSES as $s)<option value="{{ $s }}" @selected(old('status', $business->status) === $s)>{{ ucfirst($s) }}</option>@endforeach
            </select>
            <p class="mt-1 text-xs text-gray-500">Suspended or cancelled stops the API and owner logins at once.</p>
        </div>
    </div>
    <div class="grid grid-cols-2 gap-4">
        @include('admin._input', ['name' => 'theme_color', 'label' => 'Theme colour', 'value' => $business->theme_color, 'hint' => 'Hex, e.g. #111827'])
        @include('admin._input', ['name' => 'background_color', 'label' => 'Background colour', 'value' => $business->background_color, 'hint' => 'Hex, e.g. #ffffff'])
    </div>
    @include('admin._input', ['name' => 'phone', 'label' => 'Phone', 'value' => $business->phone])
    @include('admin._input', ['name' => 'address', 'label' => 'Address', 'value' => $business->address])
    @include('admin._input', ['name' => 'website_url', 'label' => 'Website', 'type' => 'url', 'value' => $business->website_url])
    @include('admin._input', ['name' => 'timezone', 'label' => 'Timezone', 'value' => $business->timezone, 'hint' => 'e.g. America/Toronto'])
    <div>
        <label class="mb-1 block text-sm font-medium">Allowed domains</label>
        <textarea name="domains" rows="4" class="w-full rounded border border-gray-300 px-3 py-2">{{ old('domains', $business->domains->pluck('domain')->implode("\n")) }}</textarea>
        <p class="mt-1 text-xs text-gray-500">One per line. Use *.example.com to allow subdomains. Removing a domain blocks it immediately.</p>
        @error('domains')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div class="flex gap-3">
        <button class="rounded bg-indigo-600 px-5 py-2 font-medium text-white hover:bg-indigo-700">Save</button>
        <a href="{{ route('admin.businesses.show', $business) }}" class="rounded border border-gray-300 px-5 py-2">Cancel</a>
    </div>
</form>
@endsection

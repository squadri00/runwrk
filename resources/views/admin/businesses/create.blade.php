@extends('layouts.admin')

@section('title', 'New business · Runwrk')

@section('content')
<h1 class="mb-6 text-2xl font-bold">New business</h1>
<form method="POST" action="{{ route('admin.businesses.store') }}" class="max-w-xl space-y-4 rounded-lg border border-gray-200 bg-white p-6">
    @csrf
    @include('admin._input', ['name' => 'name', 'label' => 'Business name'])
    @include('admin._input', ['name' => 'slug', 'label' => 'Slug', 'placeholder' => 'joes-barber', 'hint' => 'Used in the hosted app address: runwrk.com/slug'])
    @include('admin._input', ['name' => 'owner_name', 'label' => 'Owner name'])
    @include('admin._input', ['name' => 'owner_email', 'label' => 'Owner email', 'type' => 'email'])
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="mb-1 block text-sm font-medium">Plan</label>
            <select name="plan_id" class="w-full rounded border border-gray-300 px-3 py-2">
                @foreach ($plans as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium">Status</label>
            <select name="status" class="w-full rounded border border-gray-300 px-3 py-2">
                @foreach (\App\Models\Business::STATUSES as $s)<option value="{{ $s }}" @selected(old('status', 'trial') === $s)>{{ ucfirst($s) }}</option>@endforeach
            </select>
        </div>
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Allowed domains</label>
        <textarea name="domains" rows="3" placeholder="joesbarber.com&#10;www.joesbarber.com" class="w-full rounded border border-gray-300 px-3 py-2">{{ old('domains') }}</textarea>
        <p class="mt-1 text-xs text-gray-500">One per line. Websites that may connect to this business. Leave empty for the hosted app only.</p>
    </div>
    <p class="text-sm text-gray-500">A password is generated and shown once after saving.</p>
    <button class="rounded bg-indigo-600 px-5 py-2 font-medium text-white hover:bg-indigo-700">Create</button>
</form>
@endsection

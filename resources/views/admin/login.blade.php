@extends('layouts.app')

@section('title', 'Admin sign in · Runwrk')

@section('body')
<main class="mx-auto flex min-h-screen max-w-sm flex-col justify-center px-4">
    <h1 class="mb-6 text-2xl font-bold">Runwrk admin</h1>
    <form method="POST" action="{{ route('admin.login') }}" class="space-y-4 rounded-lg border border-gray-200 bg-white p-6">
        @csrf
        <div>
            <label class="mb-1 block text-sm font-medium">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus class="w-full rounded border border-gray-300 px-3 py-2">
            @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium">Password</label>
            <input type="password" name="password" required class="w-full rounded border border-gray-300 px-3 py-2">
        </div>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="remember"> Remember me</label>
        <button class="w-full rounded bg-indigo-600 py-2 font-medium text-white hover:bg-indigo-700">Sign in</button>
    </form>
</main>
@endsection

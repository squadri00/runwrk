@extends('layouts.app')

@section('title', $business->name.' · Runwrk')

@section('body')
<main class="mx-auto max-w-3xl px-4 py-16">
    <h1 class="text-2xl font-bold">{{ $business->name }}</h1>
    <p class="mt-2 text-gray-500">Your dashboard is coming in the next update.</p>
</main>
@endsection

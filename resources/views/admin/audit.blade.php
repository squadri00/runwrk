@extends('layouts.admin')

@section('title', 'Audit log · Runwrk')

@section('content')
<h1 class="mb-6 text-2xl font-bold">Audit log</h1>
@include('admin._logs')
<div class="mt-4">{{ $logs->links() }}</div>
@endsection

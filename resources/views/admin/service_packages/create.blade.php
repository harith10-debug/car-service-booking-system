@extends('layouts.app')
@section('title', 'Add Service Package')
@section('content')
<h1 class="page-title mb-3">Add Service Package</h1>
<div class="card p-4">
    <form method="POST" action="{{ route('admin.service-packages.store') }}">
        @csrf
        @include('admin.service_packages._form')
        <div class="d-flex gap-2">
            <button class="btn btn-dark">Save Package</button>
            <a href="{{ route('admin.service-packages.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

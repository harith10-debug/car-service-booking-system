@extends('layouts.app')
@section('title', 'Edit Service Package')
@section('content')
<h1 class="page-title mb-3">Edit Service Package</h1>
<div class="card p-4">
    <form method="POST" action="{{ route('admin.service-packages.update', $servicePackage) }}">
        @csrf @method('PUT')
        @include('admin.service_packages._form', ['servicePackage' => $servicePackage])
        <div class="d-flex gap-2">
            <button class="btn btn-dark">Update Package</button>
            <a href="{{ route('admin.service-packages.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

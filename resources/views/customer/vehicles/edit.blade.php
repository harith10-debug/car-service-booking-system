@extends('layouts.app')
@section('title', 'Edit Vehicle')
@section('content')
<h1 class="page-title mb-3">Edit Vehicle</h1>
<div class="card p-4">
    <form method="POST" action="{{ route('customer.vehicles.update', $vehicle) }}">
        @csrf @method('PUT')
        @include('customer.vehicles._form', ['vehicle' => $vehicle])
        <div class="d-flex gap-2">
            <button class="btn btn-dark">Update Vehicle</button>
            <a href="{{ route('customer.vehicles.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

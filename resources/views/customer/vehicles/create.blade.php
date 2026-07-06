@extends('layouts.app')
@section('title', 'Add Vehicle')
@section('content')
<h1 class="page-title mb-3">Add Vehicle</h1>
<div class="card p-4">
    <form method="POST" action="{{ route('customer.vehicles.store') }}">
        @csrf
        @include('customer.vehicles._form')
        <div class="d-flex gap-2">
            <button class="btn btn-dark">Save Vehicle</button>
            <a href="{{ route('customer.vehicles.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

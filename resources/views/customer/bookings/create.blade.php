@extends('layouts.app')
@section('title', 'Create Booking')
@section('content')
<h1 class="page-title mb-3">Create Booking</h1>
<div class="card p-4">
    <form method="POST" action="{{ route('customer.bookings.store') }}">
        @csrf
        @include('customer.bookings._form')
        <div class="d-flex gap-2">
            <button class="btn btn-dark" @disabled($vehicles->isEmpty())>Submit Booking</button>
            <a href="{{ route('customer.bookings.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

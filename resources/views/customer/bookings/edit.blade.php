@extends('layouts.app')
@section('title', 'Edit Booking')
@section('content')
<h1 class="page-title mb-3">Edit Booking #{{ $booking->id }}</h1>
<div class="alert alert-info">Only pending bookings can be edited before admin approval.</div>
<div class="card p-4">
    <form method="POST" action="{{ route('customer.bookings.update', $booking) }}">
        @csrf @method('PUT')
        @include('customer.bookings._form', ['booking' => $booking])
        <div class="d-flex gap-2">
            <button class="btn btn-dark">Update Booking</button>
            <a href="{{ route('customer.bookings.show', $booking) }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

@extends('layouts.app')
@section('title', 'My Bookings')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title">My Bookings</h1>
    <a href="{{ route('customer.bookings.create') }}" class="btn btn-dark btn-rounded"><i class="bi bi-plus-circle me-1"></i>Create Booking</a>
</div>
<div class="card p-3">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>ID</th><th>Vehicle</th><th>Package</th><th>Workshop</th><th>Date</th><th>Total</th><th>Booking</th><th>Payment</th><th></th></tr></thead>
            <tbody>
            @forelse($bookings as $booking)
                <tr>
                    <td>#{{ $booking->id }}</td>
                    <td>{{ $booking->vehicle->plate_number }}</td>
                    <td>{{ $booking->servicePackage->package_name }}</td>
                    <td>{{ $booking->workshop->name ?? '-' }}</td>
                    <td>{{ $booking->preferred_date->format('d M Y') }} {{ substr($booking->preferred_time,0,5) }}</td>
                    <td>RM {{ number_format($booking->total_price, 2) }}</td>
                    <td>@include('partials.status-badge', ['status' => $booking->status])</td>
                    <td>@include('partials.payment-status-badge', ['payment' => $booking->payment])</td>
                    <td class="text-end">
                        <a href="{{ route('customer.bookings.show', $booking) }}" class="btn btn-sm btn-outline-dark">View</a>
                        @if(!$booking->payment && in_array($booking->status, ['Approved', 'Completed'], true))
                            <a href="{{ route('customer.payments.create', $booking) }}" class="btn btn-sm btn-success">Pay</a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="9" class="text-center text-muted">No bookings found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $bookings->links() }}
</div>
@endsection

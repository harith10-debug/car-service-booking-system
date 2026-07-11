@extends('layouts.app')
@section('title', 'Booking Detail')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title">Booking #{{ $booking->id }}</h1>
    <div class="d-flex gap-2 flex-wrap">
        @if($booking->payment)
            <a href="{{ route('customer.payments.receipt', $booking->payment) }}" class="btn btn-danger"><i class="bi bi-receipt me-1"></i>Receipt</a>
        @elseif(in_array($booking->status, ['Approved', 'Completed'], true))
            <a href="{{ route('customer.payments.create', $booking) }}" class="btn btn-success"><i class="bi bi-credit-card me-1"></i>Pay Now</a>
        @endif
        @if($booking->canBeEditedByCustomer())
            <a href="{{ route('customer.bookings.edit', $booking) }}" class="btn btn-outline-primary">Edit</a>
        @endif
        @if($booking->canBeCancelledByCustomer())
            <form method="POST" action="{{ route('customer.bookings.destroy', $booking) }}" onsubmit="return confirm('Cancel this booking?')">
                @csrf @method('DELETE')
                <button class="btn btn-outline-danger">Cancel Booking</button>
            </form>
        @endif
    </div>
</div>
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card p-4 mb-4">
            <h2 class="h5 fw-bold mb-3">Booking Information</h2>
            <dl class="row mb-0">
                <dt class="col-sm-4">Status</dt><dd class="col-sm-8">@include('partials.status-badge', ['status' => $booking->status])</dd>
                <dt class="col-sm-4">Payment</dt><dd class="col-sm-8">@include('partials.payment-status-badge', ['payment' => $booking->payment])</dd>
                <dt class="col-sm-4">Customer</dt><dd class="col-sm-8">{{ $booking->user->name }}</dd>
                <dt class="col-sm-4">Vehicle</dt><dd class="col-sm-8">{{ $booking->vehicle->plate_number }} - {{ $booking->vehicle->brand }} {{ $booking->vehicle->model }}</dd>
                <dt class="col-sm-4">Service Package</dt><dd class="col-sm-8">{{ $booking->servicePackage->package_name }}</dd>
                <dt class="col-sm-4">Workshop</dt><dd class="col-sm-8">{{ $booking->workshop?->name ?? 'Admin will assign / confirm workshop' }}</dd>
                <dt class="col-sm-4">Workshop Address</dt><dd class="col-sm-8">{{ $booking->workshop?->address ?? '-' }}</dd>
                <dt class="col-sm-4">Date & Time</dt><dd class="col-sm-8">{{ $booking->preferred_date->format('d M Y') }} at {{ substr($booking->preferred_time,0,5) }}</dd>
                <dt class="col-sm-4">Total Price</dt><dd class="col-sm-8">RM {{ number_format($booking->total_price, 2) }}</dd>
                <dt class="col-sm-4">Customer Notes</dt><dd class="col-sm-8">{{ $booking->additional_notes ?: '-' }}</dd>
                <dt class="col-sm-4">Admin Remarks</dt><dd class="col-sm-8">{{ $booking->admin_remarks ?: '-' }}</dd>
            </dl>
        </div>

        @if($booking->payment)
            <div class="card p-4">
                <h2 class="h5 fw-bold mb-3">Payment Summary</h2>
                <dl class="row mb-0">
                    <dt class="col-sm-4">Reference</dt><dd class="col-sm-8">{{ $booking->payment->payment_reference }}</dd>
                    <dt class="col-sm-4">Method</dt><dd class="col-sm-8">{{ $booking->payment->method }}</dd>
                    <dt class="col-sm-4">Discount</dt><dd class="col-sm-8">RM {{ number_format($booking->payment->discount_amount, 2) }}</dd>
                    <dt class="col-sm-4">Total Paid</dt><dd class="col-sm-8 fw-bold text-success">RM {{ number_format($booking->payment->total_paid, 2) }}</dd>
                </dl>
            </div>
        @endif
    </div>
    <div class="col-lg-4">
        <div class="card p-4">
            <h2 class="h5 fw-bold mb-3">Status Log</h2>
            @forelse($booking->statusLogs as $log)
                <div class="border-start ps-3 mb-3">
                    <div class="fw-bold">{{ $log->from_status ?: 'New' }} → {{ $log->to_status }}</div>
                    <div class="small text-muted">{{ $log->created_at->format('d M Y h:i A') }} by {{ $log->changedBy->name ?? 'System' }}</div>
                    <div class="small">{{ $log->remarks }}</div>
                </div>
            @empty
                <p class="text-muted">No status logs.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

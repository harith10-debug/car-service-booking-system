@extends('layouts.app')
@section('title', 'Payment Detail')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title">Payment {{ $payment->payment_reference }}</h1>
    <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary">Back</a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card p-4">
            <h2 class="h5 fw-bold mb-3">Payment Information</h2>
            <dl class="row mb-0">
                <dt class="col-sm-4">Status</dt><dd class="col-sm-8">@include('partials.payment-status-badge', ['payment' => $payment])</dd>
                <dt class="col-sm-4">Customer</dt><dd class="col-sm-8">{{ $payment->user->name }} ({{ $payment->user->email }})</dd>
                <dt class="col-sm-4">Booking</dt><dd class="col-sm-8">#{{ $payment->booking_id }} - {{ $payment->booking->servicePackage->package_name }}</dd>
                <dt class="col-sm-4">Vehicle</dt><dd class="col-sm-8">{{ $payment->booking->vehicle->plate_number }} - {{ $payment->booking->vehicle->brand }} {{ $payment->booking->vehicle->model }}</dd>
                <dt class="col-sm-4">Workshop</dt><dd class="col-sm-8">{{ $payment->booking->workshop->name ?? 'Admin assigned' }}</dd>
                <dt class="col-sm-4">Method</dt><dd class="col-sm-8">{{ $payment->method }} @if($payment->card_last_four) •••• {{ $payment->card_last_four }} @endif</dd>
                <dt class="col-sm-4">Service Amount</dt><dd class="col-sm-8">RM {{ number_format($payment->amount, 2) }}</dd>
                <dt class="col-sm-4">Discount</dt><dd class="col-sm-8">RM {{ number_format($payment->discount_amount, 2) }}</dd>
                <dt class="col-sm-4">Total Paid</dt><dd class="col-sm-8 fw-bold text-success">RM {{ number_format($payment->total_paid, 2) }}</dd>
                <dt class="col-sm-4">Paid At</dt><dd class="col-sm-8">{{ $payment->paid_at?->format('d M Y h:i A') ?? '-' }}</dd>
                <dt class="col-sm-4">Note</dt><dd class="col-sm-8">{{ $payment->transaction_note ?: '-' }}</dd>
            </dl>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card p-4">
            <h2 class="h5 fw-bold mb-3">Linked Booking</h2>
            <a href="{{ route('admin.bookings.show', $payment->booking) }}" class="btn btn-dark w-100 mb-2">Open Booking</a>
            <a href="{{ route('admin.payments.export.pdf', ['reference' => $payment->payment_reference]) }}" class="btn btn-danger w-100"><i class="bi bi-filetype-pdf me-1"></i>Export This Payment</a>
        </div>
    </div>
</div>
@endsection

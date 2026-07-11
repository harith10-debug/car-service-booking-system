@extends('layouts.app')
@section('title', 'Payment Receipt')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <div>
        <h1 class="page-title mb-1">Payment {{ $payment->payment_reference }}</h1>
        <p class="text-muted mb-0">Official receipt and booking payment details.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('customer.payments.receipt', $payment) }}" class="btn btn-danger btn-rounded"><i class="bi bi-filetype-pdf me-1"></i>Download Receipt</a>
        <a href="{{ route('customer.bookings.show', $payment->booking) }}" class="btn btn-outline-secondary">Booking</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card p-4 receipt-preview">
            <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-3">
                <div>
                    <h2 class="h4 fw-bold mb-1">DH Motorsport</h2>
                    <p class="text-muted mb-0">Car Service Booking System</p>
                </div>
                <div class="text-end">
                    <span class="badge bg-success mb-2">{{ $payment->status }}</span>
                    <div class="fw-bold">{{ $payment->payment_reference }}</div>
                </div>
            </div>
            <dl class="row">
                <dt class="col-sm-4">Paid By</dt><dd class="col-sm-8">{{ $payment->payer_name }} ({{ $payment->payer_email ?: $payment->user->email }})</dd>
                <dt class="col-sm-4">Payment Method</dt><dd class="col-sm-8">{{ $payment->method }} @if($payment->card_last_four) •••• {{ $payment->card_last_four }} @endif</dd>
                <dt class="col-sm-4">Paid At</dt><dd class="col-sm-8">{{ $payment->paid_at?->format('d M Y h:i A') }}</dd>
                <dt class="col-sm-4">Booking</dt><dd class="col-sm-8">#{{ $payment->booking_id }} - {{ $payment->booking->servicePackage->package_name }}</dd>
                <dt class="col-sm-4">Vehicle</dt><dd class="col-sm-8">{{ $payment->booking->vehicle->plate_number }} - {{ $payment->booking->vehicle->brand }} {{ $payment->booking->vehicle->model }}</dd>
                <dt class="col-sm-4">Workshop</dt><dd class="col-sm-8">{{ $payment->booking->workshop->name ?? 'Admin assigned' }}</dd>
                <dt class="col-sm-4">Service Amount</dt><dd class="col-sm-8">RM {{ number_format($payment->amount, 2) }}</dd>
                <dt class="col-sm-4">Discount</dt><dd class="col-sm-8">RM {{ number_format($payment->discount_amount, 2) }}</dd>
                <dt class="col-sm-4">Total Paid</dt><dd class="col-sm-8 h5 fw-bold text-success">RM {{ number_format($payment->total_paid, 2) }}</dd>
                <dt class="col-sm-4">Note</dt><dd class="col-sm-8">{{ $payment->transaction_note ?: '-' }}</dd>
            </dl>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card p-4 h-100">
            <h2 class="h5 fw-bold mb-3">Receipt Actions</h2>
            <a href="{{ route('customer.payments.receipt', $payment) }}" class="btn btn-danger w-100 mb-2"><i class="bi bi-download me-1"></i>Download PDF Receipt</a>
            <a href="{{ route('customer.payments.index') }}" class="btn btn-outline-dark w-100">All Payments</a>
            <div class="alert alert-success mt-3 mb-0"><i class="bi bi-check2-circle me-1"></i>Receipt is generated from the payment record stored in the database.</div>
        </div>
    </div>
</div>
@endsection

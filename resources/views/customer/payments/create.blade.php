@extends('layouts.app')
@section('title', 'Pay Booking')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="page-title mb-1">Payment for Booking #{{ $booking->id }}</h1>
        <p class="text-muted mb-0">Complete payment to generate your official receipt.</p>
    </div>
    <a href="{{ route('customer.bookings.show', $booking) }}" class="btn btn-outline-secondary">Back</a>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card p-4">
            <h2 class="h5 fw-bold mb-3">Payment Details</h2>
            <form method="POST" action="{{ route('customer.payments.store', $booking) }}">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Payment Method</label>
                        <select name="method" class="form-select" required>
                            <option value="">-- Select method --</option>
                            @foreach(\App\Models\Payment::METHODS as $method)
                                <option value="{{ $method }}" @selected(old('method') === $method)>{{ $method }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Card Last 4 Digits <span class="text-muted small">optional</span></label>
                        <input name="card_last_four" class="form-control" value="{{ old('card_last_four') }}" placeholder="1234" maxlength="4">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Payer Name</label>
                        <input name="payer_name" class="form-control" value="{{ old('payer_name', auth()->user()->name) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Payer Email</label>
                        <input type="email" name="payer_email" class="form-control" value="{{ old('payer_email', auth()->user()->email) }}">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Transaction Note</label>
                    <textarea name="transaction_note" class="form-control" rows="3" placeholder="Example: FPX transaction number, e-wallet reference, counter note">{{ old('transaction_note') }}</textarea>
                </div>
                <button class="btn btn-brand btn-rounded px-4"><i class="bi bi-credit-card me-1"></i>Pay RM {{ number_format($payableAmount, 2) }}</button>
            </form>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card p-4 h-100">
            <h2 class="h5 fw-bold mb-3">Order Summary</h2>
            <dl class="row mb-0">
                <dt class="col-5">Service</dt><dd class="col-7">{{ $booking->servicePackage->package_name }}</dd>
                <dt class="col-5">Vehicle</dt><dd class="col-7">{{ $booking->vehicle->plate_number }}</dd>
                <dt class="col-5">Workshop</dt><dd class="col-7">{{ $booking->workshop->name ?? 'Admin assigned' }}</dd>
                <dt class="col-5">Service Price</dt><dd class="col-7">RM {{ number_format($booking->total_price, 2) }}</dd>
                <dt class="col-5">Subscription Discount</dt><dd class="col-7">{{ number_format($discountPercentage, 0) }}% (RM {{ number_format($discountAmount, 2) }})</dd>
                <dt class="col-5">Total Payable</dt><dd class="col-7 fw-bold text-success">RM {{ number_format($payableAmount, 2) }}</dd>
            </dl>
            <div class="alert alert-info mt-3 mb-0"><i class="bi bi-shield-check me-1"></i>This demo payment records the transaction safely inside the system database for admin monitoring and receipt generation.</div>
        </div>
    </div>
</div>
@endsection

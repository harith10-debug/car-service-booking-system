@extends('layouts.app')
@section('title', 'My Payments')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <div>
        <h1 class="page-title mb-1">My Payments</h1>
        <p class="text-muted mb-0">Track completed payments and download receipts anytime.</p>
    </div>
    <a href="{{ route('customer.bookings.index') }}" class="btn btn-dark btn-rounded"><i class="bi bi-calendar-check me-1"></i>Go to Bookings</a>
</div>

<div class="card p-3 mb-3">
    <form class="row g-2" method="GET">
        <div class="col-md-4"><input name="reference" class="form-control" value="{{ request('reference') }}" placeholder="Payment reference"></div>
        <div class="col-md-3">
            <select name="method" class="form-select">
                <option value="">All Methods</option>
                @foreach($methods as $method)
                    <option value="{{ $method }}" @selected(request('method') === $method)>{{ $method }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All Status</option>
                @foreach($statuses as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 d-grid"><button class="btn btn-dark">Filter</button></div>
    </form>
</div>

<div class="card p-3">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>Reference</th><th>Booking</th><th>Vehicle</th><th>Method</th><th>Paid At</th><th>Total Paid</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse($payments as $payment)
                <tr>
                    <td class="fw-bold">{{ $payment->payment_reference }}</td>
                    <td>#{{ $payment->booking_id }} - {{ $payment->booking->servicePackage->package_name ?? '-' }}</td>
                    <td>{{ $payment->booking->vehicle->plate_number ?? '-' }}</td>
                    <td>{{ $payment->method }}</td>
                    <td>{{ $payment->paid_at?->format('d M Y h:i A') ?? '-' }}</td>
                    <td>RM {{ number_format($payment->total_paid, 2) }}</td>
                    <td>@include('partials.payment-status-badge', ['payment' => $payment])</td>
                    <td class="text-end">
                        <a href="{{ route('customer.payments.show', $payment) }}" class="btn btn-sm btn-outline-dark">View</a>
                        <a href="{{ route('customer.payments.receipt', $payment) }}" class="btn btn-sm btn-danger"><i class="bi bi-receipt me-1"></i>Receipt</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted">No payment records yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $payments->links() }}
</div>
@endsection

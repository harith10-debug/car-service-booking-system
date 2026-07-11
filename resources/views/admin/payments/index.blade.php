@extends('layouts.app')
@section('title', 'Payment & Sales Monitor')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <div>
        <h1 class="page-title mb-1">Payment & Sales Monitor</h1>
        <p class="text-muted mb-0">Monitor customer payments, method performance and total revenue.</p>
    </div>
    <a href="{{ route('admin.payments.export.pdf', request()->query()) }}" class="btn btn-danger btn-rounded"><i class="bi bi-filetype-pdf me-1"></i>Export Sales PDF</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="card stat-card p-3"><div class="text-muted">Total Paid Sales</div><div class="display-6 fw-bold">RM {{ number_format($totalSales, 2) }}</div></div></div>
    <div class="col-md-4"><div class="card stat-card p-3"><div class="text-muted">Paid Transactions</div><div class="display-6 fw-bold">{{ $paidPayments }}</div></div></div>
    <div class="col-md-4"><div class="card stat-card p-3"><div class="text-muted">Average Payment</div><div class="display-6 fw-bold">RM {{ number_format($averagePayment, 2) }}</div></div></div>
</div>

<div class="card p-3 mb-3">
    <form class="row g-2" method="GET">
        <div class="col-md-2"><input name="customer" class="form-control" value="{{ request('customer') }}" placeholder="Customer"></div>
        <div class="col-md-2"><input name="reference" class="form-control" value="{{ request('reference') }}" placeholder="Reference"></div>
        <div class="col-md-2">
            <select name="method" class="form-select">
                <option value="">All Methods</option>
                @foreach($methods as $method)
                    <option value="{{ $method }}" @selected(request('method') === $method)>{{ $method }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">All Status</option>
                @foreach($statuses as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2"><input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}"></div>
        <div class="col-md-2"><input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}"></div>
        <div class="col-12 d-grid d-md-flex gap-2 justify-content-md-end"><button class="btn btn-dark px-4">Filter</button><a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary">Reset</a></div>
    </form>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card p-3">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead><tr><th>Reference</th><th>Customer</th><th>Booking</th><th>Method</th><th>Paid At</th><th>Total</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td class="fw-bold">{{ $payment->payment_reference }}</td>
                            <td>{{ $payment->user->name }}</td>
                            <td>#{{ $payment->booking_id }} - {{ $payment->booking->servicePackage->package_name ?? '-' }}</td>
                            <td>{{ $payment->method }}</td>
                            <td>{{ $payment->paid_at?->format('d M Y') ?? '-' }}</td>
                            <td>RM {{ number_format($payment->total_paid, 2) }}</td>
                            <td>@include('partials.payment-status-badge', ['payment' => $payment])</td>
                            <td class="text-end"><a href="{{ route('admin.payments.show', $payment) }}" class="btn btn-sm btn-outline-dark">View</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted">No payments found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            {{ $payments->links() }}
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card p-3 h-100">
            <h2 class="h5 fw-bold mb-3">Sales by Method</h2>
            @forelse($methodTotals as $methodTotal)
                @php $percentage = $totalSales > 0 ? min(100, ($methodTotal->total / $totalSales) * 100) : 0; @endphp
                <div class="mb-3">
                    <div class="d-flex justify-content-between small mb-1"><span>{{ $methodTotal->method }}</span><span>RM {{ number_format($methodTotal->total, 2) }}</span></div>
                    <div class="progress"><div class="progress-bar" role="progressbar" style="width: {{ $percentage }}%"></div></div>
                </div>
            @empty
                <p class="text-muted">No paid sales yet.</p>
            @endforelse
            <div class="alert alert-info mb-0">Use this page during presentation to show admin payment and sale monitoring.</div>
        </div>
    </div>
</div>
@endsection

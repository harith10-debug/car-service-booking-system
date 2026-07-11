@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Admin Dashboard</h1>
        <p class="text-muted mb-0">Monitor customers, booking acceptance, payments, sales, workshops and subscriptions.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('admin.bookings.index', ['status' => 'Pending']) }}" class="btn btn-dark btn-rounded"><i class="bi bi-check2-square me-1"></i>Accept Bookings</a>
        <a href="{{ route('admin.payments.index') }}" class="btn btn-danger btn-rounded"><i class="bi bi-cash-stack me-1"></i>Payment & Sales</a>
    </div>
</div>

<div class="row g-3 mb-4">
    @foreach([
        ['Customers', $totalCustomers, 'bi-people'],
        ['Vehicles', $totalVehicles, 'bi-car-front'],
        ['Packages', $totalPackages, 'bi-box-seam'],
        ['Bookings', $totalBookings, 'bi-calendar-check'],
        ['Pending', $pendingBookings, 'bi-hourglass-split'],
        ['Approved', $approvedBookings, 'bi-check2-square'],
        ['Paid Bookings', $paidBookings, 'bi-credit-card'],
        ['Workshops', $workshopCount, 'bi-building-gear'],
    ] as [$label, $value, $icon])
        <div class="col-xl-3 col-md-4 col-6">
            <div class="card stat-card p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="text-muted small">{{ $label }}</div>
                        <div class="h3 fw-bold">{{ $value }}</div>
                    </div>
                    <i class="bi {{ $icon }} fs-3 text-secondary"></i>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-4"><div class="card stat-card p-3"><div class="text-muted">Total Paid Sales</div><div class="display-6 fw-bold">RM {{ number_format($totalSales, 2) }}</div><a href="{{ route('admin.payments.index') }}" class="small">View sales monitor</a></div></div>
    <div class="col-lg-4"><div class="card stat-card p-3"><div class="text-muted">Today's Sales</div><div class="display-6 fw-bold">RM {{ number_format($todaySales, 2) }}</div><span class="small text-muted">Based on paid payment records</span></div></div>
    <div class="col-lg-4"><div class="card stat-card p-3"><div class="text-muted">Active Subscriptions</div><div class="display-6 fw-bold">{{ $activeSubscriptions }}</div><a href="{{ route('admin.subscriptions.index') }}" class="small">Monitor subscription plan</a></div></div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card p-3 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h5 fw-bold mb-0">Booking Acceptance Queue</h2>
                <a href="{{ route('admin.bookings.index', ['status' => 'Pending']) }}" class="btn btn-sm btn-outline-dark">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead><tr><th>ID</th><th>Customer</th><th>Package</th><th>Date</th><th>Workshop</th><th></th></tr></thead>
                    <tbody>
                    @forelse($pendingQueue as $booking)
                        <tr>
                            <td>#{{ $booking->id }}</td>
                            <td>{{ $booking->user->name }}</td>
                            <td>{{ $booking->servicePackage->package_name }}</td>
                            <td>{{ $booking->preferred_date->format('d M') }} {{ substr($booking->preferred_time,0,5) }}</td>
                            <td>{{ $booking->workshop->name ?? '-' }}</td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('admin.bookings.approve', $booking) }}" class="d-inline">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-sm btn-success">Accept</button>
                                </form>
                                <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-sm btn-outline-dark">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted">No pending bookings.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card p-3 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h5 fw-bold mb-0">Recent Payments</h2>
                <a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-outline-dark">Sales</a>
            </div>
            @forelse($recentPayments as $payment)
                <div class="border rounded p-3 mb-2">
                    <div class="d-flex justify-content-between"><strong>{{ $payment->payment_reference }}</strong><span>RM {{ number_format($payment->total_paid, 2) }}</span></div>
                    <div class="small text-muted">{{ $payment->user->name }} • {{ $payment->method }} • {{ $payment->paid_at?->format('d M h:i A') }}</div>
                </div>
            @empty
                <p class="text-muted">No paid payments yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

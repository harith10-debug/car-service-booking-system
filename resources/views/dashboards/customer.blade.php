@extends('layouts.app')

@section('title', 'Customer Dashboard')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Customer Dashboard</h1>
        <p class="text-muted mb-0">Book services, pay approved bookings, download receipts and find nearby workshops.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('customer.workshops.index') }}" class="btn btn-outline-brand btn-rounded"><i class="bi bi-geo-alt me-1"></i>Find Workshop</a>
        <a href="{{ route('customer.bookings.create') }}" class="btn btn-dark btn-rounded"><i class="bi bi-plus-circle me-1"></i>New Booking</a>
    </div>
</div>

@if($activeSubscription)
    <div class="alert alert-success"><i class="bi bi-stars me-1"></i>{{ $activeSubscription->plan->plan_name }} active: {{ number_format($activeSubscription->plan->discount_percentage, 0) }}% service discount until {{ $activeSubscription->ends_at->format('d M Y') }}.</div>
@else
    <div class="alert alert-info d-flex flex-wrap justify-content-between align-items-center gap-2"><span><i class="bi bi-gem me-1"></i>Subscribe to unlock discounts, priority bookings and admin-recognised membership benefits.</span><a href="{{ route('customer.subscriptions.index') }}" class="btn btn-sm btn-outline-dark">View Subscription</a></div>
@endif

<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card p-3 stat-card"><div class="text-muted">My Vehicles</div><div class="display-6 fw-bold">{{ $vehicleCount }}</div></div></div>
    <div class="col-md-3"><div class="card p-3 stat-card"><div class="text-muted">My Bookings</div><div class="display-6 fw-bold">{{ $bookingCount }}</div></div></div>
    <div class="col-md-3"><div class="card p-3 stat-card"><div class="text-muted">Pending Approval</div><div class="display-6 fw-bold">{{ $pendingBookings }}</div></div></div>
    <div class="col-md-3"><div class="card p-3 stat-card"><div class="text-muted">Need Payment</div><div class="display-6 fw-bold">{{ $unpaidApprovedBookings }}</div></div></div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card p-3 h-100">
            <h2 class="h5 fw-bold mb-3">Latest Bookings</h2>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead><tr><th>ID</th><th>Vehicle</th><th>Package</th><th>Date</th><th>Booking</th><th>Payment</th><th></th></tr></thead>
                    <tbody>
                    @forelse($latestBookings as $booking)
                        <tr>
                            <td>#{{ $booking->id }}</td>
                            <td>{{ $booking->vehicle->plate_number }}</td>
                            <td>{{ $booking->servicePackage->package_name }}</td>
                            <td>{{ $booking->preferred_date->format('d M Y') }}</td>
                            <td>@include('partials.status-badge', ['status' => $booking->status])</td>
                            <td>@include('partials.payment-status-badge', ['payment' => $booking->payment])</td>
                            <td>
                                <a href="{{ route('customer.bookings.show', $booking) }}" class="btn btn-sm btn-outline-dark">View</a>
                                @if(!$booking->payment && in_array($booking->status, ['Approved', 'Completed'], true))
                                    <a href="{{ route('customer.payments.create', $booking) }}" class="btn btn-sm btn-success">Pay</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">No bookings yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card p-3 mb-4">
            <h2 class="h5 fw-bold mb-3">Popular Packages</h2>
            @foreach($activePackages as $package)
                <div class="border rounded p-3 mb-2">
                    <div class="fw-bold">{{ $package->package_name }}</div>
                    <div class="small text-muted">{{ $package->estimated_duration }} minutes</div>
                    <div class="fw-bold mt-1">RM {{ number_format($package->price, 2) }}</div>
                </div>
            @endforeach
            <a href="{{ route('customer.packages.index') }}" class="btn btn-outline-dark mt-2">View All Packages</a>
        </div>
        <div class="card p-3">
            <h2 class="h5 fw-bold mb-3">Nearby Workshops</h2>
            @foreach($nearestWorkshops as $workshop)
                <div class="border rounded p-3 mb-2">
                    <div class="fw-bold">{{ $workshop->name }}</div>
                    <div class="small text-muted">{{ $workshop->city }} • {{ $workshop->opening_hours }}</div>
                </div>
            @endforeach
            <a href="{{ route('customer.workshops.index') }}" class="btn btn-outline-dark mt-2">Find Nearby</a>
        </div>
    </div>
</div>
@endsection

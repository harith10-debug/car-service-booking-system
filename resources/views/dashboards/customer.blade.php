@extends('layouts.app')

@section('title', 'Customer Dashboard')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Customer Dashboard</h1>
        <p class="text-muted mb-0">Book services and manage your registered vehicles.</p>
    </div>
    <a href="{{ route('customer.bookings.create') }}" class="btn btn-dark btn-rounded"><i class="bi bi-plus-circle me-1"></i>New Booking</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="card p-3 stat-card"><div class="text-muted">My Vehicles</div><div class="display-6 fw-bold">{{ $vehicleCount }}</div></div></div>
    <div class="col-md-4"><div class="card p-3 stat-card"><div class="text-muted">My Bookings</div><div class="display-6 fw-bold">{{ $bookingCount }}</div></div></div>
    <div class="col-md-4"><div class="card p-3 stat-card"><div class="text-muted">Pending Approval</div><div class="display-6 fw-bold">{{ $pendingBookings }}</div></div></div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card p-3 h-100">
            <h2 class="h5 fw-bold mb-3">Latest Bookings</h2>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead><tr><th>ID</th><th>Vehicle</th><th>Package</th><th>Date</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                    @forelse($latestBookings as $booking)
                        <tr>
                            <td>#{{ $booking->id }}</td>
                            <td>{{ $booking->vehicle->plate_number }}</td>
                            <td>{{ $booking->servicePackage->package_name }}</td>
                            <td>{{ $booking->preferred_date->format('d M Y') }}</td>
                            <td>@include('partials.status-badge', ['status' => $booking->status])</td>
                            <td><a href="{{ route('customer.bookings.show', $booking) }}" class="btn btn-sm btn-outline-dark">View</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted">No bookings yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card p-3 h-100">
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
    </div>
</div>
@endsection

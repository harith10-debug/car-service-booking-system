@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Admin Dashboard</h1>
        <p class="text-muted mb-0">Monitor customers, vehicles, packages and booking workflow.</p>
    </div>
    <a href="{{ route('admin.bookings.index') }}" class="btn btn-dark btn-rounded"><i class="bi bi-search me-1"></i>Search Bookings</a>
</div>

<div class="row g-3 mb-4">
    @foreach([
        ['Customers', $totalCustomers, 'bi-people'],
        ['Vehicles', $totalVehicles, 'bi-car-front'],
        ['Packages', $totalPackages, 'bi-box-seam'],
        ['Bookings', $totalBookings, 'bi-calendar-check'],
        ['Pending', $pendingBookings, 'bi-hourglass-split'],
        ['Completed', $completedBookings, 'bi-check2-circle'],
    ] as [$label, $value, $icon])
        <div class="col-xl-2 col-md-4 col-6">
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

<div class="card p-3">
    <h2 class="h5 fw-bold mb-3">Latest Bookings</h2>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>ID</th><th>Customer</th><th>Plate</th><th>Package</th><th>Date</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse($latestBookings as $booking)
                <tr>
                    <td>#{{ $booking->id }}</td>
                    <td>{{ $booking->user->name }}</td>
                    <td>{{ $booking->vehicle->plate_number }}</td>
                    <td>{{ $booking->servicePackage->package_name }}</td>
                    <td>{{ $booking->preferred_date->format('d M Y') }} {{ substr($booking->preferred_time,0,5) }}</td>
                    <td>@include('partials.status-badge', ['status' => $booking->status])</td>
                    <td><a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-sm btn-outline-dark">View</a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted">No bookings yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

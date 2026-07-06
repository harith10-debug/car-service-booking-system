@extends('layouts.app')
@section('title', 'Manage Bookings')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title">Manage Bookings</h1>
    <a href="{{ route('admin.reports.bookings.pdf', request()->query()) }}" class="btn btn-danger btn-rounded"><i class="bi bi-filetype-pdf me-1"></i>Export PDF</a>
</div>
<div class="card p-3 mb-3">
    <form class="row g-2" method="GET">
        <div class="col-md-2"><input name="customer" class="form-control" value="{{ request('customer') }}" placeholder="Customer name"></div>
        <div class="col-md-2"><input name="plate_number" class="form-control" value="{{ request('plate_number') }}" placeholder="Plate number"></div>
        <div class="col-md-2"><input name="service_type" class="form-control" value="{{ request('service_type') }}" placeholder="Service type"></div>
        <div class="col-md-2"><input type="date" name="preferred_date" class="form-control" value="{{ request('preferred_date') }}"></div>
        <div class="col-md-2">
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
            <thead><tr><th>ID</th><th>Customer</th><th>Plate</th><th>Package</th><th>Date</th><th>Time</th><th>Total</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse($bookings as $booking)
                <tr>
                    <td>#{{ $booking->id }}</td>
                    <td>{{ $booking->user->name }}</td>
                    <td>{{ $booking->vehicle->plate_number }}</td>
                    <td>{{ $booking->servicePackage->package_name }}</td>
                    <td>{{ $booking->preferred_date->format('d M Y') }}</td>
                    <td>{{ substr($booking->preferred_time,0,5) }}</td>
                    <td>RM {{ number_format($booking->total_price, 2) }}</td>
                    <td>@include('partials.status-badge', ['status' => $booking->status])</td>
                    <td class="text-end"><a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-sm btn-outline-dark">View</a></td>
                </tr>
            @empty
                <tr><td colspan="9" class="text-center text-muted">No bookings found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $bookings->links() }}
</div>
@endsection

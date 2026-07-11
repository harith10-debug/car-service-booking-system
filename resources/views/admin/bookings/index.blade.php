@extends('layouts.app')
@section('title', 'Booking Acceptance Monitor')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <div>
        <h1 class="page-title mb-1">Booking Acceptance Monitor</h1>
        <p class="text-muted mb-0">Review, approve, reject and monitor payment status from one place.</p>
    </div>
    <a href="{{ route('admin.reports.bookings.pdf', request()->query()) }}" class="btn btn-danger btn-rounded"><i class="bi bi-filetype-pdf me-1"></i>Export PDF</a>
</div>
<div class="card p-3 mb-3">
    <form class="row g-2" method="GET">
        <div class="col-md-2"><input name="customer" class="form-control" value="{{ request('customer') }}" placeholder="Customer name"></div>
        <div class="col-md-2"><input name="plate_number" class="form-control" value="{{ request('plate_number') }}" placeholder="Plate number"></div>
        <div class="col-md-2"><input name="service_type" class="form-control" value="{{ request('service_type') }}" placeholder="Service type"></div>
        <div class="col-md-2"><input name="workshop" class="form-control" value="{{ request('workshop') }}" placeholder="Workshop"></div>
        <div class="col-md-2"><input type="date" name="preferred_date" class="form-control" value="{{ request('preferred_date') }}"></div>
        <div class="col-md-1">
            <select name="status" class="form-select">
                <option value="">Status</option>
                @foreach($statuses as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-1">
            <select name="payment_status" class="form-select">
                <option value="">Pay</option>
                <option value="Paid" @selected(request('payment_status') === 'Paid')>Paid</option>
                <option value="Unpaid" @selected(request('payment_status') === 'Unpaid')>Unpaid</option>
            </select>
        </div>
        <div class="col-12 d-grid d-md-flex justify-content-md-end gap-2"><button class="btn btn-dark px-4">Filter</button><a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary">Reset</a></div>
    </form>
</div>
<div class="card p-3">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>ID</th><th>Customer</th><th>Plate</th><th>Package</th><th>Workshop</th><th>Date</th><th>Total</th><th>Status</th><th>Payment</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
            @forelse($bookings as $booking)
                <tr>
                    <td>#{{ $booking->id }}</td>
                    <td>{{ $booking->user->name }}</td>
                    <td>{{ $booking->vehicle->plate_number }}</td>
                    <td>{{ $booking->servicePackage->package_name }}</td>
                    <td>{{ $booking->workshop->name ?? '-' }}</td>
                    <td>{{ $booking->preferred_date->format('d M Y') }} {{ substr($booking->preferred_time,0,5) }}</td>
                    <td>RM {{ number_format($booking->total_price, 2) }}</td>
                    <td>@include('partials.status-badge', ['status' => $booking->status])</td>
                    <td>@include('partials.payment-status-badge', ['payment' => $booking->payment])</td>
                    <td class="text-end">
                        <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-sm btn-outline-dark">View</a>
                        @if($booking->status === 'Pending')
                            <form method="POST" action="{{ route('admin.bookings.approve', $booking) }}" class="d-inline">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-success">Accept</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="10" class="text-center text-muted">No bookings found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $bookings->links() }}
</div>
@endsection

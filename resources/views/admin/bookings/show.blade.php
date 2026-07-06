@extends('layouts.app')
@section('title', 'Admin Booking Detail')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title">Booking #{{ $booking->id }}</h1>
    <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary">Back</a>
</div>
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card p-4 mb-4">
            <h2 class="h5 fw-bold mb-3">Booking Detail</h2>
            <dl class="row mb-0">
                <dt class="col-sm-4">Status</dt><dd class="col-sm-8">@include('partials.status-badge', ['status' => $booking->status])</dd>
                <dt class="col-sm-4">Customer</dt><dd class="col-sm-8">{{ $booking->user->name }} ({{ $booking->user->email }})</dd>
                <dt class="col-sm-4">Vehicle</dt><dd class="col-sm-8">{{ $booking->vehicle->plate_number }} - {{ $booking->vehicle->brand }} {{ $booking->vehicle->model }} {{ $booking->vehicle->year }}</dd>
                <dt class="col-sm-4">Service Package</dt><dd class="col-sm-8">{{ $booking->servicePackage->package_name }}</dd>
                <dt class="col-sm-4">Date & Time</dt><dd class="col-sm-8">{{ $booking->preferred_date->format('d M Y') }} at {{ substr($booking->preferred_time,0,5) }}</dd>
                <dt class="col-sm-4">Total Price</dt><dd class="col-sm-8">RM {{ number_format($booking->total_price, 2) }}</dd>
                <dt class="col-sm-4">Customer Notes</dt><dd class="col-sm-8">{{ $booking->additional_notes ?: '-' }}</dd>
                <dt class="col-sm-4">Admin Remarks</dt><dd class="col-sm-8">{{ $booking->admin_remarks ?: '-' }}</dd>
            </dl>
        </div>

        <div class="card p-4">
            <h2 class="h5 fw-bold mb-3">Admin Actions</h2>
            <div class="d-flex flex-wrap gap-2">
                <form method="POST" action="{{ route('admin.bookings.approve', $booking) }}">
                    @csrf @method('PATCH')
                    <button class="btn btn-primary" @disabled($booking->status === 'Cancelled' || $booking->status === 'Completed')>Approve</button>
                </form>
                <form method="POST" action="{{ route('admin.bookings.complete', $booking) }}">
                    @csrf @method('PATCH')
                    <button class="btn btn-success" @disabled($booking->status === 'Cancelled' || $booking->status === 'Rejected')>Mark Completed</button>
                </form>
            </div>
            <form method="POST" action="{{ route('admin.bookings.reject', $booking) }}" class="mt-3">
                @csrf @method('PATCH')
                <label class="form-label">Reject Remarks</label>
                <textarea name="admin_remarks" class="form-control mb-2" rows="3" placeholder="Reason for rejection"></textarea>
                <button class="btn btn-danger" @disabled($booking->status === 'Cancelled' || $booking->status === 'Completed')>Reject Booking</button>
            </form>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card p-4">
            <h2 class="h5 fw-bold mb-3">Status Log</h2>
            @forelse($booking->statusLogs as $log)
                <div class="border-start ps-3 mb-3">
                    <div class="fw-bold">{{ $log->from_status ?: 'New' }} → {{ $log->to_status }}</div>
                    <div class="small text-muted">{{ $log->created_at->format('d M Y h:i A') }} by {{ $log->changedBy->name ?? 'System' }}</div>
                    <div class="small">{{ $log->remarks }}</div>
                </div>
            @empty
                <p class="text-muted">No status logs.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

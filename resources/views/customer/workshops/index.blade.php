@extends('layouts.app')
@section('title', 'Nearby Workshops')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <span class="section-badge mb-2"><i class="bi bi-geo-alt"></i> Workshop Finder</span>
        <h1 class="page-title mb-1">Find Nearby Workshop</h1>
        <p class="text-muted mb-0">Default distance is calculated from Shah Alam. Use your location for a more accurate nearby list.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <button class="btn btn-brand btn-rounded" data-use-location><i class="bi bi-crosshair me-1"></i>Use My Location</button>
        <a href="{{ route('customer.bookings.create') }}" class="btn btn-outline-brand btn-rounded"><i class="bi bi-calendar2-plus me-1"></i>Create Booking</a>
    </div>
</div>

<div class="card p-3 mb-4">
    <form class="row g-2" method="GET">
        <div class="col-md-10"><input name="search" class="form-control" value="{{ request('search') }}" placeholder="Search workshop, city, address or service e.g. Shah Alam, aircond, tyres"></div>
        <div class="col-md-2 d-grid"><button class="btn btn-dark">Search</button></div>
    </form>
</div>

<div class="row g-4">
@forelse($workshops as $workshop)
    <div class="col-lg-4 col-md-6">
        <div class="card p-4 h-100 workshop-card">
            <div class="d-flex justify-content-between gap-2 mb-3">
                <span class="service-icon"><i class="bi bi-building-gear"></i></span>
                <span class="distance-pill"><i class="bi bi-signpost-split me-1"></i>{{ number_format($workshop->distance_km, 1) }} km</span>
            </div>
            <h2 class="h5 fw-bold mb-2">{{ $workshop->name }}</h2>
            <p class="text-muted mb-2"><i class="bi bi-geo-alt me-1"></i>{{ $workshop->address }}, {{ $workshop->city }}</p>
            <p class="small mb-2"><i class="bi bi-clock me-1"></i>{{ $workshop->opening_hours ?: 'Opening hours not set' }}</p>
            <p class="small mb-3"><i class="bi bi-tools me-1"></i>{{ $workshop->services ?: 'General service, inspection and maintenance' }}</p>
            <div class="d-grid gap-2 mt-auto">
                <a href="{{ route('customer.bookings.create', ['workshop_id' => $workshop->id]) }}" class="btn btn-brand btn-rounded">Book at This Workshop</a>
                @if($workshop->maps_url)
                    <a href="{{ $workshop->maps_url }}" target="_blank" rel="noopener" class="btn btn-outline-dark btn-rounded"><i class="bi bi-map me-1"></i>Open Map</a>
                @endif
            </div>
        </div>
    </div>
@empty
    <div class="col-12"><div class="alert alert-info">No active workshops found.</div></div>
@endforelse
</div>
@endsection

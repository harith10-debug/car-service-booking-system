@extends('layouts.app')
@section('title', 'Service Packages')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <span class="section-badge mb-2"><i class="bi bi-box-seam"></i> Available Services</span>
        <h1 class="page-title mb-1">Choose Your Service Package</h1>
        <p class="text-muted mb-0">Review DH Motorsport service packages and continue to booking when you are ready.</p>
    </div>
    <a href="{{ route('customer.bookings.create') }}" class="btn btn-brand btn-rounded px-4"><i class="bi bi-calendar2-plus me-1"></i>Book Now</a>
</div>

<div class="row g-4">
@forelse($packages as $package)
    <div class="col-lg-4 col-md-6">
        <div class="landing-service-card h-100" data-service-card data-service-group="customer-packages" data-package-id="{{ $package->id }}" tabindex="0" role="button" aria-label="Select {{ $package->package_name }}">
            <span class="service-icon mb-3"><i class="bi bi-tools"></i></span>
            <h2 class="h5 fw-bold mb-2">{{ $package->package_name }}</h2>
            <p class="text-muted">{{ $package->description ?: 'DH Motorsport service package for your vehicle appointment.' }}</p>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <span class="duration-pill"><i class="bi bi-clock me-1"></i>{{ $package->estimated_duration }} minutes</span>
                <span class="price-pill">RM {{ number_format($package->price, 2) }}</span>
            </div>
            <a href="{{ route('customer.bookings.create', ['service_package_id' => $package->id]) }}" class="btn btn-outline-brand btn-rounded w-100 mt-4">
                Select & Book This Service
            </a>
        </div>
    </div>
@empty
    <div class="col-12"><div class="alert alert-info">No active packages available.</div></div>
@endforelse
</div>
<div class="mt-3">{{ $packages->links() }}</div>
@endsection

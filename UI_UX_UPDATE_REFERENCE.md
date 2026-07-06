# UI/UX Landing Page Update Reference
This file lists every modified or added file for the Car Service Booking Management System UI/UX improvement. Replace the files in your project using the exact paths shown below.
## Modified / Added Files
- `routes/web.php`
- `resources/views/layouts/app.blade.php`
- `resources/views/welcome.blade.php`
- `resources/views/customer/bookings/_form.blade.php`
- `resources/views/customer/packages/index.blade.php`
- `public/css/custom.css`
- `public/js/ui.js`

## Full Updated Code
### `routes/web.php`
```php
<?php

use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ServicePackageController as AdminServicePackageController;
use App\Http\Controllers\Admin\VehicleController as AdminVehicleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Customer\BookingController as CustomerBookingController;
use App\Http\Controllers\Customer\PackageController as CustomerPackageController;
use App\Http\Controllers\Customer\VehicleController as CustomerVehicleController;
use App\Http\Controllers\DashboardController;
use App\Models\ServicePackage;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    $landingPackages = collect();

    try {
        $landingPackages = ServicePackage::where('status', 'Active')
            ->orderBy('price')
            ->take(6)
            ->get();
    } catch (Throwable $exception) {
        // Keep the public landing page available even before database migration.
        $landingPackages = collect();
    }

    return view('welcome', compact('landingPackages'));
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('role:customer')->prefix('customer')->name('customer.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'customer'])->name('dashboard');
        Route::resource('vehicles', CustomerVehicleController::class)->except(['show']);
        Route::get('/packages', [CustomerPackageController::class, 'index'])->name('packages.index');
        Route::resource('bookings', CustomerBookingController::class);
    });

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
        Route::resource('customers', AdminCustomerController::class)->only(['index', 'destroy']);
        Route::resource('vehicles', AdminVehicleController::class)->only(['index']);
        Route::resource('service-packages', AdminServicePackageController::class)
            ->parameters(['service-packages' => 'servicePackage'])
            ->except(['show']);
        Route::resource('bookings', AdminBookingController::class)->only(['index', 'show']);
        Route::patch('/bookings/{booking}/approve', [AdminBookingController::class, 'approve'])->name('bookings.approve');
        Route::patch('/bookings/{booking}/reject', [AdminBookingController::class, 'reject'])->name('bookings.reject');
        Route::patch('/bookings/{booking}/complete', [AdminBookingController::class, 'complete'])->name('bookings.complete');
        Route::get('/reports/bookings/pdf', [ReportController::class, 'exportBookingsPdf'])->name('reports.bookings.pdf');
    });
});
```

### `resources/views/layouts/app.blade.php`
```blade
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Car Service Booking Management System')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
</head>
<body class="@yield('body_class')">
@if(auth()->check())
<nav class="navbar navbar-expand-lg app-navbar sticky-top">
    <div class="container-fluid px-lg-4">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
            <span class="brand-icon"><i class="bi bi-tools"></i></span>
            <span>Car Service Booking</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 nav-pills-soft">
                @if(auth()->user()->isAdmin())
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}" href="{{ route('admin.customers.index') }}"><i class="bi bi-people me-1"></i>Customers</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.vehicles.*') ? 'active' : '' }}" href="{{ route('admin.vehicles.index') }}"><i class="bi bi-car-front me-1"></i>Vehicles</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.service-packages.*') ? 'active' : '' }}" href="{{ route('admin.service-packages.index') }}"><i class="bi bi-box-seam me-1"></i>Packages</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}" href="{{ route('admin.bookings.index') }}"><i class="bi bi-calendar-check me-1"></i>Bookings</a></li>
                @else
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}" href="{{ route('customer.dashboard') }}"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('customer.vehicles.*') ? 'active' : '' }}" href="{{ route('customer.vehicles.index') }}"><i class="bi bi-car-front me-1"></i>My Vehicles</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('customer.packages.*') ? 'active' : '' }}" href="{{ route('customer.packages.index') }}"><i class="bi bi-box-seam me-1"></i>Packages</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('customer.bookings.*') ? 'active' : '' }}" href="{{ route('customer.bookings.index') }}"><i class="bi bi-calendar2-week me-1"></i>My Bookings</a></li>
                @endif
            </ul>
            <div class="d-flex flex-column flex-lg-row align-items-lg-center gap-2 gap-lg-3">
                <div class="user-chip">
                    <i class="bi bi-person-circle me-1"></i>
                    <span>{{ auth()->user()->name }}</span>
                    <span class="role-dot"></span>
                    <span>{{ ucfirst(auth()->user()->role) }}</span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-outline-dark btn-sm btn-rounded" type="submit"><i class="bi bi-box-arrow-right me-1"></i>Logout</button>
                </form>
            </div>
        </div>
    </div>
</nav>
@endif

<main class="@yield('main_class', 'container py-4')">
    @include('partials.flash')
    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/ui.js') }}"></script>
</body>
</html>
```

### `resources/views/welcome.blade.php`
```blade
@extends('layouts.app')

@section('title', 'Car Service Booking Management System')
@section('body_class', 'landing-page')
@section('main_class', 'landing-main')

@section('content')
<nav class="navbar navbar-expand-lg landing-nav">
    <div class="container py-2">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
            <span class="landing-brand-icon"><i class="bi bi-tools"></i></span>
            <span>AutoCare Booking</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#landingNavbar" aria-controls="landingNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="landingNavbar">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                <li class="nav-item"><a class="nav-link" href="#services">Services</a></li>
                <li class="nav-item"><a class="nav-link" href="#how-it-works">How It Works</a></li>
                <li class="nav-item"><a class="nav-link" href="#benefits">Benefits</a></li>
                <li class="nav-item"><a class="btn btn-outline-brand btn-rounded px-3" href="{{ route('login') }}">Login</a></li>
                <li class="nav-item"><a class="btn btn-brand btn-rounded px-3" href="{{ route('register') }}">Book Service</a></li>
            </ul>
        </div>
    </div>
</nav>

<section class="hero-section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="hero-badge mb-3"><i class="bi bi-stars"></i> Fast, simple and trackable car service booking</div>
                <h1 class="hero-title mb-4">Book your car service <span>without the waiting hassle.</span></h1>
                <p class="hero-copy mb-4">
                    Choose a service package, register your vehicle, pick your preferred date and track your booking status from pending to completed.
                </p>
                <div class="hero-actions mb-4">
                    <a href="{{ route('register') }}" class="btn btn-brand btn-lg btn-rounded px-4"><i class="bi bi-calendar2-check me-2"></i>Start Booking</a>
                    <a href="#services" class="btn btn-outline-dark btn-lg btn-rounded px-4"><i class="bi bi-grid-3x3-gap me-2"></i>View Services</a>
                </div>
                <div class="hero-trust">
                    <span><i class="bi bi-check-circle-fill text-success"></i> Online booking</span>
                    <span><i class="bi bi-check-circle-fill text-success"></i> Status updates</span>
                    <span><i class="bi bi-check-circle-fill text-success"></i> Customer dashboard</span>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-visual">
                    <div class="hero-car-card">
                        <div class="car-illustration">
                            <i class="bi bi-car-front-fill"></i>
                        </div>
                        <div class="floating-status-card"><i class="bi bi-check2-circle me-1"></i> Booking Approved</div>
                        <div class="floating-service-card">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="service-icon"><i class="bi bi-wrench-adjustable"></i></span>
                                <div>
                                    <div class="fw-bold">General Service</div>
                                    <div class="small text-muted">Estimated 60 minutes</div>
                                </div>
                            </div>
                            <div class="progress" role="progressbar" aria-label="Booking progress" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="height: 8px;">
                                <div class="progress-bar bg-warning" style="width: 75%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="services" class="section-padding">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-badge mb-3"><i class="bi bi-box-seam"></i> Service Selection</div>
            <h2 class="section-title display-6 mb-3">Choose the service that fits your car</h2>
            <p class="section-copy mx-auto mb-0">Click a service card to preview your choice. After logging in, the booking form keeps the same easy card-based selection.</p>
        </div>

        <div class="row g-4">
            @forelse($landingPackages as $package)
                <div class="col-lg-4 col-md-6">
                    <div class="landing-service-card" data-service-card data-service-group="landing" data-package-id="{{ $package->id }}" tabindex="0" role="button" aria-label="Select {{ $package->package_name }} service">
                        <span class="service-icon mb-3"><i class="bi bi-tools"></i></span>
                        <h3 class="h5 fw-bold mb-2">{{ $package->package_name }}</h3>
                        <p class="text-muted mb-3">{{ $package->description ?: 'A reliable service package prepared for your vehicle maintenance needs.' }}</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            <span class="duration-pill"><i class="bi bi-clock me-1"></i>{{ $package->estimated_duration }} min</span>
                            <span class="price-pill">RM {{ number_format($package->price, 2) }}</span>
                        </div>
                    </div>
                </div>
            @empty
                @foreach([
                    ['General Inspection', 'Basic vehicle health check, engine inspection and safety review.', '60 min', 'From RM 80'],
                    ['Oil & Filter Service', 'Engine oil replacement and filter change for smoother driving.', '45 min', 'From RM 120'],
                    ['Brake Service', 'Brake pad, fluid and system check for safer stopping power.', '75 min', 'From RM 150'],
                ] as [$name, $desc, $duration, $price])
                    <div class="col-lg-4 col-md-6">
                        <div class="landing-service-card" data-service-card data-service-group="landing" tabindex="0" role="button" aria-label="Select {{ $name }} service">
                            <span class="service-icon mb-3"><i class="bi bi-tools"></i></span>
                            <h3 class="h5 fw-bold mb-2">{{ $name }}</h3>
                            <p class="text-muted mb-3">{{ $desc }}</p>
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <span class="duration-pill"><i class="bi bi-clock me-1"></i>{{ $duration }}</span>
                                <span class="price-pill">{{ $price }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endforelse
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('register') }}" class="btn btn-brand btn-rounded px-4 py-2">Create Account to Book</a>
        </div>
    </div>
</section>

<section id="how-it-works" class="section-padding section-muted">
    <div class="container">
        <div class="row align-items-end mb-4">
            <div class="col-lg-7">
                <div class="section-badge mb-3"><i class="bi bi-signpost-2"></i> How It Works</div>
                <h2 class="section-title display-6 mb-0">A clear booking flow from start to finish</h2>
            </div>
            <div class="col-lg-5">
                <p class="section-copy mb-0">The process is designed for normal customers: no complicated forms, no confusing status, and no need to call just to check progress.</p>
            </div>
        </div>

        <div class="row g-4">
            @foreach([
                ['1', 'Create Account', 'Register as a customer and access your personal dashboard.'],
                ['2', 'Add Vehicle', 'Save your plate number, brand, model, year and color.'],
                ['3', 'Choose Service', 'Select the service package and preferred appointment time.'],
                ['4', 'Track Status', 'View whether your booking is pending, approved, completed or cancelled.'],
            ] as [$number, $title, $description])
                <div class="col-lg-3 col-md-6">
                    <div class="process-card">
                        <span class="process-number mb-3">{{ $number }}</span>
                        <h3 class="h5 fw-bold">{{ $title }}</h3>
                        <p class="text-muted mb-0">{{ $description }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section id="benefits" class="section-padding">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-badge mb-3"><i class="bi bi-heart-pulse"></i> Customer Benefits</div>
            <h2 class="section-title display-6 mb-3">Built for a smoother service experience</h2>
            <p class="section-copy mx-auto mb-0">The system helps customers book faster and helps the workshop manage appointments more clearly.</p>
        </div>

        <div class="row g-4">
            @foreach([
                ['bi-phone', 'Mobile Friendly', 'Book or check your appointment from desktop, tablet or phone.'],
                ['bi-shield-check', 'Secure Access', 'Your vehicles and bookings are protected inside your own account.'],
                ['bi-search', 'Easy Tracking', 'Quickly view booking details, service price and current status.'],
                ['bi-receipt', 'Clear Records', 'Every booking stores date, time, service type and total price.'],
                ['bi-lightning-charge', 'Fast Actions', 'Edit or cancel pending bookings before approval.'],
                ['bi-emoji-smile', 'Simple UI', 'Clear cards, buttons and labels make the flow easy to understand.'],
            ] as [$icon, $title, $description])
                <div class="col-lg-4 col-md-6">
                    <div class="benefit-card">
                        <span class="benefit-icon mb-3"><i class="bi {{ $icon }}"></i></span>
                        <h3 class="h5 fw-bold">{{ $title }}</h3>
                        <p class="text-muted mb-0">{{ $description }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="section-padding pt-0">
    <div class="container">
        <div class="cta-panel">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <h2 class="display-6 fw-bold mb-3">Ready to book your next car service?</h2>
                    <p class="mb-0 opacity-75">Create an account, add your vehicle and submit your preferred appointment in a few simple steps.</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="{{ route('register') }}" class="btn btn-light btn-lg btn-rounded px-4"><i class="bi bi-arrow-right-circle me-2"></i>Get Started</a>
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="border-top py-4">
    <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
        <div class="d-flex align-items-center gap-2">
            <span class="landing-brand-icon"><i class="bi bi-tools"></i></span>
            <div>
                <div class="fw-bold">Car Service Booking Management System</div>
                <div class="small text-muted">Online booking, vehicle management and service tracking.</div>
            </div>
        </div>
        <div class="d-flex gap-3 small">
            <a href="#services" class="footer-link">Services</a>
            <a href="#how-it-works" class="footer-link">Process</a>
            <a href="{{ route('login') }}" class="footer-link">Login</a>
        </div>
    </div>
</footer>
@endsection
```

### `resources/views/customer/bookings/_form.blade.php`
```blade
@php
    $selectedPackageId = old('service_package_id', $booking->service_package_id ?? request('service_package_id'));
@endphp

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Customer Name</label>
        <input type="text" class="form-control" value="{{ auth()->user()->name }}" readonly>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Vehicle</label>
        <select name="vehicle_id" class="form-select" required>
            <option value="">-- Select vehicle --</option>
            @foreach($vehicles as $vehicle)
                <option value="{{ $vehicle->id }}" @selected(old('vehicle_id', $booking->vehicle_id ?? '') == $vehicle->id)>
                    {{ $vehicle->plate_number }} - {{ $vehicle->brand }} {{ $vehicle->model }}
                </option>
            @endforeach
        </select>
        @if($vehicles->isEmpty())
            <div class="small text-danger mt-1">Please add a vehicle first.</div>
        @endif
    </div>

    <div class="col-12 mb-3">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-2 mb-3">
            <div>
                <label class="form-label mb-1">Service Package</label>
                <p class="text-muted small mb-0">Click a service card or use the dropdown below. The selected service will be submitted as the booking package.</p>
            </div>
            <span class="section-badge align-self-lg-start"><i class="bi bi-hand-index-thumb"></i> Select one service</span>
        </div>

        <div class="service-selection-grid mb-3">
            @forelse($packages as $package)
                <div class="service-option-card {{ (string) $selectedPackageId === (string) $package->id ? 'selected' : '' }}"
                     data-service-card
                     data-service-group="booking-form"
                     data-package-id="{{ $package->id }}"
                     data-target-select="service_package_id"
                     tabindex="0"
                     role="button"
                     aria-label="Select {{ $package->package_name }}">
                    <input type="radio" aria-hidden="true" @checked((string) $selectedPackageId === (string) $package->id)>
                    <span class="service-icon mb-3"><i class="bi bi-tools"></i></span>
                    <h3 class="h6 service-option-title mb-2">{{ $package->package_name }}</h3>
                    <p class="service-option-desc mb-0">{{ $package->description ?: 'Service package for your vehicle maintenance appointment.' }}</p>
                    <div class="service-option-meta">
                        <span class="duration-pill"><i class="bi bi-clock me-1"></i>{{ $package->estimated_duration }} min</span>
                        <span class="price-pill">RM {{ number_format($package->price, 2) }}</span>
                    </div>
                </div>
            @empty
                <div class="alert alert-info mb-0">No active service packages are available.</div>
            @endforelse
        </div>

        <label class="form-label small text-muted">Selected Service Package</label>
        <select id="service_package_id" name="service_package_id" class="form-select service-package-select" required>
            <option value="">-- Select service package --</option>
            @foreach($packages as $package)
                <option value="{{ $package->id }}" @selected((string) $selectedPackageId === (string) $package->id)>
                    {{ $package->package_name }} - RM {{ number_format($package->price, 2) }}
                </option>
            @endforeach
        </select>
        <div class="selected-service-helper alert alert-success mt-2 mb-0 py-2" data-selected-helper="service_package_id"></div>
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label">Preferred Date</label>
        <input type="date" name="preferred_date" class="form-control" value="{{ old('preferred_date', isset($booking) ? $booking->preferred_date->format('Y-m-d') : '') }}" required>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Preferred Time</label>
        <input type="time" name="preferred_time" class="form-control" value="{{ old('preferred_time', isset($booking) ? substr($booking->preferred_time,0,5) : '') }}" required>
    </div>
    <div class="col-12 mb-3">
        <label class="form-label">Additional Notes</label>
        <textarea name="additional_notes" class="form-control" rows="4" placeholder="Describe any issue or request">{{ old('additional_notes', $booking->additional_notes ?? '') }}</textarea>
    </div>
</div>
```

### `resources/views/customer/packages/index.blade.php`
```blade
@extends('layouts.app')
@section('title', 'Service Packages')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <span class="section-badge mb-2"><i class="bi bi-box-seam"></i> Available Services</span>
        <h1 class="page-title mb-1">Choose Your Service Package</h1>
        <p class="text-muted mb-0">Review the available car services and continue to booking when you are ready.</p>
    </div>
    <a href="{{ route('customer.bookings.create') }}" class="btn btn-brand btn-rounded px-4"><i class="bi bi-calendar2-plus me-1"></i>Book Now</a>
</div>

<div class="row g-4">
@forelse($packages as $package)
    <div class="col-lg-4 col-md-6">
        <div class="landing-service-card h-100" data-service-card data-service-group="customer-packages" data-package-id="{{ $package->id }}" tabindex="0" role="button" aria-label="Select {{ $package->package_name }}">
            <span class="service-icon mb-3"><i class="bi bi-tools"></i></span>
            <h2 class="h5 fw-bold mb-2">{{ $package->package_name }}</h2>
            <p class="text-muted">{{ $package->description ?: 'Service package for your vehicle maintenance appointment.' }}</p>
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
```

### `public/css/custom.css`
```css
:root {
    --brand: #f97316;
    --brand-dark: #ea580c;
    --brand-soft: #fff7ed;
    --dark: #111827;
    --muted: #6b7280;
    --line: #e5e7eb;
    --surface: #ffffff;
    --page: #f5f7fb;
    --success-soft: #ecfdf5;
    --shadow-sm: 0 10px 25px rgba(15, 23, 42, .06);
    --shadow-md: 0 18px 45px rgba(15, 23, 42, .12);
    --radius-lg: 1.25rem;
    --radius-md: 1rem;
}

* {
    scroll-behavior: smooth;
}

body {
    background: radial-gradient(circle at top left, rgba(249, 115, 22, .08), transparent 30%), var(--page);
    color: var(--dark);
    font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
}

a {
    transition: all .2s ease;
}

.app-navbar {
    background: rgba(255, 255, 255, .92);
    border-bottom: 1px solid rgba(229, 231, 235, .85);
    backdrop-filter: blur(16px);
    box-shadow: 0 8px 25px rgba(15, 23, 42, .06);
}

.navbar-brand {
    font-weight: 800;
    letter-spacing: -.02em;
    color: var(--dark);
}

.brand-icon,
.landing-brand-icon {
    width: 38px;
    height: 38px;
    display: inline-grid;
    place-items: center;
    border-radius: 12px;
    color: #fff;
    background: linear-gradient(135deg, var(--brand), #fb923c);
    box-shadow: 0 12px 24px rgba(249, 115, 22, .25);
}

.nav-pills-soft .nav-link {
    color: #374151;
    border-radius: 999px;
    padding: .55rem .9rem;
    font-weight: 650;
}

.nav-pills-soft .nav-link:hover,
.nav-pills-soft .nav-link.active {
    color: var(--brand-dark);
    background: var(--brand-soft);
}

.user-chip {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .45rem .75rem;
    border: 1px solid var(--line);
    border-radius: 999px;
    background: #fff;
    font-size: .875rem;
    font-weight: 650;
    color: #374151;
}

.role-dot {
    width: 5px;
    height: 5px;
    border-radius: 999px;
    background: var(--brand);
}

.card {
    border: 0;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.stat-card {
    min-height: 120px;
    overflow: hidden;
    position: relative;
}

.stat-card::after {
    content: "";
    position: absolute;
    width: 90px;
    height: 90px;
    right: -30px;
    bottom: -30px;
    border-radius: 999px;
    background: rgba(249, 115, 22, .09);
}

.page-title {
    font-weight: 850;
    color: var(--dark);
    letter-spacing: -.035em;
}

.badge-status {
    font-size: .8rem;
}

.table {
    margin-bottom: 0;
}

.table td,
.table th {
    vertical-align: middle;
}

.table thead th {
    background: #f9fafb;
    color: #4b5563;
    font-size: .8rem;
    text-transform: uppercase;
    letter-spacing: .04em;
}

.auth-shell {
    min-height: calc(100vh - 3rem);
    display: flex;
    align-items: center;
}

.auth-shell .card {
    border: 1px solid rgba(229, 231, 235, .7);
}

.btn-rounded {
    border-radius: 999px;
}

.btn-dark {
    background: var(--dark);
    border-color: var(--dark);
    box-shadow: 0 12px 22px rgba(17, 24, 39, .12);
}

.btn-dark:hover {
    background: #000;
    border-color: #000;
    transform: translateY(-1px);
}

.btn-brand {
    background: linear-gradient(135deg, var(--brand), #fb923c);
    border: 0;
    color: #fff;
    box-shadow: 0 16px 30px rgba(249, 115, 22, .28);
}

.btn-brand:hover,
.btn-brand:focus {
    color: #fff;
    background: linear-gradient(135deg, var(--brand-dark), var(--brand));
    transform: translateY(-2px);
}

.btn-outline-brand {
    border-color: rgba(249, 115, 22, .45);
    color: var(--brand-dark);
    background: #fff;
}

.btn-outline-brand:hover {
    background: var(--brand-soft);
    border-color: var(--brand);
    color: var(--brand-dark);
    transform: translateY(-1px);
}

.form-control,
.form-select {
    border-radius: .85rem;
    border-color: #dbe1ea;
    padding: .72rem .9rem;
}

.form-control:focus,
.form-select:focus {
    border-color: rgba(249, 115, 22, .75);
    box-shadow: 0 0 0 .25rem rgba(249, 115, 22, .12);
}

.alert {
    border: 0;
    border-radius: 1rem;
    box-shadow: var(--shadow-sm);
}

/* Landing page */
.landing-main {
    padding: 0;
}

.landing-page {
    background: #fff;
    overflow-x: hidden;
}

.landing-nav {
    position: sticky;
    top: 0;
    z-index: 1020;
    background: rgba(255, 255, 255, .9);
    backdrop-filter: blur(18px);
    border-bottom: 1px solid rgba(229, 231, 235, .8);
}

.landing-nav .nav-link {
    color: #374151;
    font-weight: 650;
}

.landing-nav .nav-link:hover {
    color: var(--brand-dark);
}

.hero-section {
    position: relative;
    padding: 6rem 0 4rem;
    background:
        radial-gradient(circle at 10% 20%, rgba(249, 115, 22, .16), transparent 28%),
        radial-gradient(circle at 85% 15%, rgba(17, 24, 39, .10), transparent 28%),
        linear-gradient(180deg, #fff, #f8fafc);
}

.hero-badge,
.section-badge {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    padding: .45rem .8rem;
    border-radius: 999px;
    background: var(--brand-soft);
    color: var(--brand-dark);
    font-weight: 750;
    font-size: .875rem;
}

.hero-title {
    font-size: clamp(2.4rem, 5vw, 4.8rem);
    line-height: .98;
    letter-spacing: -.06em;
    font-weight: 900;
    color: var(--dark);
}

.hero-title span {
    color: var(--brand-dark);
}

.hero-copy {
    max-width: 640px;
    color: #4b5563;
    font-size: 1.1rem;
    line-height: 1.75;
}

.hero-actions {
    display: flex;
    flex-wrap: wrap;
    gap: .85rem;
}

.hero-trust {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    color: #4b5563;
    font-weight: 650;
}

.hero-trust span {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
}

.hero-visual {
    position: relative;
    max-width: 520px;
    margin-inline: auto;
}

.hero-car-card {
    position: relative;
    padding: 1.5rem;
    border: 1px solid rgba(229, 231, 235, .8);
    border-radius: 2rem;
    background: rgba(255, 255, 255, .82);
    box-shadow: var(--shadow-md);
    backdrop-filter: blur(16px);
}

.car-illustration {
    min-height: 250px;
    display: grid;
    place-items: center;
    border-radius: 1.5rem;
    background:
        linear-gradient(135deg, rgba(249, 115, 22, .13), rgba(17, 24, 39, .05)),
        #f8fafc;
}

.car-illustration i {
    font-size: 8rem;
    color: var(--dark);
    filter: drop-shadow(0 16px 18px rgba(15, 23, 42, .12));
}

.floating-service-card {
    position: absolute;
    left: -18px;
    bottom: 30px;
    width: min(250px, 70%);
    padding: 1rem;
    border-radius: 1.25rem;
    background: #fff;
    box-shadow: var(--shadow-md);
    border: 1px solid var(--line);
    animation: floatCard 4s ease-in-out infinite;
}

.floating-status-card {
    position: absolute;
    right: -12px;
    top: 28px;
    padding: .85rem 1rem;
    border-radius: 999px;
    background: #111827;
    color: #fff;
    font-weight: 750;
    box-shadow: var(--shadow-md);
    animation: floatCard 4.5s ease-in-out infinite;
}

@keyframes floatCard {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

.section-padding {
    padding: 5rem 0;
}

.section-muted {
    background: #f8fafc;
}

.section-title {
    font-weight: 900;
    letter-spacing: -.04em;
    color: var(--dark);
}

.section-copy {
    color: var(--muted);
    max-width: 680px;
    line-height: 1.7;
}

.feature-card,
.process-card,
.benefit-card,
.landing-service-card {
    height: 100%;
    padding: 1.35rem;
    border: 1px solid rgba(229, 231, 235, .85);
    border-radius: var(--radius-lg);
    background: #fff;
    box-shadow: var(--shadow-sm);
    transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
}

.feature-card:hover,
.process-card:hover,
.benefit-card:hover,
.landing-service-card:hover {
    transform: translateY(-6px);
    box-shadow: var(--shadow-md);
    border-color: rgba(249, 115, 22, .35);
}

.feature-icon,
.process-number,
.benefit-icon,
.service-icon {
    width: 48px;
    height: 48px;
    display: inline-grid;
    place-items: center;
    border-radius: 16px;
    background: var(--brand-soft);
    color: var(--brand-dark);
    font-size: 1.35rem;
    font-weight: 850;
}

.process-number {
    color: #fff;
    background: linear-gradient(135deg, var(--brand), #fb923c);
}

.landing-service-card {
    cursor: pointer;
    position: relative;
}

.landing-service-card.selected,
.service-option-card.selected {
    border-color: var(--brand);
    background: linear-gradient(180deg, #fff, var(--brand-soft));
    box-shadow: 0 20px 50px rgba(249, 115, 22, .18);
}

.landing-service-card.selected::after,
.service-option-card.selected::after {
    content: "\F26A";
    font-family: "bootstrap-icons";
    position: absolute;
    top: 1rem;
    right: 1rem;
    width: 30px;
    height: 30px;
    display: grid;
    place-items: center;
    border-radius: 999px;
    color: #fff;
    background: var(--brand);
}

.cta-panel {
    padding: clamp(2rem, 5vw, 4rem);
    border-radius: 2rem;
    color: #fff;
    background:
        radial-gradient(circle at top right, rgba(249, 115, 22, .55), transparent 32%),
        linear-gradient(135deg, #111827, #1f2937);
    box-shadow: var(--shadow-md);
}

.footer-link {
    color: #6b7280;
    text-decoration: none;
}

.footer-link:hover {
    color: var(--brand-dark);
}

/* Booking service selection */
.service-selection-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 1rem;
}

.service-option-card {
    position: relative;
    display: block;
    min-height: 180px;
    padding: 1.15rem;
    border: 1px solid var(--line);
    border-radius: 1.25rem;
    background: #fff;
    cursor: pointer;
    transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease, background .2s ease;
}

.service-option-card:hover {
    transform: translateY(-4px);
    border-color: rgba(249, 115, 22, .45);
    box-shadow: var(--shadow-sm);
}

.service-option-card:active {
    transform: scale(.98);
}

.service-option-card input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.service-option-title {
    padding-right: 2rem;
    font-weight: 850;
}

.service-option-desc {
    color: var(--muted);
    font-size: .9rem;
    min-height: 42px;
}

.service-option-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: .75rem;
    margin-top: 1rem;
}

.price-pill {
    padding: .45rem .7rem;
    border-radius: 999px;
    background: #111827;
    color: #fff;
    font-weight: 800;
    white-space: nowrap;
}

.duration-pill {
    color: #6b7280;
    font-size: .875rem;
    font-weight: 650;
}

.selected-service-helper {
    display: none;
}

.selected-service-helper.show {
    display: block;
}

@media (max-width: 991.98px) {
    .hero-section {
        padding: 4rem 0 3rem;
    }

    .hero-visual {
        margin-top: 2rem;
    }

    .service-selection-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 767.98px) {
    .container,
    .container-fluid {
        padding-left: 1rem;
        padding-right: 1rem;
    }

    .section-padding {
        padding: 3.25rem 0;
    }

    .hero-actions .btn {
        width: 100%;
    }

    .floating-service-card,
    .floating-status-card {
        position: static;
        width: 100%;
        margin-top: .75rem;
        animation: none;
    }

    .service-selection-grid {
        grid-template-columns: 1fr;
    }

    .d-flex.justify-content-between.align-items-center,
    .d-flex.flex-wrap.justify-content-between.align-items-center {
        gap: 1rem;
    }
}
```

### `public/js/ui.js`
```javascript
document.addEventListener('DOMContentLoaded', () => {
    const selectableCards = document.querySelectorAll('[data-service-card]');

    selectableCards.forEach((card) => {
        card.addEventListener('click', () => {
            const group = card.dataset.serviceGroup || 'default';
            const groupCards = document.querySelectorAll(`[data-service-card][data-service-group="${group}"]`);

            groupCards.forEach((item) => item.classList.remove('selected'));
            card.classList.add('selected');

            const packageId = card.dataset.packageId;
            const targetSelectId = card.dataset.targetSelect;
            const targetSelect = targetSelectId ? document.getElementById(targetSelectId) : null;

            if (targetSelect && packageId) {
                targetSelect.value = packageId;
                targetSelect.dispatchEvent(new Event('change', { bubbles: true }));
            }

            const radio = card.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
            }
        });

        card.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                card.click();
            }
        });
    });

    document.querySelectorAll('.service-package-select').forEach((select) => {
        const updateCards = () => {
            const packageId = select.value;
            const cards = document.querySelectorAll(`[data-target-select="${select.id}"]`);
            const helper = document.querySelector(`[data-selected-helper="${select.id}"]`);
            const selectedOption = select.options[select.selectedIndex];

            cards.forEach((card) => {
                card.classList.toggle('selected', card.dataset.packageId === packageId);
            });

            if (helper) {
                if (packageId && selectedOption) {
                    helper.classList.add('show');
                    helper.innerHTML = `<i class="bi bi-check-circle me-1"></i>Selected service: <strong>${selectedOption.text}</strong>`;
                } else {
                    helper.classList.remove('show');
                    helper.textContent = '';
                }
            }
        };

        select.addEventListener('change', updateCards);
        updateCards();
    });
});
```


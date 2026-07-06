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

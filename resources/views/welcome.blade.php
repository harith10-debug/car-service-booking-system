@extends('layouts.app')

@section('title', 'DH Motorsport')
@section('body_class', 'landing-page dh-motorsport')
@section('main_class', 'landing-main')

@section('content')
<nav class="navbar navbar-expand-lg landing-nav">
    <div class="container py-2">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
            <span class="landing-brand-icon"><i class="bi bi-speedometer2"></i></span>
            <span>DH Motorsport</span>
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
    <div class="hero-racing-stripe" aria-hidden="true"></div>
    <div class="container position-relative">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="hero-badge mb-3"><i class="bi bi-lightning-charge-fill"></i> Motorsport-inspired service booking</div>
                <h1 class="hero-title mb-4">Book your service with <span>pit-stop confidence.</span></h1>
                <p class="hero-copy mb-4">
                    DH Motorsport helps customers register vehicles, choose a service package, find nearby workshops, pay approved bookings and download official receipts.
                </p>
                <div class="hero-actions mb-4">
                    <a href="{{ route('register') }}" class="btn btn-brand btn-lg btn-rounded px-4"><i class="bi bi-calendar2-check me-2"></i>Start Booking</a>
                    <a href="#services" class="btn btn-outline-brand btn-lg btn-rounded px-4"><i class="bi bi-grid-3x3-gap me-2"></i>View Services</a>
                </div>
                <div class="hero-trust">
                    <span><i class="bi bi-check-circle-fill"></i> Online booking</span>
                    <span><i class="bi bi-check-circle-fill"></i> Payment receipt</span>
                    <span><i class="bi bi-check-circle-fill"></i> Workshop finder</span>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-visual">
                    <div class="hero-car-card">
                        <div class="hero-card-topline">
                            <span>DH Motorsport</span>
                            <span class="race-dot"></span>
                        </div>
                        <div class="car-illustration">
                            <i class="bi bi-car-front-fill"></i>
                        </div>
                        <div class="floating-status-card"><i class="bi bi-flag-fill me-1"></i> Booking Approved</div>
                        <div class="floating-service-card">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="service-icon"><i class="bi bi-wrench-adjustable"></i></span>
                                <div>
                                    <div class="fw-bold">Performance Service</div>
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
            <h2 class="section-title display-6 mb-3">Choose your DH Motorsport service</h2>
            <p class="section-copy mx-auto mb-0">Click a service card to preview your choice. After logging in, the booking form keeps the same simple card-based selection.</p>
        </div>

        <div class="row g-4">
            @forelse($landingPackages as $package)
                <div class="col-lg-4 col-md-6">
                    <div class="landing-service-card" data-service-card data-service-group="landing" data-package-id="{{ $package->id }}" tabindex="0" role="button" aria-label="Select {{ $package->package_name }} service">
                        <span class="service-icon mb-3"><i class="bi bi-tools"></i></span>
                        <h3 class="h5 fw-bold mb-2">{{ $package->package_name }}</h3>
                        <p class="text-muted mb-3">{{ $package->description ?: 'A DH Motorsport service package prepared for your vehicle appointment.' }}</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            <span class="duration-pill"><i class="bi bi-clock me-1"></i>{{ $package->estimated_duration }} min</span>
                            <span class="price-pill">RM {{ number_format($package->price, 2) }}</span>
                        </div>
                    </div>
                </div>
            @empty
                @foreach([
                    ['General Inspection', 'Vehicle health check, engine inspection and safety review.', '60 min', 'From RM 80'],
                    ['Oil & Filter Service', 'Engine oil replacement and filter change for smoother driving.', '45 min', 'From RM 120'],
                    ['Brake Service', 'Brake pad, fluid and system check for stronger stopping power.', '75 min', 'From RM 150'],
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
                <h2 class="section-title display-6 mb-0">A clean booking flow from garage to finish line</h2>
            </div>
            <div class="col-lg-5">
                <p class="section-copy mb-0">The process is designed for normal customers: clear steps, obvious service choices and simple booking status updates.</p>
            </div>
        </div>

        <div class="row g-4">
            @foreach([
                ['1', 'Create Account', 'Register as a customer and access your personal dashboard.'],
                ['2', 'Add Vehicle', 'Save your plate number, brand, model, year and color.'],
                ['3', 'Choose Service', 'Select the service package, preferred workshop and appointment time.'],
                ['4', 'Pay & Download Receipt', 'Pay after admin approval and keep the PDF receipt for your record.'],
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
            <div class="section-badge mb-3"><i class="bi bi-trophy"></i> Customer Benefits</div>
            <h2 class="section-title display-6 mb-3">Built for a faster service experience</h2>
            <p class="section-copy mx-auto mb-0">DH Motorsport combines a bold motorsport look with a simple system for customers and admins to manage service appointments clearly.</p>
        </div>

        <div class="row g-4">
            @foreach([
                ['bi-phone', 'Mobile Friendly', 'Book or check your appointment from desktop, tablet or phone.'],
                ['bi-shield-check', 'Secure Access', 'Your vehicles and bookings are protected inside your own account.'],
                ['bi-search', 'Easy Tracking', 'Quickly view booking details, service price and current status.'],
                ['bi-geo-alt', 'Nearby Workshop', 'Find DH Motorsport branches around Shah Alam and nearby areas.'],
                ['bi-receipt', 'PDF Receipt', 'Every paid booking can generate an official receipt for customer records.'],
                ['bi-gem', 'Subscription Benefits', 'Customers can subscribe for discounts while admin gains recurring sales insight.'],
                ['bi-flag', 'Sporty Experience', 'Red, yellow and black visuals make the interface feel energetic.'],
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
                    <h2 class="display-6 fw-bold mb-3">Ready for your next DH Motorsport service?</h2>
                    <p class="mb-0 opacity-75">Create an account, add your vehicle and submit your preferred appointment in a few simple steps.</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="{{ route('register') }}" class="btn btn-light btn-lg btn-rounded px-4"><i class="bi bi-arrow-right-circle me-2"></i>Get Started</a>
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="landing-footer py-4">
    <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
        <div class="d-flex align-items-center gap-2">
            <span class="landing-brand-icon"><i class="bi bi-speedometer2"></i></span>
            <div>
                <div class="fw-bold">DH Motorsport</div>
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

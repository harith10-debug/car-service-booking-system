<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'DH Motorsport')</title>

    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    {{-- DH Motorsport Custom CSS --}}
    <link href="/css/custom.css?v=dh-motorsport-2" rel="stylesheet">
</head>

<body class="@yield('body_class')">

@if(auth()->check())
<nav class="navbar navbar-expand-lg app-navbar sticky-top">
    <div class="container-fluid px-lg-4">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
            <span class="brand-icon">
                <i class="bi bi-speedometer2"></i>
            </span>
            <span>DH Motorsport</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
                aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 nav-pills-soft">
                @if(auth()->user()->isAdmin())
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                           href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-speedometer2 me-1"></i>Dashboard
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}"
                           href="{{ route('admin.customers.index') }}">
                            <i class="bi bi-people me-1"></i>Customers
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.vehicles.*') ? 'active' : '' }}"
                           href="{{ route('admin.vehicles.index') }}">
                            <i class="bi bi-car-front me-1"></i>Vehicles
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.service-packages.*') ? 'active' : '' }}"
                           href="{{ route('admin.service-packages.index') }}">
                            <i class="bi bi-box-seam me-1"></i>Packages
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}"
                           href="{{ route('admin.bookings.index') }}">
                            <i class="bi bi-calendar-check me-1"></i>Bookings
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}"
                           href="{{ route('customer.dashboard') }}">
                            <i class="bi bi-speedometer2 me-1"></i>Dashboard
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('customer.vehicles.*') ? 'active' : '' }}"
                           href="{{ route('customer.vehicles.index') }}">
                            <i class="bi bi-car-front me-1"></i>My Vehicles
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('customer.packages.*') ? 'active' : '' }}"
                           href="{{ route('customer.packages.index') }}">
                            <i class="bi bi-box-seam me-1"></i>Packages
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('customer.bookings.*') ? 'active' : '' }}"
                           href="{{ route('customer.bookings.index') }}">
                            <i class="bi bi-calendar2-week me-1"></i>My Bookings
                        </a>
                    </li>
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
                    <button class="btn btn-outline-brand btn-sm btn-rounded" type="submit">
                        <i class="bi bi-box-arrow-right me-1"></i>Logout
                    </button>
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

{{-- Bootstrap JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

{{-- DH Motorsport Custom JS --}}
<script src="/js/ui.js?v=dh-motorsport-2"></script>

</body>
</html>
@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="row justify-content-center auth-shell">
    <div class="col-lg-5 col-md-7">
        <div class="card p-4">
            <div class="text-center mb-4">
                <div class="display-5"><i class="bi bi-tools"></i></div>
                <h1 class="h3 fw-bold">Car Service Booking</h1>
                <p class="text-muted mb-0">Login to manage your service bookings.</p>
            </div>
            <form method="POST" action="{{ route('login.post') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3 form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>
                <button class="btn btn-dark w-100" type="submit">Login</button>
            </form>
            <hr>
            <p class="text-center mb-0">No account? <a href="{{ route('register') }}">Register as customer</a></p>
            <div class="small text-muted mt-3">
                Admin: admin@example.com / password<br>
                Customer: customer@example.com / password
            </div>
        </div>
    </div>
</div>
@endsection

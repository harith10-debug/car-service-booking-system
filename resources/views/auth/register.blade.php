@extends('layouts.app')

@section('title', 'Register | DH Motorsport')

@section('content')
<div class="row justify-content-center auth-shell">
    <div class="col-lg-6 col-md-8">
        <div class="card p-4">
            <div class="text-center mb-4">
                <h1 class="h3 fw-bold">Create DH Motorsport Account</h1>
                <p class="text-muted mb-0">Register to add your vehicle and book a DH Motorsport service slot.</p>
            </div>
            <form method="POST" action="{{ route('register.post') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                </div>
                <button class="btn btn-brand w-100" type="submit">Register</button>
            </form>
            <hr>
            <p class="text-center mb-0">Already registered? <a href="{{ route('login') }}">Login</a></p>
        </div>
    </div>
</div>
@endsection

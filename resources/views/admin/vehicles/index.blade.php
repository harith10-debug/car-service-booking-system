@extends('layouts.app')
@section('title', 'Manage Vehicles')
@section('content')
<h1 class="page-title mb-3">Manage Vehicles</h1>
<div class="card p-3 mb-3">
    <form class="row g-2" method="GET">
        <div class="col-md-10"><input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search by customer, plate, brand or model"></div>
        <div class="col-md-2 d-grid"><button class="btn btn-dark">Search</button></div>
    </form>
</div>
<div class="card p-3">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>Customer</th><th>Plate</th><th>Brand</th><th>Model</th><th>Year</th><th>Color</th></tr></thead>
            <tbody>
            @forelse($vehicles as $vehicle)
                <tr>
                    <td>{{ $vehicle->user->name }}</td>
                    <td class="fw-bold">{{ $vehicle->plate_number }}</td>
                    <td>{{ $vehicle->brand }}</td>
                    <td>{{ $vehicle->model }}</td>
                    <td>{{ $vehicle->year }}</td>
                    <td>{{ $vehicle->color }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted">No vehicles found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $vehicles->links() }}
</div>
@endsection

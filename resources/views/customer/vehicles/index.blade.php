@extends('layouts.app')
@section('title', 'My Vehicles')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title">My Vehicles</h1>
    <a href="{{ route('customer.vehicles.create') }}" class="btn btn-dark btn-rounded"><i class="bi bi-plus-circle me-1"></i>Add Vehicle</a>
</div>
<div class="card p-3">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>Plate</th><th>Brand</th><th>Model</th><th>Year</th><th>Color</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
            @forelse($vehicles as $vehicle)
                <tr>
                    <td class="fw-bold">{{ $vehicle->plate_number }}</td>
                    <td>{{ $vehicle->brand }}</td>
                    <td>{{ $vehicle->model }}</td>
                    <td>{{ $vehicle->year }}</td>
                    <td>{{ $vehicle->color }}</td>
                    <td class="text-end">
                        <a href="{{ route('customer.vehicles.edit', $vehicle) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form method="POST" action="{{ route('customer.vehicles.destroy', $vehicle) }}" class="d-inline" onsubmit="return confirm('Delete this vehicle?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted">No vehicles registered.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $vehicles->links() }}
</div>
@endsection

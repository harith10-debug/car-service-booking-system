@extends('layouts.app')
@section('title', 'Manage Customers')
@section('content')
<h1 class="page-title mb-3">Manage Customers</h1>
<div class="card p-3 mb-3">
    <form class="row g-2" method="GET">
        <div class="col-md-10"><input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search by name, email or phone"></div>
        <div class="col-md-2 d-grid"><button class="btn btn-dark">Search</button></div>
    </form>
</div>
<div class="card p-3">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Vehicles</th><th>Bookings</th><th class="text-end">Action</th></tr></thead>
            <tbody>
            @forelse($customers as $customer)
                <tr>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->email }}</td>
                    <td>{{ $customer->phone ?: '-' }}</td>
                    <td>{{ $customer->vehicles_count }}</td>
                    <td>{{ $customer->bookings_count }}</td>
                    <td class="text-end">
                        <form method="POST" action="{{ route('admin.customers.destroy', $customer) }}" onsubmit="return confirm('Delete this customer?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted">No customers found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $customers->links() }}
</div>
@endsection

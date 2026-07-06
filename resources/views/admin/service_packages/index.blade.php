@extends('layouts.app')
@section('title', 'Manage Service Packages')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title">Manage Service Packages</h1>
    <a href="{{ route('admin.service-packages.create') }}" class="btn btn-dark btn-rounded"><i class="bi bi-plus-circle me-1"></i>Add Package</a>
</div>
<div class="card p-3 mb-3">
    <form class="row g-2" method="GET">
        <div class="col-md-7"><input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search package name"></div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All Status</option>
                <option value="Active" @selected(request('status') === 'Active')>Active</option>
                <option value="Inactive" @selected(request('status') === 'Inactive')>Inactive</option>
            </select>
        </div>
        <div class="col-md-2 d-grid"><button class="btn btn-dark">Filter</button></div>
    </form>
</div>
<div class="card p-3">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>Package</th><th>Duration</th><th>Price</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
            @forelse($packages as $package)
                <tr>
                    <td><div class="fw-bold">{{ $package->package_name }}</div><div class="small text-muted">{{ Str::limit($package->description, 80) }}</div></td>
                    <td>{{ $package->estimated_duration }} min</td>
                    <td>RM {{ number_format($package->price, 2) }}</td>
                    <td><span class="badge {{ $package->status === 'Active' ? 'bg-success' : 'bg-secondary' }}">{{ $package->status }}</span></td>
                    <td class="text-end">
                        <a href="{{ route('admin.service-packages.edit', $package) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form method="POST" action="{{ route('admin.service-packages.destroy', $package) }}" class="d-inline" onsubmit="return confirm('Delete this package?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted">No packages found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $packages->links() }}
</div>
@endsection

@extends('layouts.app')
@section('title', 'Manage Workshops')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <div>
        <h1 class="page-title mb-1">Manage Workshops</h1>
        <p class="text-muted mb-0">Maintain workshop locations for customer nearby search and booking assignment.</p>
    </div>
    <a href="{{ route('admin.workshops.create') }}" class="btn btn-dark btn-rounded"><i class="bi bi-plus-circle me-1"></i>Add Workshop</a>
</div>
<div class="card p-3 mb-3">
    <form class="row g-2" method="GET">
        <div class="col-md-7"><input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search name, city or service"></div>
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
            <thead><tr><th>Workshop</th><th>City</th><th>Services</th><th>Bookings</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
            @forelse($workshops as $workshop)
                <tr>
                    <td><div class="fw-bold">{{ $workshop->name }}</div><div class="small text-muted">{{ $workshop->address }}</div></td>
                    <td>{{ $workshop->city }}</td>
                    <td>{{ Str::limit($workshop->services, 60) }}</td>
                    <td>{{ $workshop->bookings_count }}</td>
                    <td><span class="badge {{ $workshop->status === 'Active' ? 'bg-success' : 'bg-secondary' }}">{{ $workshop->status }}</span></td>
                    <td class="text-end">
                        <a href="{{ route('admin.workshops.edit', $workshop) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form method="POST" action="{{ route('admin.workshops.destroy', $workshop) }}" class="d-inline" onsubmit="return confirm('Delete this workshop?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted">No workshops found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $workshops->links() }}
</div>
@endsection

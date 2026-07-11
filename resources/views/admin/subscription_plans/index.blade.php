@extends('layouts.app')
@section('title', 'Manage Subscription Plans')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <div>
        <h1 class="page-title mb-1">Manage Subscription Plans</h1>
        <p class="text-muted mb-0">Create customer membership plans and define benefits, discounts and priority.</p>
    </div>
    <a href="{{ route('admin.subscription-plans.create') }}" class="btn btn-dark btn-rounded"><i class="bi bi-plus-circle me-1"></i>Add Plan</a>
</div>
<div class="card p-3 mb-3">
    <form class="row g-2" method="GET">
        <div class="col-md-7"><input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search plan name"></div>
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
            <thead><tr><th>Plan</th><th>Price</th><th>Discount</th><th>Priority</th><th>Subscribers</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
            @forelse($plans as $plan)
                <tr>
                    <td><div class="fw-bold">{{ $plan->plan_name }}</div><div class="small text-muted">{{ Str::limit($plan->description, 80) }}</div></td>
                    <td>RM {{ number_format($plan->monthly_price, 2) }} / {{ $plan->billing_cycle }}</td>
                    <td>{{ number_format($plan->discount_percentage, 0) }}%</td>
                    <td>{{ $plan->priority_level }}</td>
                    <td>{{ $plan->subscriptions_count }}</td>
                    <td><span class="badge {{ $plan->status === 'Active' ? 'bg-success' : 'bg-secondary' }}">{{ $plan->status }}</span></td>
                    <td class="text-end">
                        <a href="{{ route('admin.subscription-plans.edit', $plan) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form method="POST" action="{{ route('admin.subscription-plans.destroy', $plan) }}" class="d-inline" onsubmit="return confirm('Delete this subscription plan?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted">No subscription plans found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $plans->links() }}
</div>
@endsection

@extends('layouts.app')
@section('title', 'Subscription Monitor')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <div>
        <h1 class="page-title mb-1">Subscription Monitor</h1>
        <p class="text-muted mb-0">Track active subscribers, plan revenue and membership benefits.</p>
    </div>
    <a href="{{ route('admin.subscription-plans.index') }}" class="btn btn-dark btn-rounded"><i class="bi bi-gem me-1"></i>Manage Plans</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card stat-card p-3"><div class="text-muted">Active Subscribers</div><div class="display-6 fw-bold">{{ $activeSubscriptions }}</div></div></div>
    <div class="col-md-3"><div class="card stat-card p-3"><div class="text-muted">Subscription Revenue</div><div class="display-6 fw-bold">RM {{ number_format($subscriptionRevenue, 2) }}</div></div></div>
    <div class="col-md-3"><div class="card stat-card p-3"><div class="text-muted">Active Plans</div><div class="display-6 fw-bold">{{ $activePlans }}</div></div></div>
    <div class="col-md-3"><div class="card stat-card p-3"><div class="text-muted">Total Plans</div><div class="display-6 fw-bold">{{ $totalPlans }}</div></div></div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6"><div class="card p-4 h-100"><h2 class="h5 fw-bold mb-3">Admin Value</h2><ul class="benefit-list mb-0"><li><i class="bi bi-cash-stack"></i>Predictable membership revenue.</li><li><i class="bi bi-people"></i>Identify loyal customers quickly.</li><li><i class="bi bi-graph-up"></i>Use subscriber count as a sales KPI.</li></ul></div></div>
    <div class="col-lg-6"><div class="card p-4 h-100"><h2 class="h5 fw-bold mb-3">Customer Value</h2><ul class="benefit-list mb-0"><li><i class="bi bi-percent"></i>Discount during payment.</li><li><i class="bi bi-lightning"></i>Priority booking handling.</li><li><i class="bi bi-tools"></i>Extra service benefits by plan.</li></ul></div></div>
</div>

<div class="card p-3 mb-3">
    <form class="row g-2" method="GET">
        <div class="col-md-8"><input name="customer" class="form-control" value="{{ request('customer') }}" placeholder="Customer name"></div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">All Status</option>
                @foreach($statuses as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 d-grid"><button class="btn btn-dark">Filter</button></div>
    </form>
</div>

<div class="card p-3">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>Reference</th><th>Customer</th><th>Plan</th><th>Period</th><th>Paid</th><th>Method</th><th>Status</th></tr></thead>
            <tbody>
            @forelse($subscriptions as $subscription)
                <tr>
                    <td class="fw-bold">{{ $subscription->subscription_reference }}</td>
                    <td>{{ $subscription->user->name }}</td>
                    <td>{{ $subscription->plan->plan_name }}</td>
                    <td>{{ $subscription->starts_at->format('d M Y') }} - {{ $subscription->ends_at->format('d M Y') }}</td>
                    <td>RM {{ number_format($subscription->amount_paid, 2) }}</td>
                    <td>{{ $subscription->payment_method }}</td>
                    <td><span class="badge {{ $subscription->status === 'Active' ? 'bg-success' : 'bg-secondary' }}">{{ $subscription->status }}</span></td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted">No subscriptions found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $subscriptions->links() }}
</div>
@endsection

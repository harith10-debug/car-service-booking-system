@extends('layouts.app')
@section('title', 'Subscription Plans')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <span class="section-badge mb-2"><i class="bi bi-gem"></i> Subscription Plan</span>
        <h1 class="page-title mb-1">DH Motorsport Membership</h1>
        <p class="text-muted mb-0">Subscribe to unlock discounts, priority booking and extra service benefits.</p>
    </div>
    @if($activeSubscription)
        <span class="badge bg-success fs-6">Active: {{ $activeSubscription->plan->plan_name }}</span>
    @endif
</div>

@if($activeSubscription)
    <div class="alert alert-success d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div><i class="bi bi-stars me-1"></i>Your active subscription gives {{ number_format($activeSubscription->plan->discount_percentage, 0) }}% service payment discount until {{ $activeSubscription->ends_at->format('d M Y') }}.</div>
        <form method="POST" action="{{ route('customer.subscriptions.cancel', $activeSubscription) }}" onsubmit="return confirm('Cancel current subscription?')">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-outline-danger">Cancel Subscription</button>
        </form>
    </div>
@endif

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card p-4 h-100">
            <h2 class="h5 fw-bold mb-3">Benefits for Customers</h2>
            <ul class="benefit-list mb-0">
                <li><i class="bi bi-percent"></i> Discount on every eligible service payment.</li>
                <li><i class="bi bi-lightning-charge"></i> Priority booking queue for faster admin approval.</li>
                <li><i class="bi bi-receipt"></i> Receipt history in one place for easier record keeping.</li>
                <li><i class="bi bi-geo-alt"></i> Workshop recommendation before booking.</li>
                <li><i class="bi bi-shield-check"></i> Free basic diagnostic note during service check-in.</li>
            </ul>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card p-4 h-100">
            <h2 class="h5 fw-bold mb-3">Benefits for Admin</h2>
            <ul class="benefit-list mb-0">
                <li><i class="bi bi-graph-up-arrow"></i> More predictable monthly revenue.</li>
                <li><i class="bi bi-person-heart"></i> Higher customer retention and repeat service visits.</li>
                <li><i class="bi bi-speedometer2"></i> Priority customers are easier to identify in booking monitor.</li>
                <li><i class="bi bi-bar-chart"></i> Subscription records support sales monitoring and reporting.</li>
                <li><i class="bi bi-megaphone"></i> Admin can promote plans based on customer service history.</li>
            </ul>
        </div>
    </div>
</div>

<div class="row g-4">
@forelse($plans as $plan)
    <div class="col-lg-4 col-md-6">
        <div class="card p-4 h-100 subscription-card">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <span class="service-icon"><i class="bi bi-gem"></i></span>
                <span class="badge bg-warning text-dark">Priority {{ $plan->priority_level }}</span>
            </div>
            <h2 class="h4 fw-bold">{{ $plan->plan_name }}</h2>
            <p class="text-muted">{{ $plan->description }}</p>
            <div class="display-6 fw-bold mb-2">RM {{ number_format($plan->monthly_price, 2) }}</div>
            <p class="text-muted small">{{ $plan->billing_cycle }} billing • {{ number_format($plan->discount_percentage, 0) }}% service discount</p>
            <div class="border rounded p-3 mb-3 small">{!! nl2br(e($plan->benefits)) !!}</div>
            <form method="POST" action="{{ route('customer.subscriptions.store', $plan) }}">
                @csrf
                <div class="mb-2">
                    <select name="payment_method" class="form-select" required>
                        <option value="Online Banking">Online Banking</option>
                        <option value="E-Wallet">E-Wallet</option>
                        <option value="Card">Card</option>
                        <option value="Cash">Cash</option>
                    </select>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" value="1" name="auto_renew" id="autoRenew{{ $plan->id }}">
                    <label class="form-check-label" for="autoRenew{{ $plan->id }}">Auto renew reminder</label>
                </div>
                <button class="btn btn-brand btn-rounded w-100" @disabled($activeSubscription?->subscription_plan_id === $plan->id)>Subscribe Now</button>
            </form>
        </div>
    </div>
@empty
    <div class="col-12"><div class="alert alert-info">No subscription plans available.</div></div>
@endforelse
</div>

@if($subscriptionHistory->isNotEmpty())
<div class="card p-3 mt-4">
    <h2 class="h5 fw-bold mb-3">Subscription History</h2>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>Reference</th><th>Plan</th><th>Start</th><th>End</th><th>Paid</th><th>Status</th></tr></thead>
            <tbody>
            @foreach($subscriptionHistory as $subscription)
                <tr>
                    <td>{{ $subscription->subscription_reference }}</td>
                    <td>{{ $subscription->plan->plan_name }}</td>
                    <td>{{ $subscription->starts_at->format('d M Y') }}</td>
                    <td>{{ $subscription->ends_at->format('d M Y') }}</td>
                    <td>RM {{ number_format($subscription->amount_paid, 2) }}</td>
                    <td><span class="badge {{ $subscription->status === 'Active' ? 'bg-success' : 'bg-secondary' }}">{{ $subscription->status }}</span></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection

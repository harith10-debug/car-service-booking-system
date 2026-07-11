@extends('layouts.app')
@section('title', 'Edit Subscription Plan')
@section('content')
<h1 class="page-title mb-3">Edit Subscription Plan</h1>
<div class="card p-4">
    <form method="POST" action="{{ route('admin.subscription-plans.update', $subscriptionPlan) }}">
        @csrf @method('PUT')
        @include('admin.subscription_plans._form', ['subscriptionPlan' => $subscriptionPlan])
        <button class="btn btn-dark">Update Plan</button>
        <a href="{{ route('admin.subscription-plans.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </form>
</div>
@endsection

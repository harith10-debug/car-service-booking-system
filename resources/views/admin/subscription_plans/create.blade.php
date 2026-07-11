@extends('layouts.app')
@section('title', 'Add Subscription Plan')
@section('content')
<h1 class="page-title mb-3">Add Subscription Plan</h1>
<div class="card p-4">
    <form method="POST" action="{{ route('admin.subscription-plans.store') }}">
        @csrf
        @include('admin.subscription_plans._form')
        <button class="btn btn-dark">Save Plan</button>
        <a href="{{ route('admin.subscription-plans.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </form>
</div>
@endsection

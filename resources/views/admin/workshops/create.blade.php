@extends('layouts.app')
@section('title', 'Add Workshop')
@section('content')
<h1 class="page-title mb-3">Add Workshop</h1>
<div class="card p-4">
    <form method="POST" action="{{ route('admin.workshops.store') }}">
        @csrf
        @include('admin.workshops._form')
        <button class="btn btn-dark">Save Workshop</button>
        <a href="{{ route('admin.workshops.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </form>
</div>
@endsection

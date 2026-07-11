@extends('layouts.app')
@section('title', 'Edit Workshop')
@section('content')
<h1 class="page-title mb-3">Edit Workshop</h1>
<div class="card p-4">
    <form method="POST" action="{{ route('admin.workshops.update', $workshop) }}">
        @csrf @method('PUT')
        @include('admin.workshops._form', ['workshop' => $workshop])
        <button class="btn btn-dark">Update Workshop</button>
        <a href="{{ route('admin.workshops.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </form>
</div>
@endsection

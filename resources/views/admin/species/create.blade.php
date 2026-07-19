@extends('admin.layouts.app')

@section('title', 'Add Fish Species')
@section('page_title', 'Fish Species')
@section('page_subtitle', 'Create a species record for user selection')

@section('content')
<div class="card card-shadow p-4">
    <div class="fw-semibold mb-3">Add Fish Species</div>

    <form method="post" action="{{ route('admin.species.store') }}">
        @csrf
        @include('admin.species.partials.form', ['species' => null])

        <div class="mt-3 d-flex gap-2">
            <button class="btn btn-primary" type="submit">Save</button>
            <a class="btn btn-outline-secondary" href="{{ route('admin.species.index') }}">Cancel</a>
        </div>
    </form>
</div>
@endsection

@extends('admin.layouts.app')

@section('title', 'Edit Fish Species')
@section('page_title', 'Fish Species')
@section('page_subtitle', 'Update species details')

@section('content')
<div class="card card-shadow p-4">
    <div class="fw-semibold mb-3">Edit Fish Species</div>

    <form method="post" action="{{ route('admin.species.update', $species) }}">
        @csrf
        @method('put')
        @include('admin.species.partials.form', ['species' => $species])

        <div class="mt-3 d-flex gap-2">
            <button class="btn btn-primary" type="submit">Update</button>
            <a class="btn btn-outline-secondary" href="{{ route('admin.species.index') }}">Cancel</a>
        </div>
    </form>
</div>
@endsection

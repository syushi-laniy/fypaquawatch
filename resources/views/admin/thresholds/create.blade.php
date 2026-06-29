@extends('admin.layouts.app')

@section('title', 'Add Threshold')
@section('page_title', 'Thresholds')
@section('page_subtitle', 'Create a new safe range')

@section('content')
<div class="card card-shadow p-4">
    <div class="fw-semibold mb-3">Add Threshold</div>

    <form method="post" action="{{ route('thresholds.store') }}">
        @csrf
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label">Parameter</label>
                <select class="form-select" name="parameter" required>
                    <option value="">Select parameter</option>
                    @foreach($parameters as $parameter)
                        <option value="{{ $parameter->name }}" {{ old('parameter') === $parameter->name ? 'selected' : '' }}>
                            {{ $parameter->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label">Minimum</label>
                <input class="form-control" type="text" name="min_value" value="{{ old('min_value') }}"
                       placeholder="Example: 6.5" required>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label">Maximum</label>
                <input class="form-control" type="text" name="max_value" value="{{ old('max_value') }}"
                       placeholder="Example: 7.5" required>
            </div>
        </div>

        <div class="mt-3 d-flex gap-2">
            <button class="btn btn-primary" type="submit">Save</button>
            <a class="btn btn-outline-secondary" href="{{ route('thresholds.index') }}">Cancel</a>
        </div>
    </form>
</div>
@endsection

@extends('admin.layouts.app')

@section('title', 'Edit Threshold')
@section('page_title', 'Thresholds')
@section('page_subtitle', 'Update safe range values')

@section('content')
<div class="card card-shadow p-4">
    <div class="fw-semibold mb-3">Edit Threshold</div>

    <form method="post" action="{{ route('thresholds.update', $threshold) }}">
        @csrf
        @method('put')
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label">Parameter</label>
                <select class="form-select" name="parameter" required>
                    @foreach($parameters as $parameter)
                        <option value="{{ $parameter->name }}"
                            {{ old('parameter', $threshold->parameter) === $parameter->name ? 'selected' : '' }}>
                            {{ $parameter->name }}
                        </option>
                    @endforeach
                    @if($parameters->where('name', $threshold->parameter)->isEmpty())
                        <option value="{{ $threshold->parameter }}" selected>{{ $threshold->parameter }}</option>
                    @endif
                </select>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label">Minimum</label>
                <input class="form-control" type="text" name="min_value" value="{{ old('min_value', $threshold->min_value) }}" required>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label">Maximum</label>
                <input class="form-control" type="text" name="max_value" value="{{ old('max_value', $threshold->max_value) }}" required>
            </div>
        </div>

        <div class="mt-3 d-flex gap-2">
            <button class="btn btn-primary" type="submit">Update</button>
            <a class="btn btn-outline-secondary" href="{{ route('thresholds.index') }}">Cancel</a>
        </div>
    </form>
</div>
@endsection

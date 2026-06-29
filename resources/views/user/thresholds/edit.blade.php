@extends('user.layouts.app')

@section('title', 'Edit Threshold')

@section('content')
<style>
    .form-card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 16px;
        box-shadow: 0 10px 26px rgba(31, 58, 95, 0.08);
        border: 1px solid rgba(7, 59, 76, 0.06);
    }
    .section-title {
        font-weight: 600;
        color: #0f1f26;
    }
    .icon-title {
        width: 18px;
        height: 18px;
        margin-right: 8px;
        vertical-align: text-bottom;
        color: #0b5f76;
    }
    .btn-primary {
        background: linear-gradient(135deg, #0f6c85 0%, #1f8aa5 100%);
        border: 0;
    }
</style>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <div class="h5 mb-1 fw-semibold">Edit Threshold</div>
        <div class="small muted">{{ $tank->name }}</div>
    </div>
    <a class="btn btn-outline-secondary btn-sm" href="{{ route('tanks.thresholds.index', $tank) }}">Back to Thresholds</a>
</div>

<div class="form-card p-4">
    <div class="section-title mb-3">
        <svg class="icon-title" viewBox="0 0 24 24" fill="none">
            <path d="M7 12h10M9.5 8.5h5M9.5 15.5h5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
            <path d="M4 5.5a2 2 0 0 1 2-2h8.5L20 9v9.5a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5.5Z" stroke="currentColor" stroke-width="1.6"/>
        </svg>
        Update Range
    </div>

    <form method="POST" action="{{ route('tanks.thresholds.update', [$tank, $threshold]) }}">
        @csrf
        @method('put')
        <div class="row g-3">
            <div class="col-12 col-md-4">
                <label class="form-label">Parameter</label>
                <select class="form-select" name="parameter" required>
                    @foreach($parameters as $parameter)
                        <option value="{{ $parameter->name }}"
                            {{ $parameter->name === $threshold->parameter ? 'selected' : '' }}>
                            {{ $parameter->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label">Minimum</label>
                <input class="form-control" type="text" name="min_value" value="{{ $threshold->min_value }}" required>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label">Maximum</label>
                <input class="form-control" type="text" name="max_value" value="{{ $threshold->max_value }}" required>
            </div>
        </div>
        <div class="mt-4 d-flex flex-wrap gap-2">
            <button class="btn btn-primary" type="submit">Update</button>
            <a class="btn btn-outline-secondary" href="{{ route('tanks.thresholds.index', $tank) }}">Cancel</a>
        </div>
    </form>
</div>
@endsection

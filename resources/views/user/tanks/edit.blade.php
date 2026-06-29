@extends('user.layouts.app')

@section('title', 'Edit Tank')

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
        <div class="h5 mb-1 fw-semibold">Edit Tank</div>
        <div class="small muted">Update the aquarium profile.</div>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-outline-secondary btn-sm" href="{{ route('tanks.index') }}">Back to Tanks</a>
        <a class="btn btn-outline-primary btn-sm" href="{{ route('tanks.thresholds.index', $tank) }}">
            Manage Thresholds
        </a>
    </div>
</div>

<div class="form-card p-4">
    <div class="section-title mb-3">
        <svg class="icon-title" viewBox="0 0 24 24" fill="none">
            <path d="M3 12c2.2-2.5 5.1-4 9-4 3.9 0 6.8 1.5 9 4-2.2 2.5-5.1 4-9 4-3.9 0-6.8-1.5-9-4Z" stroke="currentColor" stroke-width="1.6"/>
            <path d="M6 12c0 2.5-1.5 4-3 4 .5-1.5.5-2.5 0-4 .5-1.5.5-2.5 0-4 1.5 0 3 1.5 3 4Z" stroke="currentColor" stroke-width="1.6"/>
        </svg>
        Tank Details
    </div>

    <form method="post" action="{{ route('tanks.update', $tank) }}">
        @csrf
        @method('put')
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <label class="form-label">Tank Name</label>
                <input class="form-control" type="text" name="name" value="{{ old('name', $tank->name) }}" required>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label">Tank Code</label>
                <input class="form-control" type="text" name="code" value="{{ old('code', $tank->code) }}" placeholder="Optional">
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="Active" {{ old('status', $tank->status) === 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Inactive" {{ old('status', $tank->status) === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label">Control Mode</label>
                <select class="form-select" name="control_mode">
                    <option value="auto" {{ old('control_mode', $tank->control_mode) === 'auto' ? 'selected' : '' }}>Auto</option>
                    <option value="manual" {{ old('control_mode', $tank->control_mode) === 'manual' ? 'selected' : '' }}>Manual</option>
                </select>
            </div>
        </div>

        <div class="mt-4 d-flex flex-wrap gap-2">
            <button class="btn btn-primary" type="submit">Update Tank</button>
            <a class="btn btn-outline-secondary" href="{{ route('tanks.index') }}">Cancel</a>
        </div>
    </form>
</div>
@endsection

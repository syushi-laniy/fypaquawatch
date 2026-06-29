@extends('user.layouts.app')

@section('title', 'Tank Thresholds')

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
    .threshold-row {
        background: rgba(245, 250, 250, 0.6);
        border-radius: 12px;
        border: 1px solid rgba(7, 59, 76, 0.06);
    }
    .table thead th { border-bottom: 0; }
    .table > :not(caption) > * > * { border-bottom: 0; }
</style>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <div class="h5 mb-0 fw-bold">Thresholds for {{ $tank->name }}</div>
        <div class="small muted">Set safe ranges for each parameter.</div>
    </div>
    <a class="btn btn-outline-secondary" href="{{ route('tanks.edit', $tank) }}">Back to Tank</a>
</div>

<div class="form-card p-4 mb-3">
    <div class="section-title mb-3">
        <svg class="icon-title" viewBox="0 0 24 24" fill="none">
            <path d="M4 5h16v14H4z" stroke="currentColor" stroke-width="1.6"/>
            <path d="M9 3v4M15 3v4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
        </svg>
        Add or Update Threshold
    </div>
    <form method="POST" action="{{ route('tanks.thresholds.store', $tank) }}">
        @csrf
        <div class="row g-3">
            <div class="col-12 col-md-4">
                <label class="form-label">Parameter</label>
                <select class="form-select" name="parameter" required>
                    <option value="">Select parameter</option>
                    @foreach($parameters as $parameter)
                        <option value="{{ $parameter->name }}">{{ $parameter->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label">Minimum</label>
                <input class="form-control" type="text" name="min_value" required>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label">Maximum</label>
                <input class="form-control" type="text" name="max_value" required>
            </div>
        </div>
        <div class="mt-3">
            <button class="btn btn-primary" type="submit">Save Threshold</button>
        </div>
    </form>
</div>

<div class="form-card p-4">
    <div class="section-title mb-3">
        <svg class="icon-title" viewBox="0 0 24 24" fill="none">
            <path d="M7 12h10M9.5 8.5h5M9.5 15.5h5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
            <path d="M4 5.5a2 2 0 0 1 2-2h8.5L20 9v9.5a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5.5Z" stroke="currentColor" stroke-width="1.6"/>
        </svg>
        Current Thresholds
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Parameter</th>
                    <th>Minimum</th>
                    <th>Maximum</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($thresholds as $threshold)
                    <tr class="threshold-row">
                        <td>{{ $threshold->parameter }}</td>
                        <td>{{ $threshold->min_value }}</td>
                        <td>{{ $threshold->max_value }}</td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <a class="btn btn-sm btn-outline-primary"
                                   href="{{ route('tanks.thresholds.edit', [$tank, $threshold]) }}">Edit</a>
                                <form method="POST" action="{{ route('tanks.thresholds.destroy', [$tank, $threshold]) }}"
                                      onsubmit="return confirm('Delete this threshold?');">
                                    @csrf
                                    @method('delete')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">No thresholds set yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

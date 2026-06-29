@extends('admin.layouts.app')

@section('title', 'Thresholds')
@section('page_title', 'Thresholds')
@section('page_subtitle', 'Define safe ranges for each parameter')

@section('content')
<div class="card card-shadow p-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div class="fw-semibold">Threshold List</div>
        <a class="btn btn-sm btn-outline-primary" href="{{ route('thresholds.create') }}">+ Add Threshold</a>
    </div>

    <form class="row g-2 align-items-end mb-3" method="GET" action="{{ route('thresholds.index') }}">
        <div class="col-12 col-md-8">
            <label class="form-label mb-1">Search</label>
            <input class="form-control" type="text" name="q" value="{{ request('q') }}"
                   placeholder="Filter by parameter">
        </div>
        <div class="col-12 col-md-4 d-flex gap-2">
            <button class="btn btn-primary w-100" type="submit">Filter</button>
            <a class="btn btn-outline-secondary w-100" href="{{ route('thresholds.index') }}">Reset</a>
        </div>
    </form>

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
                    <tr>
                        <td>{{ $threshold->parameter }}</td>
                        <td>{{ $threshold->min_value }}</td>
                        <td>{{ $threshold->max_value }}</td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('thresholds.edit', $threshold) }}">Edit</a>
                                <form method="POST" action="{{ route('thresholds.destroy', $threshold) }}"
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
                        <td colspan="4" class="text-center text-muted">No thresholds found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

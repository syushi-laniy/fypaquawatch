@extends('admin.layouts.app')

@section('title', 'Fish Species Details')
@section('page_title', 'Fish Species')
@section('page_subtitle', 'View species details and tank usage')

@section('content')
<div class="card card-shadow p-4 mb-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div class="fw-semibold">{{ $species->name }}</div>
        <div class="d-flex gap-2">
            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.species.edit', $species) }}">Edit</a>
            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.species.index') }}">Back</a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered align-middle mb-0">
            <tbody>
                <tr><th class="text-muted" style="width: 220px;">Species Name</th><td>{{ $species->name }}</td></tr>
                <tr><th class="text-muted">Description</th><td>{{ $species->description ?? '-' }}</td></tr>
                <tr><th class="text-muted">Minimum pH</th><td>{{ number_format($species->min_ph, 2) }}</td></tr>
                <tr><th class="text-muted">Maximum pH</th><td>{{ number_format($species->max_ph, 2) }}</td></tr>
                <tr><th class="text-muted">Image Path</th><td>{{ $species->image_path ?? '-' }}</td></tr>
                <tr>
                    <th class="text-muted">Status</th>
                    <td>
                        <span class="badge text-bg-{{ $species->is_active ? 'success' : 'secondary' }}">
                            {{ $species->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card card-shadow p-4">
    <div class="fw-semibold mb-3">Assigned Tanks</div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Tank</th>
                    <th>User</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($species->tanks as $tank)
                    <tr>
                        <td>{{ $tank->name }}</td>
                        <td>{{ $tank->user?->name ?? '-' }}</td>
                        <td>{{ $tank->status ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted">No tanks currently use this species.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

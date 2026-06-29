@extends('admin.layouts.app')

@section('title', 'Tanks')
@section('page_title', 'Tanks')
@section('page_subtitle', 'Manage registered monitoring tanks')

@section('content')
<div class="card card-shadow p-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div class="fw-semibold">Tank List</div>
        <span class="badge text-bg-light">Demo</span>
    </div>

    <form class="row g-2 align-items-end mb-3" method="GET" action="{{ route('admin.tanks.index') }}">
        <div class="col-12 col-md-6">
            <label class="form-label mb-1">Search</label>
            <input class="form-control" type="text" name="q" value="{{ request('q') }}"
                   placeholder="Tank name, code, location, or owner">
        </div>
        <div class="col-12 col-md-3">
            <label class="form-label mb-1">Status</label>
            <select class="form-select" name="status">
                <option value="">All</option>
                <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>Active</option>
                <option value="Inactive" {{ request('status') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div class="col-12 col-md-3 d-flex gap-2">
            <button class="btn btn-primary w-100" type="submit">Filter</button>
            <a class="btn btn-outline-secondary w-100" href="{{ route('admin.tanks.index') }}">Reset</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Tank</th>
                    <th>Owner</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tanks as $tank)
                    <tr>
                        <td>{{ $tank->name }}</td>
                        <td>{{ $tank->user->name ?? '-' }}</td>
                        <td><span class="badge text-bg-success">{{ $tank->status }}</span></td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.tanks.show', $tank) }}">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">No tanks found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

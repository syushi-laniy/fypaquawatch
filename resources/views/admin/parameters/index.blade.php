@extends('admin.layouts.app')

@section('title', 'Parameters')
@section('page_title', 'Parameters')
@section('page_subtitle', 'Manage monitored water parameters')

@section('content')
<div class="card card-shadow p-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div class="fw-semibold">Parameter List</div>
        <a class="btn btn-sm btn-outline-primary" href="{{ route('parameters.create') }}">+ Add Parameter</a>
    </div>

    <form class="row g-2 align-items-end mb-3" method="GET" action="{{ route('parameters.index') }}">
        <div class="col-12 col-md-6">
            <label class="form-label mb-1">Search</label>
            <input class="form-control" type="text" name="q" value="{{ request('q') }}"
                   placeholder="Parameter name or unit">
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
            <a class="btn btn-outline-secondary w-100" href="{{ route('parameters.index') }}">Reset</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Parameter</th>
                    <th>Unit</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($parameters as $parameter)
                    <tr>
                        <td>{{ $parameter->name }}</td>
                        <td>{{ $parameter->unit ?? '-' }}</td>
                        <td><span class="badge text-bg-success">{{ $parameter->status }}</span></td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('parameters.edit', $parameter) }}">Edit</a>
                                <form method="POST" action="{{ route('parameters.destroy', $parameter) }}"
                                      onsubmit="return confirm('Delete this parameter?');">
                                    @csrf
                                    @method('delete')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">No parameters found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

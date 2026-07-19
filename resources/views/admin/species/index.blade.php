@extends('admin.layouts.app')

@section('title', 'Fish Species')
@section('page_title', 'Fish Species')
@section('page_subtitle', 'Manage species records used by user tank selection')

@section('content')
<div class="card card-shadow p-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div class="fw-semibold">Fish Species List</div>
        <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.species.create') }}">+ Add Species</a>
    </div>

    <form class="row g-2 align-items-end mb-3" method="GET" action="{{ route('admin.species.index') }}">
        <div class="col-12 col-md-6">
            <label class="form-label mb-1">Search</label>
            <input class="form-control" type="text" name="q" value="{{ request('q') }}"
                   placeholder="Species name or description">
        </div>
        <div class="col-12 col-md-3">
            <label class="form-label mb-1">Status</label>
            <select class="form-select" name="status">
                <option value="">All</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div class="col-12 col-md-3 d-flex gap-2">
            <button class="btn btn-primary w-100" type="submit">Filter</button>
            <a class="btn btn-outline-secondary w-100" href="{{ route('admin.species.index') }}">Reset</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Species</th>
                    <th>pH Range</th>
                    <th>Image Path</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($species as $item)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $item->name }}</div>
                            <div class="small text-muted">{{ \Illuminate\Support\Str::limit($item->description, 80) ?: '-' }}</div>
                        </td>
                        <td>{{ number_format($item->min_ph, 2) }} - {{ number_format($item->max_ph, 2) }}</td>
                        <td>{{ $item->image_path ?? '-' }}</td>
                        <td>
                            <span class="badge text-bg-{{ $item->is_active ? 'success' : 'secondary' }}">
                                {{ $item->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.species.show', $item) }}">View</a>
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.species.edit', $item) }}">Edit</a>
                                <form method="POST" action="{{ route('admin.species.destroy', $item) }}"
                                      onsubmit="return confirm('Delete this fish species?');">
                                    @csrf
                                    @method('delete')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">No fish species found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

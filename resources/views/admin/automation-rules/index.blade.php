@extends('admin.layouts.app')

@section('title', 'Automation Rules')
@section('page_title', 'Automation Rules')
@section('page_subtitle', 'Configure actions triggered by sensor readings')

@section('content')
<div class="card card-shadow p-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div class="fw-semibold">Rule List</div>
        <a class="btn btn-sm btn-outline-primary" href="{{ route('automation-rules.create') }}">+ Add Rule</a>
    </div>

    <form class="row g-2 align-items-end mb-3" method="GET" action="{{ route('automation-rules.index') }}">
        <div class="col-12 col-md-8">
            <label class="form-label mb-1">Search</label>
            <input class="form-control" type="text" name="q" value="{{ request('q') }}"
                   placeholder="Rule name, trigger, or action">
        </div>
        <div class="col-12 col-md-4 d-flex gap-2">
            <button class="btn btn-primary w-100" type="submit">Filter</button>
            <a class="btn btn-outline-secondary w-100" href="{{ route('automation-rules.index') }}">Reset</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Rule</th>
                    <th>Trigger</th>
                    <th>Action</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rules as $rule)
                    <tr>
                        <td>{{ $rule->name }}</td>
                        <td>{{ $rule->trigger }}</td>
                        <td><span class="badge text-bg-info">{{ $rule->action }}</span></td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('automation-rules.edit', $rule) }}">Edit</a>
                                <form method="POST" action="{{ route('automation-rules.destroy', $rule) }}"
                                      onsubmit="return confirm('Delete this automation rule?');">
                                    @csrf
                                    @method('delete')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">No automation rules found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

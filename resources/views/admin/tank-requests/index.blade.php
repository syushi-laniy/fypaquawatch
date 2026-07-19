@extends('admin.layouts.app')

@section('title', 'Tank Requests')
@section('page_title', 'Tank Requests')
@section('page_subtitle', 'Review requested tanks and setup progress')

@section('content')
@php
    $badgeClasses = [
        'pending' => 'text-bg-warning',
        'approved' => 'text-bg-primary',
        'in_progress' => 'text-bg-info',
        'completed' => 'text-bg-success',
        'rejected' => 'text-bg-danger',
    ];
@endphp

<div class="card card-shadow p-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div class="fw-semibold">Tank Request List</div>
    </div>

    <form class="row g-2 align-items-end mb-3" method="GET" action="{{ route('admin.tank-requests.index') }}">
        <div class="col-12 col-md-4">
            <label class="form-label mb-1">Status</label>
            <select class="form-select" name="status">
                <option value="">All</option>
                @foreach(\App\Models\TankRequest::STATUSES as $status)
                    <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                        {{ str_replace('_', ' ', ucfirst($status)) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-3 d-flex gap-2">
            <button class="btn btn-primary w-100" type="submit">Filter</button>
            <a class="btn btn-outline-secondary w-100" href="{{ route('admin.tank-requests.index') }}">Reset</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>User</th>
                    <th>Requested Tank</th>
                    <th>Tank Size</th>
                    <th>Request Date</th>
                    <th>Status</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tankRequests as $tankRequest)
                    <tr>
                        <td>{{ $tankRequest->user->name ?? '-' }}</td>
                        <td>{{ $tankRequest->tank_name }}</td>
                        <td>{{ $tankRequest->tank_size }}</td>
                        <td>{{ $tankRequest->created_at->format('d F Y') }}</td>
                        <td>
                            <span class="badge {{ $badgeClasses[$tankRequest->status] ?? 'text-bg-secondary' }}">
                                {{ str_replace('_', ' ', ucfirst($tankRequest->status)) }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.tank-requests.show', $tankRequest) }}">Review</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">No tank requests found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

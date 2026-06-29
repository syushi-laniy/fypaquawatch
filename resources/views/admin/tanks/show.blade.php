@extends('admin.layouts.app')

@section('title', 'Tank Details')
@section('page_title', 'Tank Details')
@section('page_subtitle', 'Summary for a single monitoring unit')

@section('content')
<div class="card card-shadow p-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div class="fw-semibold">{{ $tank->name }}</div>
        <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.tanks.index') }}">Back to Tanks</a>
    </div>

    <div class="table-responsive">
        <table class="table table-borderless mb-0">
            <tbody>
                <tr>
                    <th class="text-muted">Code</th>
                    <td>{{ $tank->code ?? '-' }}</td>
                </tr>
                <tr>
                    <th class="text-muted">Owner</th>
                    <td>{{ $tank->user->name ?? '-' }}</td>
                </tr>
                <tr>
                    <th class="text-muted">Location</th>
                    <td>{{ $tank->location ?? '-' }}</td>
                </tr>
                <tr>
                    <th class="text-muted">Status</th>
                    <td><span class="badge text-bg-success">{{ $tank->status }}</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

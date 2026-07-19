@extends('admin.layouts.app')

@section('title', 'Tank Request Details')
@section('page_title', 'Tank Request Details')
@section('page_subtitle', 'Review request information and update setup status')

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

<div class="row g-4">
    <div class="col-12 col-xl-7">
        <div class="card card-shadow p-4 h-100">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <div class="fw-semibold">{{ $tankRequest->tank_name }}</div>
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.tank-requests.index') }}">Back to Requests</a>
            </div>

            <div class="table-responsive">
                <table class="table table-borderless mb-0">
                    <tbody>
                        <tr><th class="text-muted">User</th><td>{{ $tankRequest->user->name ?? '-' }} ({{ $tankRequest->user->email ?? '-' }})</td></tr>
                        <tr><th class="text-muted">Request Date</th><td>{{ $tankRequest->created_at->format('d F Y') }}</td></tr>
                        <tr><th class="text-muted">Status</th><td><span class="badge {{ $badgeClasses[$tankRequest->status] ?? 'text-bg-secondary' }}">{{ str_replace('_', ' ', ucfirst($tankRequest->status)) }}</span></td></tr>
                        <tr><th class="text-muted">Tank Size</th><td>{{ $tankRequest->tank_size }}</td></tr>
                        <tr><th class="text-muted">Fish Species</th><td>{{ $tankRequest->fish_species ?? '-' }}</td></tr>
                        <tr><th class="text-muted">Delivery Address</th><td>{{ $tankRequest->delivery_address }}</td></tr>
                        <tr><th class="text-muted">Phone Number</th><td>{{ $tankRequest->phone_number }}</td></tr>
                        <tr><th class="text-muted">Additional Notes</th><td>{{ $tankRequest->additional_notes ?? '-' }}</td></tr>
                        <tr><th class="text-muted">Tank Code</th><td>{{ $tankRequest->tank_code ?? 'Not assigned' }}</td></tr>
                        <tr><th class="text-muted">Admin Note</th><td>{{ $tankRequest->admin_note ?? '-' }}</td></tr>
                        <tr><th class="text-muted">Approved By</th><td>{{ $tankRequest->approver->name ?? '-' }}</td></tr>
                        <tr><th class="text-muted">Approved At</th><td>{{ $tankRequest->approved_at?->format('d F Y H:i') ?? '-' }}</td></tr>
                        <tr><th class="text-muted">Completed At</th><td>{{ $tankRequest->completed_at?->format('d F Y H:i') ?? '-' }}</td></tr>
                        <tr>
                            <th class="text-muted">Created Tank</th>
                            <td>
                                @if($tankRequest->tank)
                                    <a href="{{ route('admin.tanks.show', $tankRequest->tank) }}">{{ $tankRequest->tank->name }}</a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-5">
        <div class="card card-shadow p-4">
            <div class="fw-semibold mb-3">Update Request</div>

            <form method="POST" action="{{ route('admin.tank-requests.update', $tankRequest) }}">
                @csrf
                @method('put')

                <div class="mb-3">
                    <label class="form-label">Request Status</label>
                    <select class="form-select" name="status" required>
                        @foreach(\App\Models\TankRequest::STATUSES as $status)
                            <option value="{{ $status }}" {{ old('status', $tankRequest->status) === $status ? 'selected' : '' }}>
                                {{ str_replace('_', ' ', ucfirst($status)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tank Code</label>
                    <input class="form-control" type="text" name="tank_code" value="{{ old('tank_code', $tankRequest->tank_code) }}" placeholder="Example: AQUA124">
                    <div class="form-text">Required when marking the request as completed.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Admin Note</label>
                    <textarea class="form-control" name="admin_note" rows="5" placeholder="Example: IoT sensors have been configured and the kit is ready for delivery.">{{ old('admin_note', $tankRequest->admin_note) }}</textarea>
                </div>

                <button class="btn btn-primary" type="submit">Update Request</button>
            </form>
        </div>
    </div>
</div>
@endsection

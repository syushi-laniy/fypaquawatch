@extends('user.layouts.app')

@section('title', 'Tank Request')

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

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <div class="h5 mb-1 fw-semibold">{{ $tankRequest->tank_name }}</div>
        <div class="small muted">Tank request details.</div>
    </div>
    <a class="btn btn-outline-secondary btn-sm" href="{{ route('tanks.index') }}">Back to Tanks</a>
</div>

<div class="card border-0 shadow-sm p-4">
    <div class="table-responsive">
        <table class="table table-borderless mb-0">
            <tbody>
                <tr><th class="text-muted">Request Date</th><td>{{ $tankRequest->created_at->format('d F Y') }}</td></tr>
                <tr><th class="text-muted">Status</th><td><span class="badge {{ $badgeClasses[$tankRequest->status] ?? 'text-bg-secondary' }}">{{ str_replace('_', ' ', ucfirst($tankRequest->status)) }}</span></td></tr>
                <tr><th class="text-muted">Tank Size</th><td>{{ $tankRequest->tank_size }}</td></tr>
                <tr><th class="text-muted">Fish Species</th><td>{{ $tankRequest->fish_species ?? '-' }}</td></tr>
                <tr><th class="text-muted">Delivery Address</th><td>{{ $tankRequest->delivery_address }}</td></tr>
                <tr><th class="text-muted">Phone Number</th><td>{{ $tankRequest->phone_number }}</td></tr>
                <tr><th class="text-muted">Additional Notes</th><td>{{ $tankRequest->additional_notes ?? '-' }}</td></tr>
                <tr><th class="text-muted">Tank Code</th><td>{{ $tankRequest->tank_code ?? 'Not assigned' }}</td></tr>
                <tr><th class="text-muted">Admin Note</th><td>{{ $tankRequest->admin_note ?? '-' }}</td></tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

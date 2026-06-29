@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'System-wide AquaWatch management overview')

@section('content')
@php
    $statCards = [
        ['label' => 'Total Tanks', 'value' => $stats['tanks'] ?? 0, 'note' => 'Registered monitoring tanks'],
        ['label' => 'Parameters', 'value' => $stats['parameters'] ?? 0, 'note' => 'Active water measurements'],
        ['label' => 'Thresholds', 'value' => $stats['thresholds'] ?? 0, 'note' => 'Configured safety ranges'],
        ['label' => 'Automation Rules', 'value' => $stats['rules'] ?? 0, 'note' => 'System control rules'],
        ['label' => 'Registered Users', 'value' => $stats['users'] ?? 0, 'note' => 'AquaWatch user accounts'],
        ['label' => 'Fish Analyses', 'value' => $stats['analyses'] ?? 0, 'note' => 'Completed AI assessments'],
    ];
@endphp

<div class="row g-3 mb-4">
    @foreach($statCards as $stat)
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card card-shadow p-3 h-100">
                <div class="small muted">{{ $stat['label'] }}</div>
                <div class="display-6 fw-semibold">{{ $stat['value'] }}</div>
                <div class="small muted">{{ $stat['note'] }}</div>
            </div>
        </div>
    @endforeach
</div>

<div class="card card-shadow p-4 mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div class="fw-semibold">Recent Fish Analysis</div>
        <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.fish-analyses.index') }}">View All</a>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>User</th>
                    <th>Tank</th>
                    <th>Result</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentAnalyses as $analysis)
                    @php($healthy = strtoupper($analysis->status) === 'HEALTHY')
                    <tr>
                        <td class="text-nowrap">{{ $analysis->created_at->format('d M Y, h:i A') }}</td>
                        <td>{{ $analysis->user->name ?? 'Deleted user' }}</td>
                        <td>{{ $analysis->tank->name ?? 'Not linked' }}</td>
                        <td>{{ $analysis->disease_name }}</td>
                        <td>
                            <span class="badge text-bg-{{ $healthy ? 'success' : 'warning' }}">
                                {{ $analysis->status }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No fish analyses recorded yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card card-shadow p-4">
    <div class="fw-semibold mb-3">Recent Alerts / Latest Tank Readings</div>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Tank</th>
                    <th>Parameter</th>
                    <th>Value</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentReadings as $reading)
                    <tr>
                        <td class="text-nowrap">{{ $reading['date']->format('d M Y, h:i A') }}</td>
                        <td>{{ $reading['tank']->name ?? 'Deleted tank' }}</td>
                        <td>{{ $reading['parameter'] }}</td>
                        <td>{{ $reading['value'] }} {{ $reading['unit'] }}</td>
                        <td>
                            <span class="badge text-bg-{{ $reading['status'] === 'Good' ? 'success' : 'warning' }}">
                                {{ $reading['status'] }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No tank readings recorded yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

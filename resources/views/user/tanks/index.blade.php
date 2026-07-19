@extends('user.layouts.app')

@section('title', 'My Tanks')

@section('content')
<style>
    .tank-card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 16px;
        box-shadow: 0 8px 22px rgba(31, 58, 95, 0.06);
        transition: all 0.25s ease;
        border: 1px solid rgba(7, 59, 76, 0.06);
        padding: 1.25rem;
    }
    .tank-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(31, 58, 95, 0.08);
    }
    .tank-meta {
        color: #5b6b74;
        font-size: 0.9rem;
    }
    .tank-status {
        background: rgba(25, 135, 84, 0.12);
        color: #18794e;
        border: 1px solid rgba(25, 135, 84, 0.2);
        font-weight: 600;
    }
    .btn-primary {
        background: linear-gradient(135deg, #0f6c85 0%, #1f8aa5 100%);
        border: 0;
    }
    .btn-outline-danger {
        border-color: rgba(220, 53, 69, 0.5);
        color: #dc3545;
    }
    .btn-outline-danger:hover {
        background: rgba(220, 53, 69, 0.08);
        color: #b02a37;
    }
    .icon-inline {
        width: 18px;
        height: 18px;
        margin-right: 6px;
        vertical-align: text-bottom;
    }
    .tank-actions-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 0.6rem;
        align-items: center;
    }
    .tank-actions-row .btn-primary {
        min-height: 42px;
        border-radius: 12px;
        font-weight: 700;
    }
    .tank-action-toggle {
        width: 42px;
        height: 42px;
        border: 1px solid #D6E8ED;
        border-radius: 12px;
        background: #fff;
        color: #8a9aa4;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        box-shadow: 0 4px 12px rgba(15, 87, 110, 0.08);
    }
    .tank-action-toggle:hover,
    .tank-action-toggle:focus,
    .tank-action-toggle.show {
        color: #0b5f76;
        border-color: rgba(31, 138, 165, 0.35);
        background: #F8FCFD;
    }
    .tank-action-menu {
        min-width: 190px;
        padding: 0.4rem 0;
        border: 1px solid #D6E8ED;
        border-radius: 14px;
        box-shadow: 0 12px 24px rgba(31, 58, 95, 0.12);
        overflow: hidden;
    }
    .tank-action-item {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        width: 100%;
        border: 0;
        background: transparent;
        color: #5b6b74;
        font-weight: 600;
        text-decoration: none;
        padding: 0.75rem 0.9rem;
    }
    .tank-action-item:hover,
    .tank-action-item:focus {
        background: #F8FCFD;
        color: #0b5f76;
    }
    .tank-action-item svg {
        width: 18px;
        height: 18px;
        flex: 0 0 18px;
    }
    .tank-action-delete {
        color: #dc3545;
        border-top: 1px solid #E8EFF1;
    }
    .tank-action-delete:hover,
    .tank-action-delete:focus {
        background: rgba(220, 53, 69, 0.06);
        color: #dc3545;
    }
    .empty-state {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 16px;
        box-shadow: 0 8px 22px rgba(31, 58, 95, 0.06);
        border: 1px solid rgba(7, 59, 76, 0.06);
    }
    .empty-fish {
        width: 120px;
        height: 120px;
        margin: 0 auto 1rem;
        color: #1f8aa5;
    }
    .request-card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 16px;
        box-shadow: 0 8px 22px rgba(31, 58, 95, 0.06);
        border: 1px solid rgba(7, 59, 76, 0.06);
    }
    .request-view-btn {
        border: 2px solid #E2EDF3;
        border-radius: 12px;
        background: #fff;
        color: #52656d;
        font-weight: 700;
        padding: 0.55rem 1.05rem;
        box-shadow: 0 2px 6px rgba(31, 58, 95, 0.04);
    }
    .request-view-btn:hover,
    .request-view-btn:focus {
        border-color: #CFE2EC;
        background: #F8FCFD;
        color: #0b5f76;
    }
</style>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <div class="h5 mb-1 fw-semibold">My Tanks</div>
	        <div class="small muted">Manage active tanks and review new tank requests.</div>
    </div>
    <a class="btn btn-primary" href="{{ route('tank-requests.create') }}">+ Request New Tank</a>
</div>

@if($tanks->isEmpty())
    <div class="p-4 text-center empty-state">
        <div class="empty-fish">
            <svg viewBox="0 0 24 24" fill="none">
                <path d="M3 12c2.2-2.5 5.1-4 9-4 3.9 0 6.8 1.5 9 4-2.2 2.5-5.1 4-9 4-3.9 0-6.8-1.5-9-4Z" stroke="currentColor" stroke-width="1.6"/>
                <path d="M6 12c0 2.5-1.5 4-3 4 .5-1.5.5-2.5 0-4 .5-1.5.5-2.5 0-4 1.5 0 3 1.5 3 4Z" stroke="currentColor" stroke-width="1.6"/>
                <path d="M15.5 10.5h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="fw-semibold mb-1">No tanks yet</div>
        <div class="small muted mb-3">Request your first aquarium to start monitoring after admin setup.</div>
        <a class="btn btn-primary" href="{{ route('tank-requests.create') }}">+ Request New Tank</a>
    </div>
@else
    <div class="row g-3">
        @foreach($tanks as $tank)
            <div class="col-12 col-md-6 col-xl-4">
	                <div class="tank-card">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="fw-semibold">{{ $tank->name }}</div>
                        <span class="badge tank-status">{{ $tank->status }}</span>
                    </div>
                    <div class="tank-meta mb-2">
                        <svg class="icon-inline" viewBox="0 0 24 24" fill="none">
                            <path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                        </svg>
                        Code: {{ $tank->code ?? '-' }}
                    </div>
                    <div class="tank-meta mb-2">Tank height: {{ number_format($tank->tankHeightCm(), 1) }} cm</div>
                    <div class="tank-actions-row">
                        <a class="btn btn-sm btn-primary d-inline-flex align-items-center justify-content-center" href="{{ route('tanks.dashboard', $tank) }}">
                            <svg class="icon-inline" viewBox="0 0 24 24" fill="none">
                                <path d="M4 5h16v14H4z" stroke="currentColor" stroke-width="1.6"/>
                                <path d="M9 3v4M15 3v4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                                <path d="M8 12h3v4H8zM13 10h3v6h-3z" stroke="currentColor" stroke-width="1.6"/>
                            </svg>
                            Dashboard
                        </a>
                        <div class="dropdown">
                            <button class="tank-action-toggle" type="button" data-bs-toggle="dropdown"
                                    data-bs-boundary="viewport" aria-expanded="false" aria-label="Tank actions">
                                ...
                            </button>
                            <div class="dropdown-menu dropdown-menu-end tank-action-menu">
                                <a class="tank-action-item" href="{{ route('tanks.show', $tank) }}">
                                    <svg viewBox="0 0 24 24" fill="none">
                                        <path d="M3 12s3.5-6 9-6 9 6 9 6-3.5 6-9 6-9-6-9-6Z" stroke="currentColor" stroke-width="1.7"/>
                                        <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" stroke="currentColor" stroke-width="1.7"/>
                                    </svg>
                                    View Details
                                </a>
                                <a class="tank-action-item" href="{{ route('tanks.edit', $tank) }}">
                                    <svg viewBox="0 0 24 24" fill="none">
                                        <path d="M4 20h4l10-10-4-4L4 16v4Z" stroke="currentColor" stroke-width="1.7"/>
                                        <path d="M13 7l4 4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                    </svg>
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('tanks.destroy', $tank) }}"
                                      onsubmit="return confirm('Delete this tank?');">
                                    @csrf
                                    @method('delete')
                                    <button class="tank-action-item tank-action-delete" type="submit">
                                        <svg viewBox="0 0 24 24" fill="none">
                                            <path d="M4 7h16" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                            <path d="M9 7V5h6v2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                            <path d="M7 7l1 12h8l1-12" stroke="currentColor" stroke-width="1.7"/>
                                        </svg>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

@php
    $requestBadgeClasses = [
        'pending' => 'text-bg-warning',
        'approved' => 'text-bg-primary',
        'in_progress' => 'text-bg-info',
        'completed' => 'text-bg-success',
        'rejected' => 'text-bg-danger',
    ];
@endphp

<div class="request-card p-4 mt-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <div class="fw-semibold">My Tank Requests</div>
            <div class="small muted">Requests are converted to tanks only after admin completion.</div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Requested Tank</th>
                    <th>Request Date</th>
                    <th>Status</th>
                    <th>Tank Code</th>
                    <th>Admin Note</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tankRequests as $tankRequest)
                    <tr>
                        <td>{{ $tankRequest->tank_name }}</td>
                        <td>{{ $tankRequest->created_at->format('d F Y') }}</td>
                        <td>
                            <span class="badge {{ $requestBadgeClasses[$tankRequest->status] ?? 'text-bg-secondary' }}">
                                {{ str_replace('_', ' ', ucfirst($tankRequest->status)) }}
                            </span>
                        </td>
                        <td>{{ $tankRequest->tank_code ?? 'Not assigned' }}</td>
                        <td>{{ $tankRequest->admin_note ?? '-' }}</td>
                        <td class="text-end">
                            <a class="btn btn-sm request-view-btn" href="{{ route('tank-requests.show', $tankRequest) }}">View Details</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">No tank requests yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

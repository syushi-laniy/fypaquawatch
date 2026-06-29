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
</style>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <div class="h5 mb-1 fw-semibold">My Tanks</div>
        <div class="small muted">Welcome {{ auth()->user()->name ?? 'User' }} to AquaWatch.</div>
        <div class="small muted">Add and manage your aquarium tanks.</div>
    </div>
    <a class="btn btn-primary" href="{{ route('tanks.create') }}">+ Add Tank</a>
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
        <div class="small muted mb-3">Add your first aquarium to start monitoring.</div>
        <a class="btn btn-primary" href="{{ route('tanks.create') }}">+ Add Tank</a>
    </div>
@else
    <div class="row g-3">
        @foreach($tanks as $tank)
            <div class="col-12 col-md-6 col-xl-4">
                <div class="tank-card p-4 h-100">
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
                    <div class="d-grid gap-2 d-sm-flex flex-wrap">
                        <a class="btn btn-sm btn-primary" href="{{ route('tanks.dashboard', $tank) }}">
                            <svg class="icon-inline" viewBox="0 0 24 24" fill="none">
                                <path d="M4 5h16v14H4z" stroke="currentColor" stroke-width="1.6"/>
                                <path d="M9 3v4M15 3v4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                                <path d="M8 12h3v4H8zM13 10h3v6h-3z" stroke="currentColor" stroke-width="1.6"/>
                            </svg>
                            Dashboard
                        </a>
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('tanks.show', $tank) }}">
                            <svg class="icon-inline" viewBox="0 0 24 24" fill="none">
                                <path d="M3 12s3.5-6 9-6 9 6 9 6-3.5 6-9 6-9-6-9-6Z" stroke="currentColor" stroke-width="1.6"/>
                                <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" stroke="currentColor" stroke-width="1.6"/>
                            </svg>
                            View
                        </a>
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('tanks.edit', $tank) }}">
                            <svg class="icon-inline" viewBox="0 0 24 24" fill="none">
                                <path d="M4 20h4l10-10-4-4L4 16v4Z" stroke="currentColor" stroke-width="1.6"/>
                                <path d="M13 7l4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                            </svg>
                            Edit
                        </a>
                        <form method="POST" action="{{ route('tanks.destroy', $tank) }}"
                              onsubmit="return confirm('Delete this tank?');">
                            @csrf
                            @method('delete')
                            <button class="btn btn-sm btn-outline-danger" type="submit">
                                <svg class="icon-inline" viewBox="0 0 24 24" fill="none">
                                    <path d="M4 7h16" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                                    <path d="M9 7V5h6v2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                                    <path d="M7 7l1 12h8l1-12" stroke="currentColor" stroke-width="1.6"/>
                                </svg>
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection

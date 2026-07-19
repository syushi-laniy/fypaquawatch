@extends('user.layouts.app')

@section('title', 'Tank Details')

@section('content')
<style>
    .detail-card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 16px;
        box-shadow: 0 10px 26px rgba(31, 58, 95, 0.08);
        border: 1px solid rgba(7, 59, 76, 0.06);
    }
    .detail-item {
        background: rgba(245, 250, 250, 0.6);
        border-radius: 12px;
        padding: 12px 14px;
        border: 1px solid rgba(7, 59, 76, 0.06);
    }
    .detail-label {
        color: #5b6b74;
        font-size: 0.85rem;
    }
    .detail-value {
        font-weight: 600;
        color: #0f1f26;
    }
    .status-badge {
        background: rgba(25, 135, 84, 0.12);
        color: #18794e;
        border: 1px solid rgba(25, 135, 84, 0.2);
    }
    .icon-title {
        width: 18px;
        height: 18px;
        margin-right: 8px;
        vertical-align: text-bottom;
        color: #0b5f76;
    }
</style>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <div class="h5 mb-1 fw-semibold">{{ $tank->name }}</div>
        <div class="small muted">Tank overview and configuration.</div>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-outline-secondary btn-sm" href="{{ route('tanks.index') }}">Back to Tanks</a>
        <a class="btn btn-outline-primary btn-sm" href="{{ route('tanks.edit', $tank) }}">Edit Tank</a>
    </div>
</div>

<div class="detail-card p-4">
    <div class="fw-semibold mb-3">
        <svg class="icon-title" viewBox="0 0 24 24" fill="none">
            <path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
        </svg>
        Essentials
    </div>
    <div class="row g-3">
        <div class="col-12 col-md-6">
            <div class="detail-item">
                <div class="detail-label">Tank Code</div>
                <div class="detail-value">{{ $tank->code ?? '-' }}</div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="detail-item">
                <div class="detail-label">Status</div>
                <div class="detail-value">
                    <span class="badge status-badge">{{ $tank->status }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="detail-item">
                <div class="detail-label">Control Mode</div>
                <div class="detail-value text-uppercase">{{ $tank->control_mode ?? 'auto' }}</div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="detail-item">
                <div class="detail-label">Tank Height</div>
                <div class="detail-value">{{ number_format($tank->tankHeightCm(), 1) }} cm</div>
            </div>
        </div>
    </div>
</div>
@endsection

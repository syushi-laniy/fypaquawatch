@extends('user.layouts.app')

@section('title', 'My Profile')

@section('content')
<style>
    .profile-shell {
        width: calc(100% - 48px);
        max-width: 880px;
        margin: 24px auto;
        padding: 24px;
        border-radius: 28px;
        border: 1px solid rgba(255, 255, 255, 0.45);
        background: rgba(255, 255, 255, 0.20);
        backdrop-filter: blur(18px);
        -webkit-backdrop-filter: blur(18px);
        box-shadow:
            0 12px 35px rgba(25, 73, 110, 0.16),
            inset 0 1px 0 rgba(255, 255, 255, 0.35);
        overflow: hidden;
    }
    .settings-card {
        border: 1px solid rgba(255, 255, 255, 0.45);
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.72);
        box-shadow: 0 8px 22px rgba(31, 58, 95, 0.08);
        overflow: hidden;
    }
    .settings-card-header {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 16px 20px;
        border-bottom: 1px solid rgba(214, 232, 237, 0.75);
        background: rgba(255, 255, 255, 0.36);
        font-weight: 700;
    }
    .settings-icon {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        background: rgba(11, 95, 118, 0.12);
        color: #0b5f76;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 34px;
    }
    .settings-icon svg {
        width: 18px;
        height: 18px;
    }
    .settings-card-body {
        padding: 20px;
    }
    .settings-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 18px;
    }
    .btn-primary {
        background: linear-gradient(135deg, #0f6c85 0%, #1f8aa5 100%);
        border: 0;
    }
    @media (max-width: 767.98px) {
        .profile-shell {
            width: 100%;
            margin: 0 auto;
            padding: 16px;
            border-radius: 22px;
        }
    }
</style>

<div class="profile-shell">
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div class="h4 mb-0 fw-bold">My Profile</div>
</div>

    <div class="card card-shadow settings-card mb-4">
        <div class="settings-card-header">
            <span class="settings-icon">
                <svg viewBox="0 0 24 24" fill="none">
                    <path d="M20 20a8 8 0 1 0-16 0" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="1.8"/>
                </svg>
            </span>
            <span>Profile Information</span>
        </div>
        <div class="settings-card-body">
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('put')
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label">Username</label>
                        <input class="form-control" type="text" name="name" value="{{ old('name', $user->name) }}" required>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Email</label>
                        <input class="form-control" type="email" name="email" value="{{ old('email', $user->email) }}" required>
                    </div>
                </div>
                <div class="settings-actions">
                    <button class="btn btn-primary" type="submit">Edit Profile</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card card-shadow settings-card">
        <div class="settings-card-header">
            <span class="settings-icon">
                <svg viewBox="0 0 24 24" fill="none">
                    <path d="M7 11V8a5 5 0 0 1 10 0v3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    <path d="M6 11h12v9H6v-9Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                    <path d="M12 15v2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
            </span>
            <span>Security</span>
        </div>
        <div class="settings-card-body">
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('put')
                <input type="hidden" name="name" value="{{ old('name', $user->name) }}">
                <input type="hidden" name="email" value="{{ old('email', $user->email) }}">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label">New Password</label>
                        <input class="form-control" type="password" name="password" autocomplete="new-password">
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Confirm Password</label>
                        <input class="form-control" type="password" name="password_confirmation" autocomplete="new-password">
                    </div>
                </div>
                <div class="form-text mt-2">Leave password fields blank to keep your current password.</div>
                <div class="settings-actions">
                    <button class="btn btn-primary" type="submit">Update Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

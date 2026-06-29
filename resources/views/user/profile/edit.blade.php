@extends('user.layouts.app')

@section('title', 'My Profile')

@section('content')
<style>
    .profile-shell {
        max-width: 880px;
    }
    .settings-card {
        border: 1px solid #D6E8ED;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 4px 12px rgba(15, 87, 110, 0.08);
        overflow: hidden;
    }
    .settings-card-header {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 16px 20px;
        border-bottom: 1px solid #D6E8ED;
        background: #fff;
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
</style>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div class="h4 mb-0 fw-bold">My Profile</div>
</div>

<div class="profile-shell">
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

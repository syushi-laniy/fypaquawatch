@extends('admin.layouts.app')

@section('title', 'Edit User')
@section('page_title', 'Users')
@section('page_subtitle', 'Update user account details')

@section('content')
<div class="card card-shadow p-4">
    <div class="fw-semibold mb-3">Edit User</div>

    <form method="post" action="{{ route('users.update', $user) }}">
        @csrf
        @method('put')
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <label class="form-label">Name</label>
                <input class="form-control" type="text" name="name" value="{{ old('name', $user->name) }}" required>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label">Email</label>
                <input class="form-control" type="email" name="email" value="{{ old('email', $user->email) }}" required>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label">Role</label>
                <select class="form-select" name="role" required>
                    <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>User</option>
                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label">Password (leave blank to keep)</label>
                <input class="form-control" type="password" name="password">
            </div>
        </div>

        <div class="mt-3 d-flex gap-2">
            <button class="btn btn-primary" type="submit">Update</button>
            <a class="btn btn-outline-secondary" href="{{ route('users.index') }}">Cancel</a>
        </div>
    </form>
</div>
@endsection

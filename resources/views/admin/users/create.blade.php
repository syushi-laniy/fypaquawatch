@extends('admin.layouts.app')

@section('title', 'Add User')
@section('page_title', 'Users')
@section('page_subtitle', 'Create a new user account')

@section('content')
<div class="card card-shadow p-4">
    <div class="fw-semibold mb-3">Add User</div>

    <form method="post" action="{{ route('users.store') }}">
        @csrf
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <label class="form-label">Name</label>
                <input class="form-control" type="text" name="name" value="{{ old('name') }}" required>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label">Email</label>
                <input class="form-control" type="email" name="email" value="{{ old('email') }}" required>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label">Role</label>
                <select class="form-select" name="role" required>
                    <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>User</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label">Password</label>
                <input class="form-control" type="password" name="password" required>
            </div>
        </div>

        <div class="mt-3 d-flex gap-2">
            <button class="btn btn-primary" type="submit">Save</button>
            <a class="btn btn-outline-secondary" href="{{ route('users.index') }}">Cancel</a>
        </div>
    </form>
</div>
@endsection

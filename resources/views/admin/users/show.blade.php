@extends('admin.layouts.app')

@section('title', 'User Details')
@section('page_title', 'User Details')
@section('page_subtitle', 'Account overview and status')

@section('content')
<div class="card card-shadow p-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div class="fw-semibold">{{ $user->name }}</div>
        <a class="btn btn-sm btn-outline-secondary" href="{{ route('users.index') }}">Back to Users</a>
    </div>

    <div class="table-responsive">
        <table class="table table-borderless mb-0">
            <tbody>
                <tr>
                    <th class="text-muted">Name</th>
                    <td>{{ $user->name }}</td>
                </tr>
                <tr>
                    <th class="text-muted">Email</th>
                    <td>{{ $user->email }}</td>
                </tr>
                <tr>
                    <th class="text-muted">Role</th>
                    <td>{{ ucfirst($user->role) }}</td>
                </tr>
                <tr>
                    <th class="text-muted">Status</th>
                    <td><span class="badge text-bg-success">Active</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

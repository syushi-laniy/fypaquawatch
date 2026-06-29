@extends('admin.layouts.app')

@section('title', 'Orders')
@section('page_title', 'Orders')
@section('page_subtitle', 'Track payments and fulfillment status')

@section('content')
<div class="card card-shadow p-4">
    <div class="fw-semibold mb-3">Order List</div>
    <form class="row g-2 align-items-end mb-3" method="GET" action="{{ route('admin.orders.index') }}">
        <div class="col-12 col-md-4">
            <label class="form-label mb-1">Search</label>
            <input class="form-control" type="text" name="q" value="{{ request('q') }}"
                   placeholder="Order ID or customer">
        </div>
        <div class="col-12 col-md-3">
            <label class="form-label mb-1">Payment Status</label>
            <select class="form-select" name="status">
                <option value="">All</option>
                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
            </select>
        </div>
        <div class="col-12 col-md-3">
            <label class="form-label mb-1">Fulfillment</label>
            <select class="form-select" name="fulfillment">
                <option value="">All</option>
                <option value="pending" {{ request('fulfillment') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="fulfilled" {{ request('fulfillment') === 'fulfilled' ? 'selected' : '' }}>Fulfilled</option>
            </select>
        </div>
        <div class="col-12 col-md-2 d-flex gap-2">
            <button class="btn btn-primary w-100" type="submit">Filter</button>
            <a class="btn btn-outline-secondary w-100" href="{{ route('admin.orders.index') }}">Reset</a>
        </div>
    </form>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Fulfillment</th>
                    <th>Created</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>{{ $order->user->name ?? '-' }}</td>
                        <td>RM {{ number_format($order->total, 2) }}</td>
                        <td>
                            <span class="badge text-bg-{{ $order->status === 'paid' ? 'success' : ($order->status === 'failed' ? 'danger' : 'warning') }}">
                                {{ strtoupper($order->status) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge text-bg-{{ $order->fulfillment_status === 'fulfilled' ? 'success' : 'secondary' }}">
                                {{ strtoupper($order->fulfillment_status) }}
                            </span>
                        </td>
                        <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.orders.show', $order) }}">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">No orders yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

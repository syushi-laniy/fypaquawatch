@extends('admin.layouts.app')

@section('title', 'Order Details')
@section('page_title', 'Order Details')
@section('page_subtitle', 'Payment and fulfillment summary')

@section('content')
<div class="card card-shadow p-4 mb-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div class="fw-semibold">Order #{{ $order->id }}</div>
        <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.orders.index') }}">Back to Orders</a>
    </div>

    <div class="row g-3">
        <div class="col-12 col-md-6">
            <div class="small muted">Customer</div>
            <div class="fw-semibold">{{ $order->user->name ?? '-' }}</div>
            <div class="small muted mt-2">Status</div>
            <div>
                <span class="badge text-bg-{{ $order->status === 'paid' ? 'success' : ($order->status === 'failed' ? 'danger' : 'warning') }}">
                    {{ strtoupper($order->status) }}
                </span>
                <span class="badge text-bg-{{ $order->fulfillment_status === 'fulfilled' ? 'success' : 'secondary' }} ms-2">
                    {{ strtoupper($order->fulfillment_status) }}
                </span>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="small muted">Stripe Session ID</div>
            <div class="fw-semibold">{{ $order->stripe_session_id ?? '-' }}</div>
            <div class="small muted mt-2">Payment Intent ID</div>
            <div class="fw-semibold">{{ $order->payment_intent_id ?? '-' }}</div>
        </div>
    </div>

    <div class="mt-3">
        @if($order->fulfillment_status !== 'fulfilled')
            <form method="POST" action="{{ route('admin.orders.fulfill', $order) }}">
                @csrf
                <button class="btn btn-outline-success" type="submit" @disabled($order->status !== 'paid')>
                    Mark as Fulfilled/Shipped
                </button>
                @if($order->status !== 'paid')
                    <div class="small text-muted mt-2">Awaiting payment. Fulfillment is locked until paid.</div>
                @endif
            </form>
        @else
            <span class="badge text-bg-success">Order Fulfilled</span>
        @endif
    </div>
</div>

<div class="card card-shadow p-4">
    <div class="fw-semibold mb-3">Order Items</div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td>{{ $item->product->name ?? 'Product' }}</td>
                        <td>RM {{ number_format($item->price, 2) }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>RM {{ number_format($item->price * $item->quantity, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">No items.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-end mt-3">
        <div class="text-end">
            <div class="small muted">Total</div>
            <div class="h5 mb-0">RM {{ number_format($total, 2) }}</div>
        </div>
    </div>
</div>
@endsection

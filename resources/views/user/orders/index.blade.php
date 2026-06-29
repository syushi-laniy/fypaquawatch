@extends('user.layouts.app')

@section('title', 'My Receipts')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <div class="h4 mb-0 fw-bold">Orders & Receipts</div>
        <div class="small muted">View payment status and receipts.</div>
    </div>
    <a class="btn btn-outline-primary" href="{{ route('shop.index') }}">Continue Shopping</a>
</div>

<div class="card card-shadow p-3">
    @if($orders->isEmpty())
        <div class="text-center py-4">
            <div class="fw-semibold mb-1">No orders yet</div>
            <div class="small muted">Your receipts will appear here after payment.</div>
        </div>
    @else
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Paid At</th>
                        <th>Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>RM {{ number_format($order->total ?? 0, 2) }}</td>
                            <td>
                                @php
                                    $badge = $order->status === 'paid' ? 'text-bg-success' : 'text-bg-warning';
                                @endphp
                                <span class="badge {{ $badge }}">{{ strtoupper($order->status ?? 'PENDING') }}</span>
                            </td>
                            <td>{{ $order->paid_at ? $order->paid_at->format('Y-m-d H:i') : '-' }}</td>
                            <td>
                                @if($order->receipt_url)
                                    <a href="{{ $order->receipt_url }}" target="_blank" rel="noopener">View Receipt</a>
                                @else
                                    <span class="small muted">Not available</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection

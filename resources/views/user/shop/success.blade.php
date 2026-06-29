@extends('user.layouts.app')

@section('title', 'Payment Successful')

@section('content')
<div class="card card-shadow p-4 text-center">
    <div class="h4 fw-bold mb-2">Payment Successful</div>
    <div class="muted mb-3">Your order has been confirmed.</div>
    <div class="mb-3">Order ID: <span class="fw-semibold">#{{ $order->id }}</span></div>
    @if($order->receipt_url)
        <div class="mb-3">
            <a class="btn btn-outline-secondary btn-sm" href="{{ $order->receipt_url }}" target="_blank" rel="noopener">
                View Receipt
            </a>
        </div>
    @endif
    <a class="btn btn-primary" href="{{ route('shop.index') }}">Continue Shopping</a>
</div>
@endsection

@extends('user.layouts.app')

@section('title', 'Payment Cancelled')

@section('content')
<div class="card card-shadow p-4 text-center">
    <div class="h4 fw-bold mb-2">Payment Cancelled</div>
    <div class="muted mb-3">You can return to your cart and try again.</div>
    <a class="btn btn-outline-primary" href="{{ route('shop.index') }}#cart">Back to Cart</a>
</div>
@endsection

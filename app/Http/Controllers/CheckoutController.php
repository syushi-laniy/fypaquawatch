<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\OrderReceiptService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CheckoutController extends Controller
{
    public function create(Request $request)
    {
        $items = CartItem::with('product')
            ->where('user_id', $request->user()->id)
            ->get();

        if ($items->isEmpty()) {
            return redirect(route('shop.index') . '#cart')->withErrors(['cart' => 'Your cart is empty.']);
        }

        $addressId = $request->input('address_id');
        $addressQuery = Address::where('user_id', $request->user()->id);
        $address = $addressId ? $addressQuery->where('id', $addressId)->first()
            : $addressQuery->where('is_default', true)->first();
        if (!$address) {
            return redirect(route('shop.index') . '#addresses')
                ->withErrors(['cart' => 'Please add a billing address before checkout.']);
        }

        $total = $items->reduce(function ($sum, $item) {
            return $sum + ($item->product->price * $item->quantity);
        }, 0);

        $order = Order::create([
            'user_id' => $request->user()->id,
            'total' => $total,
            'status' => 'pending',
        ]);

        $lineItems = [];
        foreach ($items as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'myr',
                    'product_data' => [
                        'name' => $item->product->name,
                    ],
                    'unit_amount' => (int) round($item->product->price * 100),
                ],
                'quantity' => $item->quantity,
            ];
        }

        $secret = config('services.stripe.secret');
        if (!$secret) {
            return redirect(route('shop.index') . '#cart')->withErrors(['cart' => 'Stripe secret key not configured.']);
        }

        $payload = [
            'mode' => 'payment',
            'success_url' => route('checkout.success', ['order' => $order->id], true),
            'cancel_url' => route('checkout.cancel', ['order' => $order->id], true),
            'customer_email' => $request->user()->email,
        ];

        foreach ($lineItems as $index => $item) {
            $payload["line_items[{$index}][price_data][currency]"] = $item['price_data']['currency'];
            $payload["line_items[{$index}][price_data][product_data][name]"] = $item['price_data']['product_data']['name'];
            $payload["line_items[{$index}][price_data][unit_amount]"] = $item['price_data']['unit_amount'];
            $payload["line_items[{$index}][quantity]"] = $item['quantity'];
        }

        $response = Http::asForm()
            ->withBasicAuth($secret, '')
            ->post('https://api.stripe.com/v1/checkout/sessions', $payload);

        if (!$response->ok()) {
            return redirect(route('shop.index') . '#cart')->withErrors(['cart' => 'Failed to create checkout session.']);
        }

        $session = $response->json();
        $order->stripe_session_id = $session['id'] ?? null;
        $order->save();

        return redirect()->away($session['url']);
    }

    public function success(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            abort(403);
        }

        if ($order->status === 'paid') {
            $this->handleReceipt($order);
            return view('user.shop.success', compact('order'));
        }

        $secret = config('services.stripe.secret');
        if (!$secret || !$order->stripe_session_id) {
            return redirect(route('shop.index') . '#cart')->withErrors(['cart' => 'Payment verification failed.']);
        }

        $sessionResponse = Http::withBasicAuth($secret, '')
            ->get("https://api.stripe.com/v1/checkout/sessions/{$order->stripe_session_id}");

        if (!$sessionResponse->ok()) {
            return redirect(route('shop.index') . '#cart')->withErrors(['cart' => 'Payment verification failed.']);
        }

        $session = $sessionResponse->json();
        if (($session['payment_status'] ?? '') !== 'paid') {
            return redirect(route('shop.index') . '#cart')->withErrors(['cart' => 'Payment not completed.']);
        }

        $order->payment_intent_id = $session['payment_intent'] ?? null;
        $order->save();

        $this->finalizeOrder($order);
        $this->handleReceipt($order);

        return view('user.shop.success', compact('order'));
    }

    public function cancel(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            abort(403);
        }

        return view('user.shop.cancel', compact('order'));
    }

    private function finalizeOrder(Order $order): void
    {
        $items = OrderItem::where('order_id', $order->id)->get();

        if ($items->isEmpty()) {
            $cartItems = CartItem::with('product')
                ->where('user_id', $order->user_id)
                ->get();

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ]);

                $item->product->decrement('stock', $item->quantity);
            }

            CartItem::where('user_id', $order->user_id)->delete();
        }

        $order->status = 'paid';
        $order->save();
    }

    private function handleReceipt(Order $order): void
    {
        $service = new OrderReceiptService();
        $service->handlePaidOrder($order);
    }
}

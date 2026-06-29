<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\OrderReceiptService;
use Illuminate\Http\Request;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        if (!$secret || !$sigHeader) {
            return response('Missing webhook secret or signature.', 400);
        }

        if (!$this->verifySignature($payload, $sigHeader, $secret)) {
            return response('Invalid signature.', 400);
        }

        $event = json_decode($payload, true);
        if (!is_array($event)) {
            return response('Invalid payload.', 400);
        }

        if (($event['type'] ?? '') === 'checkout.session.completed') {
            $session = $event['data']['object'] ?? [];
            $sessionId = $session['id'] ?? null;
            if ($sessionId) {
                $order = Order::where('stripe_session_id', $sessionId)->first();
                if ($order && $order->status !== 'paid') {
                    $order->payment_intent_id = $session['payment_intent'] ?? null;
                    $order->save();
                    $this->finalizeOrder($order);
                    $this->handleReceipt($order);
                }
            }
        }

        return response('ok', 200);
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

    private function verifySignature(string $payload, string $sigHeader, string $secret): bool
    {
        $parts = explode(',', $sigHeader);
        $timestamp = null;
        $signatures = [];

        foreach ($parts as $part) {
            $kv = explode('=', $part, 2);
            if (count($kv) !== 2) {
                continue;
            }
            if ($kv[0] === 't') {
                $timestamp = $kv[1];
            }
            if ($kv[0] === 'v1') {
                $signatures[] = $kv[1];
            }
        }

        if (!$timestamp || empty($signatures)) {
            return false;
        }

        $signedPayload = $timestamp . '.' . $payload;
        $computed = hash_hmac('sha256', $signedPayload, $secret);

        foreach ($signatures as $sig) {
            if (hash_equals($sig, $computed)) {
                return true;
            }
        }

        return false;
    }
}

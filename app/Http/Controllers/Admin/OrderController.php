<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user');
        $search = trim((string) $request->query('q', ''));
        $status = $request->query('status');
        $fulfillment = $request->query('fulfillment');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                if (is_numeric($search)) {
                    $q->orWhere('id', (int) $search);
                }
                $q->orWhereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($fulfillment) {
            $query->where('fulfillment_status', $fulfillment);
        }

        $orders = $query->orderByDesc('id')->get();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('user');
        $items = OrderItem::with('product')
            ->where('order_id', $order->id)
            ->get();

        $total = $items->reduce(function ($sum, $item) {
            return $sum + ($item->price * $item->quantity);
        }, 0);

        return view('admin.orders.show', compact('order', 'items', 'total'));
    }

    public function fulfill(Request $request, Order $order)
    {
        if ($order->status !== 'paid') {
            return redirect()
                ->route('admin.orders.show', $order)
                ->with('error', 'Order is still pending payment. It cannot be fulfilled yet.');
        }

        $order->fulfillment_status = 'fulfilled';
        $order->save();

        $this->notifyUserFulfilled($order);

        return redirect()->route('admin.orders.show', $order)->with('success', 'Order marked as fulfilled.');
    }

    private function notifyUserFulfilled(Order $order): void
    {
        $order->loadMissing('user');
        $user = $order->user;
        if (!$user || !$user->telegram_chat_id) {
            return;
        }

        $token = config('services.telegram.token');
        if (!$token) {
            return;
        }

        $message = "Your order #{$order->id} has been fulfilled/shipped. Thank you for your purchase!";

        Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id' => $user->telegram_chat_id,
            'text' => $message,
        ]);
    }
}

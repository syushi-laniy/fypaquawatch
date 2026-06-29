<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::where('is_active', true)
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        $cartCounts = CartItem::where('user_id', $request->user()->id)
            ->get()
            ->keyBy('product_id');

        $items = CartItem::with('product')
            ->where('user_id', $request->user()->id)
            ->get();

        $total = $items->reduce(function ($sum, $item) {
            return $sum + ($item->product->price * $item->quantity);
        }, 0);

        $addresses = Address::where('user_id', $request->user()->id)
            ->orderByDesc('is_default')
            ->orderByDesc('id')
            ->get();

        $orders = Order::where('user_id', $request->user()->id)
            ->orderByDesc('id')
            ->get();

        return view('user.shop.index', compact('products', 'cartCounts', 'items', 'total', 'addresses', 'orders'));
    }
}

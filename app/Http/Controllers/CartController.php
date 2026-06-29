<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $items = CartItem::with('product')
            ->where('user_id', $request->user()->id)
            ->get();

        $addresses = \App\Models\Address::where('user_id', $request->user()->id)
            ->orderByDesc('is_default')
            ->orderByDesc('id')
            ->get();

        $total = $items->reduce(function ($sum, $item) {
            return $sum + ($item->product->price * $item->quantity);
        }, 0);

        return view('user.cart.index', compact('items', 'total', 'addresses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $product = Product::findOrFail($data['product_id']);
        if ($product->stock < 1) {
            return back()->withErrors(['cart' => 'This item is out of stock.']);
        }

        $quantity = (int) ($data['quantity'] ?? 1);
        $quantity = min($quantity, $product->stock);

        $item = CartItem::firstOrNew([
            'user_id' => $request->user()->id,
            'product_id' => $product->id,
        ]);

        $item->quantity = min(($item->quantity ?? 0) + $quantity, $product->stock);
        $item->save();

        return redirect()->route('shop.index')->with('success', 'Added to cart.');
    }

    public function update(Request $request, CartItem $item)
    {
        if ($item->user_id !== $request->user()->id) {
            abort(403);
        }

        $data = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $max = $item->product->stock;
        $item->quantity = min($data['quantity'], $max);
        $item->save();

        return redirect(route('shop.index') . '#cart')->with('success', 'Cart updated.');
    }

    public function destroy(Request $request, CartItem $item)
    {
        if ($item->user_id !== $request->user()->id) {
            abort(403);
        }

        $item->delete();

        return redirect(route('shop.index') . '#cart')->with('success', 'Item removed.');
    }
}

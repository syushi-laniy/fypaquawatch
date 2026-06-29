<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index(Request $request)
    {
        $addresses = Address::where('user_id', $request->user()->id)
            ->orderByDesc('is_default')
            ->orderByDesc('id')
            ->get();

        return view('user.addresses.index', compact('addresses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'is_default' => 'nullable|boolean',
        ]);

        if (!empty($data['is_default'])) {
            Address::where('user_id', $request->user()->id)->update(['is_default' => false]);
        }

        Address::create($data + ['user_id' => $request->user()->id]);

        return redirect(route('shop.index') . '#addresses')->with('success', 'Address saved.');
    }

    public function update(Request $request, Address $address)
    {
        if ($address->user_id !== $request->user()->id) {
            abort(403);
        }

        $data = $request->validate([
            'is_default' => 'required|boolean',
        ]);

        if (!empty($data['is_default'])) {
            Address::where('user_id', $request->user()->id)->update(['is_default' => false]);
        }

        $address->update(['is_default' => $data['is_default']]);

        return redirect(route('shop.index') . '#addresses')->with('success', 'Default address updated.');
    }
}

@extends('user.layouts.app')

@section('title', 'Shop')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <div class="h4 mb-0 fw-bold">Shop</div>
        <div class="small muted">Products, cart, checkout details, and receipts in one place.</div>
    </div>
</div>

<div class="card card-shadow p-2 mb-3">
    <div class="nav nav-pills gap-2" role="tablist">
        <a class="nav-link active" href="#products">Products</a>
        <a class="nav-link" href="#cart">Cart</a>
        <a class="nav-link" href="#addresses">Addresses</a>
        <a class="nav-link" href="#orders">Orders</a>
    </div>
</div>

<section id="products" class="mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <div class="h5 mb-0 fw-bold">Products</div>
            <div class="small muted">Fish food, water care, and equipment for your tanks.</div>
        </div>
        <a class="btn btn-outline-primary" href="#cart">View Cart</a>
    </div>

    <div class="row g-3">
        @forelse($products as $product)
            @php
                $cartItem = $cartCounts->get($product->id);
                $currentQty = $cartItem ? $cartItem->quantity : 0;
                $lowStock = $product->stock <= $product->low_stock_threshold;
            @endphp
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card card-shadow p-3 h-100">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <div class="fw-semibold">{{ $product->name }}</div>
                            <div class="small muted">{{ $product->category ?? 'General' }}</div>
                        </div>
                        @if($lowStock)
                            <span class="badge text-bg-warning">Low stock ({{ $product->stock }})</span>
                        @else
                            <span class="badge text-bg-success">Stock {{ $product->stock }}</span>
                        @endif
                    </div>

                    <div class="h5 mb-3">RM {{ number_format($product->price, 2) }}</div>

                    <div class="small muted mb-2">
                        In cart: <span class="fw-semibold">{{ $currentQty }}</span>
                    </div>

                    <form method="POST" action="{{ route('cart.store') }}">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div class="d-flex gap-2 align-items-center">
                            <input class="form-control" type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}">
                            <button class="btn btn-primary" type="submit" {{ $product->stock < 1 ? 'disabled' : '' }}>
                                Add
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card card-shadow p-4 text-center text-muted">No products available.</div>
            </div>
        @endforelse
    </div>
</section>

<section id="cart" class="mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <div class="h5 mb-0 fw-bold">Cart</div>
            <div class="small muted">Adjust quantities and review your order.</div>
        </div>
        <a class="btn btn-outline-secondary" href="#products">Continue Shopping</a>
    </div>

    <div class="card card-shadow p-4 mb-3">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Item</th>
                        <th>Price</th>
                        <th style="width: 160px;">Quantity</th>
                        <th>Total</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $item->product->name }}</div>
                                <div class="small muted">{{ $item->product->category ?? 'General' }}</div>
                            </td>
                            <td>RM {{ number_format($item->product->price, 2) }}</td>
                            <td>
                                <form method="POST" action="{{ route('cart.update', $item) }}" class="d-flex gap-2">
                                    @csrf
                                    @method('put')
                                    <input class="form-control" type="number" name="quantity"
                                           value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock }}">
                                    <button class="btn btn-outline-primary btn-sm" type="submit">Update</button>
                                </form>
                            </td>
                            <td>RM {{ number_format($item->product->price * $item->quantity, 2) }}</td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('cart.destroy', $item) }}">
                                    @csrf
                                    @method('delete')
                                    <button class="btn btn-outline-danger btn-sm" type="submit">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Your cart is empty.</td>
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

    <div class="card card-shadow p-4">
        <div class="fw-semibold mb-2">Billing Address</div>
        @if($addresses->isEmpty())
            <div class="text-muted">No addresses saved yet. Add one below.</div>
        @else
            <form method="POST" action="{{ route('checkout.create') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Choose Address</label>
                    <select class="form-select" name="address_id" required>
                        @foreach($addresses as $address)
                            <option value="{{ $address->id }}" {{ $address->is_default ? 'selected' : '' }}>
                                {{ $address->name }} - {{ $address->address_line1 }}, {{ $address->city }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button class="btn btn-primary" type="submit" {{ $items->isEmpty() ? 'disabled' : '' }}>
                    Pay with Stripe
                </button>
            </form>
        @endif
    </div>
</section>

<section id="addresses" class="mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <div class="h5 mb-0 fw-bold">Addresses</div>
            <div class="small muted">Save your billing/shipping details for faster checkout.</div>
        </div>
    </div>

    <div class="card card-shadow p-4 mb-3">
        <div class="fw-semibold mb-3">Saved Addresses</div>
        <div class="row g-3">
            @forelse($addresses as $address)
                <div class="col-12 col-md-6">
                    <div class="border rounded-4 p-3 h-100">
                        <div class="fw-semibold">{{ $address->name }}</div>
                        <div class="small muted">{{ $address->phone }}</div>
                        <div class="mt-2 small">
                            {{ $address->address_line1 }}<br>
                            @if($address->address_line2){{ $address->address_line2 }}<br>@endif
                            {{ $address->postal_code }} {{ $address->city }}<br>
                            {{ $address->state }} {{ $address->country }}
                        </div>
                        <div class="mt-2">
                            @if($address->is_default)
                                <span class="badge text-bg-success">Default</span>
                            @else
                                <form method="POST" action="{{ route('addresses.update', $address) }}">
                                    @csrf
                                    @method('put')
                                    <input type="hidden" name="is_default" value="1">
                                    <button class="btn btn-sm btn-outline-primary" type="submit">Set Default</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-muted">No addresses saved yet.</div>
            @endforelse
        </div>
    </div>

    <div class="card card-shadow p-4">
        <div class="fw-semibold mb-1">Add Address</div>
        <div class="small muted mb-3">Add a new address for future checkouts.</div>
        <form method="POST" action="{{ route('addresses.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label">Full Name</label>
                    <input class="form-control" type="text" name="name" required>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Phone</label>
                    <input class="form-control" type="text" name="phone" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Address Line 1</label>
                    <input class="form-control" type="text" name="address_line1" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Address Line 2 (optional)</label>
                    <input class="form-control" type="text" name="address_line2">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label">Country</label>
                    <select class="form-select" name="country" id="country-select" required>
                        <option value="Malaysia">Malaysia</option>
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label">State / Region</label>
                    <select class="form-select" name="state" id="state-select" required></select>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label">City</label>
                    <select class="form-select" name="city" id="city-select" required></select>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label">Postal Code</label>
                    <input class="form-control" type="text" name="postal_code" required>
                </div>
                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_default" value="1" id="default-address">
                        <label class="form-check-label" for="default-address">
                            Set as default
                        </label>
                    </div>
                </div>
            </div>
            <div class="mt-3">
                <button class="btn btn-primary" type="submit">Save Address</button>
            </div>
        </form>
    </div>
</section>

<section id="orders" class="mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <div class="h5 mb-0 fw-bold">Orders & Receipts</div>
            <div class="small muted">View payment status and receipts.</div>
        </div>
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
</section>

<script>
    const stateSelect = document.getElementById('state-select');
    const citySelect = document.getElementById('city-select');
    const states = {
        'Johor': ['Johor Bahru', 'Batu Pahat', 'Kluang', 'Kota Tinggi', 'Muar', 'Pontian', 'Segamat', 'Mersing', 'Tangkak'],
        'Kedah': ['Alor Setar', 'Sungai Petani', 'Kulim', 'Langkawi', 'Kubang Pasu', 'Baling', 'Pendang'],
        'Kelantan': ['Kota Bharu', 'Pasir Mas', 'Tumpat', 'Tanah Merah', 'Machang', 'Kuala Krai', 'Gua Musang'],
        'Kuala Lumpur': ['Kuala Lumpur'],
        'Labuan': ['Victoria'],
        'Melaka': ['Melaka City', 'Alor Gajah', 'Jasin'],
        'Negeri Sembilan': ['Seremban', 'Port Dickson', 'Nilai', 'Jempol', 'Kuala Pilah', 'Rembau', 'Tampin'],
        'Pahang': ['Kuantan', 'Temerloh', 'Bentong', 'Jerantut', 'Cameron Highlands', 'Maran', 'Pekan', 'Raub'],
        'Penang': ['George Town', 'Bukit Mertajam', 'Bayan Lepas', 'Butterworth', 'Balik Pulau'],
        'Perak': ['Ipoh', 'Taiping', 'Manjung', 'Teluk Intan', 'Kuala Kangsar', 'Sitiawan', 'Batu Gajah'],
        'Perlis': ['Kangar', 'Arau', 'Padang Besar'],
        'Putrajaya': ['Putrajaya'],
        'Sabah': ['Kota Kinabalu', 'Sandakan', 'Tawau', 'Lahad Datu', 'Keningau', 'Semporna'],
        'Sarawak': ['Kuching', 'Miri', 'Sibu', 'Bintulu', 'Kapit', 'Sri Aman'],
        'Selangor': ['Shah Alam', 'Petaling Jaya', 'Subang Jaya', 'Klang', 'Gombak', 'Kajang', 'Puchong', 'Sepang'],
        'Terengganu': ['Kuala Terengganu', 'Kemaman', 'Dungun', 'Marang', 'Besut', 'Setiu'],
    };

    function populateStates() {
        stateSelect.innerHTML = '';
        Object.keys(states).forEach((state) => {
            const option = document.createElement('option');
            option.value = state;
            option.textContent = state;
            stateSelect.appendChild(option);
        });
        populateCities();
    }

    function populateCities() {
        const selected = stateSelect.value;
        citySelect.innerHTML = '';
        (states[selected] || []).forEach((city) => {
            const option = document.createElement('option');
            option.value = city;
            option.textContent = city;
            citySelect.appendChild(option);
        });
    }

    stateSelect.addEventListener('change', populateCities);
    populateStates();
</script>
@endsection

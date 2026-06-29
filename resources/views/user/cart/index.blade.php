@extends('user.layouts.app')

@section('title', 'Cart')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <div class="h4 mb-0 fw-bold">Your Cart</div>
        <div class="small muted">Adjust quantities and review your order.</div>
    </div>
    <a class="btn btn-outline-secondary" href="{{ route('shop.index') }}">Continue Shopping</a>
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

<div class="card card-shadow p-4 mb-3">
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

<div class="card card-shadow p-4">
    <div class="fw-semibold mb-2">Add New Address</div>
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
                <select class="form-select" name="country" id="cart-country" required>
                    <option value="Malaysia">Malaysia</option>
                </select>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label">State / Region</label>
                <select class="form-select" name="state" id="cart-state" required></select>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label">City</label>
                <select class="form-select" name="city" id="cart-city" required></select>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label">Postal Code</label>
                <input class="form-control" type="text" name="postal_code" required>
            </div>
            <div class="col-12">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_default" value="1" id="cart-default">
                    <label class="form-check-label" for="cart-default">
                        Set as default
                    </label>
                </div>
            </div>
        </div>
        <div class="mt-3">
            <button class="btn btn-outline-primary" type="submit">Save Address</button>
        </div>
    </form>
</div>

<script>
    const cartState = document.getElementById('cart-state');
    const cartCity = document.getElementById('cart-city');
    const cartStates = {
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

    function populateCartStates() {
        cartState.innerHTML = '';
        Object.keys(cartStates).forEach((state) => {
            const option = document.createElement('option');
            option.value = state;
            option.textContent = state;
            cartState.appendChild(option);
        });
        populateCartCities();
    }

    function populateCartCities() {
        const selected = cartState.value;
        cartCity.innerHTML = '';
        (cartStates[selected] || []).forEach((city) => {
            const option = document.createElement('option');
            option.value = city;
            option.textContent = city;
            cartCity.appendChild(option);
        });
    }

    cartState.addEventListener('change', populateCartCities);
    populateCartStates();
</script>
@endsection

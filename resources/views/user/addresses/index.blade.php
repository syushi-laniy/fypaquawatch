@extends('user.layouts.app')

@section('title', 'My Addresses')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <div class="h4 mb-0 fw-bold">My Addresses</div>
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

<div class="row g-3">
    <div class="col-12">
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
    </div>
</div>

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

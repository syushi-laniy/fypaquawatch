@extends('user.layouts.app')

@section('title', 'Request New Tank')

@section('content')
<style>
    .form-card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 16px;
        box-shadow: 0 10px 26px rgba(31, 58, 95, 0.08);
        border: 1px solid rgba(7, 59, 76, 0.06);
    }
    .section-title {
        font-weight: 600;
        color: #0f1f26;
    }
    .btn-primary {
        background: linear-gradient(135deg, #0f6c85 0%, #1f8aa5 100%);
        border: 0;
    }
</style>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <div class="h5 mb-1 fw-semibold">Request New Tank</div>
        <div class="small muted">Submit your tank details for admin setup.</div>
    </div>
    <a class="btn btn-outline-secondary btn-sm" href="{{ route('tanks.index') }}">Back to Tanks</a>
</div>

<div class="form-card p-4">
    <div class="section-title mb-3">Tank Request Details</div>

    <form method="post" action="{{ route('tank-requests.store') }}">
        @csrf
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <label class="form-label">Tank Name</label>
                <input class="form-control" type="text" name="tank_name" value="{{ old('tank_name') }}" required>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label">Tank Size</label>
                <input class="form-control" type="text" name="tank_size" value="{{ old('tank_size') }}" placeholder="Example: 60 L" required>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label">Fish Species <span class="text-muted">(optional)</span></label>
                <input class="form-control" type="text" name="fish_species" value="{{ old('fish_species') }}">
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label">Phone Number</label>
                <input class="form-control" type="text" name="phone_number" value="{{ old('phone_number') }}" required>
            </div>
            <div class="col-12">
                <label class="form-label">Delivery Address</label>
                <textarea class="form-control" name="delivery_address" rows="3" required>{{ old('delivery_address') }}</textarea>
            </div>
            <div class="col-12">
                <label class="form-label">Additional Notes <span class="text-muted">(optional)</span></label>
                <textarea class="form-control" name="additional_notes" rows="3">{{ old('additional_notes') }}</textarea>
            </div>
        </div>

        <div class="mt-4 d-flex flex-wrap gap-2">
            <button class="btn btn-primary" type="submit">Submit Request</button>
            <a class="btn btn-outline-secondary" href="{{ route('tanks.index') }}">Cancel</a>
        </div>
    </form>
</div>
@endsection

@extends('user.layouts.app')

@section('title', 'Species Selection')

@section('content')
<style>
    .species-card {
        background: #fff;
        border-color: #D6E8ED !important;
        box-shadow: 0 4px 12px rgba(15, 87, 110, 0.08);
        cursor: pointer;
        transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
    }
    .species-card:hover {
        border-color: #9FC9D3 !important;
        box-shadow: 0 6px 16px rgba(15, 87, 110, 0.12);
        transform: translateY(-1px);
    }
    .species-image-wrapper {
        position: relative;
        width: 100%;
        height: 140px;
        overflow: hidden;
        border-radius: 12px;
        background: #f1f5f9;
    }
    .species-section-card {
        background: #fff;
        border: 1px solid #D6E8ED;
        box-shadow: 0 4px 12px rgba(15, 87, 110, 0.08);
    }
    .selected-summary-card {
        padding: 1rem !important;
    }
    .selected-summary-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 0.75rem;
    }
    .selected-species-item {
        background: #F8FCFD;
        border-color: #D6E8ED !important;
    }
    .selected-species-item {
        min-height: 64px;
    }
    .selected-species-chip {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.55rem 0.7rem;
    }
    .ph-range-badge {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        background: #fff;
        border: 1px solid #D6E8ED;
        color: #0b5f76;
        font-size: 0.78rem;
        font-weight: 700;
        padding: 0.24rem 0.55rem;
        white-space: nowrap;
    }
    .species-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        display: block;
    }
    .selected-species-image {
        width: 54px;
        height: 44px;
        flex: 0 0 54px;
        overflow: hidden;
        border-radius: 8px;
        background: #f1f5f9;
    }
    .species-image-check {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 30px;
        height: 30px;
        margin: 0;
        border: 1px solid rgba(11, 95, 118, 0.18);
        border-radius: 9px;
        background-color: rgba(255, 255, 255, 0.9);
        box-shadow: 0 4px 12px rgba(15, 87, 110, 0.10);
        cursor: pointer;
    }
    .species-image-check:checked {
        background-color: #1f8aa5;
        border-color: #1f8aa5;
    }
    .species-actions {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        margin-top: 1rem;
    }
    .btn-primary {
        background: linear-gradient(135deg, #0f6c85 0%, #1f8aa5 100%);
        border: 0;
    }
    .btn-reset-selection {
        border: 0;
        background: transparent;
        color: #52656d;
        font-weight: 700;
    }
    .btn-reset-selection:hover,
    .btn-reset-selection:focus {
        color: #0b5f76;
    }
</style>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <div class="h4 mb-0 fw-bold">Species Selection</div>
        <div class="small muted">Save the species currently inside the selected tank.</div>
    </div>
</div>

@if(!$tank)
    <div class="card card-shadow p-4">
        <div class="fw-semibold mb-1">No tank selected</div>
        <div class="small muted mb-3">Request or select a tank before saving species.</div>
        <a class="btn btn-primary" href="{{ route('tank-requests.create') }}">Request New Tank</a>
    </div>
@else
    <div class="card card-shadow mb-3 species-section-card selected-summary-card">
        <div class="selected-summary-header">
            <div class="fw-semibold">Selected Species for This Tank</div>
            @if($recommendedRange)
                @if($recommendedRange['compatible'])
                    <span class="ph-range-badge">
                        Shared pH {{ number_format($recommendedRange['min'], 1) }} - {{ number_format($recommendedRange['max'], 1) }}
                    </span>
                @else
                    <span class="badge text-bg-warning">No shared pH range</span>
                @endif
            @endif
        </div>
        @if($selectedSpecies->isEmpty())
            <div class="small muted">No species saved for this tank yet.</div>
        @else
            <div class="row g-2">
                @foreach($selectedSpecies as $item)
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="border rounded-3 selected-species-item selected-species-chip h-100">
                            <div class="selected-species-image">
                                <img src="{{ asset($item->image_path ?: 'images/species/guppy.jpeg') }}"
                                     alt="{{ $item->name }}"
                                     class="species-image">
                            </div>
                            <div class="min-w-0">
                                <div class="fw-semibold">{{ $item->name }}</div>
                                <span class="ph-range-badge">pH {{ number_format($item->min_ph, 1) }} - {{ number_format($item->max_ph, 1) }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="card card-shadow p-4 species-section-card">
        <div class="fw-semibold mb-3">Choose Species</div>
        <form method="POST" action="{{ route('species.update') }}" id="species-selection-form">
            @csrf
            <div class="row g-2">
                @foreach($species as $item)
                    <div class="col-12 col-md-6 col-xl-4">
                        <label class="species-card border rounded-3 p-3 d-flex flex-column gap-2 h-100">
                            <div class="species-image-wrapper">
                                <img src="{{ asset($item->image_path ?: 'images/species/guppy.jpeg') }}"
                                     alt="{{ $item->name }}"
                                     class="species-image">
                                <input class="form-check-input species-image-check" type="checkbox" name="species_ids[]"
                                       value="{{ $item->id }}" {{ in_array($item->id, $selectedIds, true) ? 'checked' : '' }}
                                       aria-label="Select {{ $item->name }}">
                            </div>
                            <span>
                                <span class="fw-semibold d-block">{{ $item->name }}</span>
                                <span class="small muted">pH {{ number_format($item->min_ph, 1) }} - {{ number_format($item->max_ph, 1) }}</span>
                            </span>
                        </label>
                    </div>
                @endforeach
            </div>
            <div class="species-actions">
                <button class="btn btn-reset-selection" type="button" id="reset-species-selection">Reset Selection</button>
                <button class="btn btn-primary" type="submit">Save Species</button>
            </div>
        </form>
    </div>
@endif
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const resetButton = document.getElementById('reset-species-selection');
        const form = document.getElementById('species-selection-form');

        if (!resetButton || !form) return;

        resetButton.addEventListener('click', () => {
            form.querySelectorAll('input[name="species_ids[]"]').forEach((checkbox) => {
                checkbox.checked = false;
            });
        });
    });
</script>
@endsection

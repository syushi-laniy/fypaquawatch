@extends('user.layouts.app')

@section('title', 'Community Calculator')

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
    .species-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        display: block;
    }
    .selected-species-image {
        width: 76px;
        height: 58px;
        flex: 0 0 76px;
        overflow: hidden;
        border-radius: 10px;
        background: #f1f5f9;
    }
    .result-species-list {
        max-height: 320px;
        overflow-y: auto;
    }
    .community-section-card {
        background: #fff;
        border: 1px solid #D6E8ED;
        box-shadow: 0 4px 12px rgba(15, 87, 110, 0.08);
    }
    .btn-primary {
        background: linear-gradient(135deg, #0f6c85 0%, #1f8aa5 100%);
        border: 0;
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
    .community-actions {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        margin-top: 1rem;
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
        <div class="h4 mb-0 fw-bold">Community Calculator</div>
        <div class="small muted">Temporarily check species compatibility before saving them to a tank.</div>
    </div>
</div>

<div class="card card-shadow p-4 community-section-card">
    <div class="fw-semibold mb-3">Select Species to Check</div>
    <form method="POST" action="{{ route('community.calculate') }}" id="community-calculator-form">
        @csrf
        <div class="row g-2">
            @foreach($species as $item)
                <div class="col-12 col-md-4">
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
        <div class="community-actions">
            <button class="btn btn-reset-selection" type="button" id="reset-community-selection">Reset Selection</button>
            <button class="btn btn-primary" type="submit">Calculate</button>
        </div>
    </form>
</div>

@if($result)
    <div class="modal fade" id="communityResultModal" tabindex="-1" aria-labelledby="communityResultTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header">
                    <h5 class="modal-title" id="communityResultTitle">Compatibility Result</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if($result['compatible'])
                        <span class="badge text-bg-success mb-2">Compatible</span>
                        <div class="fw-semibold">
                            Recommended pH range:
                            {{ number_format($result['min'], 1) }} - {{ number_format($result['max'], 1) }}
                        </div>
                    @else
                        <span class="badge text-bg-warning mb-2">Not Compatible</span>
                        <div class="fw-semibold">{{ $result['message'] }}</div>
                    @endif

                    <div class="result-species-list mt-3 d-flex flex-column gap-2">
                        @foreach($selectedSpecies as $item)
                            <div class="border rounded-3 p-3 d-flex gap-3 align-items-center">
                                <div class="selected-species-image">
                                    <img src="{{ asset($item->image_path ?: 'images/species/guppy.jpeg') }}"
                                         alt="{{ $item->name }}"
                                         class="species-image">
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $item->name }}</div>
                                    <div class="small muted">pH {{ number_format($item->min_ph, 1) }} - {{ number_format($item->max_ph, 1) }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer d-block">
                    <form method="POST" action="{{ route('community.apply') }}">
                        @csrf
                        @foreach($selectedIds as $id)
                            <input type="hidden" name="species_ids[]" value="{{ $id }}">
                        @endforeach
                        <button class="btn btn-outline-primary w-100" type="submit" {{ $result['compatible'] ? '' : 'disabled' }}>
                            Apply to Current Tank
                        </button>
                        @if(!$result['compatible'])
                            <div class="small text-warning mt-2">Cannot save because selected species do not share a common pH range.</div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const resultModal = new bootstrap.Modal(document.getElementById('communityResultModal'));
            resultModal.show();
        });
    </script>
@endif
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const resetButton = document.getElementById('reset-community-selection');
        const form = document.getElementById('community-calculator-form');

        if (!resetButton || !form) return;

        resetButton.addEventListener('click', () => {
            form.querySelectorAll('input[name="species_ids[]"]').forEach((checkbox) => {
                checkbox.checked = false;
            });
        });
    });
</script>
@endsection

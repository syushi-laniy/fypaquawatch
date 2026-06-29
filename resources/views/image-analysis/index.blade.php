@extends('user.layouts.app')

@section('title', 'Image Analysis')

@section('content')
<style>
    .analysis-page {
        max-width: 980px;
        margin: 0 auto;
    }
    .analysis-heading {
        text-align: center;
        margin-bottom: 2rem;
    }
    .analysis-heading h1 {
        font-size: clamp(2rem, 4vw, 2.8rem);
        font-weight: 800;
        letter-spacing: 0;
        margin-bottom: 0.75rem;
        color: #0f1f26;
    }
    .analysis-heading p {
        margin: 0;
        color: #52656d;
        font-size: 1rem;
    }
    .usage-card {
        max-width: 680px;
        margin: 0 auto 1.25rem;
        border: 1px solid #D6E8ED;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 4px 12px rgba(15, 87, 110, 0.08);
        padding: 1rem 1.15rem;
    }
    .usage-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 0.35rem;
    }
    .usage-title {
        font-weight: 800;
        color: #0f1f26;
    }
    .usage-count {
        color: #0f6c85;
        font-weight: 800;
    }
    .usage-remaining {
        color: #52656d;
        font-size: 0.88rem;
        margin-bottom: 0.7rem;
    }
    .usage-progress {
        height: 8px;
        overflow: hidden;
        border-radius: 999px;
        background: #E5EEF1;
    }
    .usage-progress-bar {
        height: 100%;
        border-radius: inherit;
        background: #0f6c85;
        transition: width .25s ease;
    }
    .analysis-card {
        border: 1px solid #D6E8ED;
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 4px 12px rgba(15, 87, 110, 0.08);
        padding: clamp(1rem, 3vw, 1.6rem);
    }
    .upload-zone {
        min-height: 270px;
        border: 2px dashed #B9DCE4;
        border-radius: 16px;
        background: #F8FCFD;
        display: grid;
        place-items: center;
        padding: 2rem;
        text-align: center;
        transition: border-color .2s ease, background .2s ease;
    }
    .upload-zone.is-dragging {
        border-color: #0f6c85;
        background: #F2FAFC;
    }
    .upload-zone.is-disabled {
        cursor: not-allowed;
        opacity: 0.62;
    }
    .upload-icon {
        width: 72px;
        height: 72px;
        margin: 0 auto 1rem;
        border-radius: 18px;
        color: #0f6c85;
        background: rgba(15, 108, 133, 0.1);
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .upload-icon svg {
        width: 38px;
        height: 38px;
    }
    .upload-title {
        font-size: 1.15rem;
        font-weight: 700;
        margin-bottom: 0.45rem;
    }
    .upload-note {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 1.2rem;
    }
    .btn-aqua {
        border: 0;
        border-radius: 999px;
        background: #0f6c85;
        color: #fff;
        font-weight: 700;
        padding: 0.72rem 1.45rem;
    }
    .btn-aqua:hover,
    .btn-aqua:focus {
        background: #0b5f76;
        color: #fff;
    }
    .btn-aqua:disabled {
        background: #C9DDE3;
        color: #6f858d;
    }
    .file-preview-row {
        max-width: 560px;
        margin: 1rem auto 0;
        border: 1px solid #D6E8ED;
        border-radius: 12px;
        background: #fff;
        padding: 0.55rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .file-preview-row img {
        width: 54px;
        height: 44px;
        object-fit: contain;
        border-radius: 9px;
        flex: 0 0 54px;
        background: #f8fafc;
    }
    .file-name {
        min-width: 0;
        flex: 1;
        font-weight: 600;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .delete-file {
        border: 0;
        background: transparent;
        color: #7f1d1d;
        font-weight: 700;
        padding: 0.25rem 0.4rem;
    }
    .loading-state {
        min-height: 360px;
        display: grid;
        place-items: center;
        text-align: center;
    }
    .spinner-aqua {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        border: 4px solid #D6E8ED;
        border-top-color: #0f6c85;
        animation: spin .8s linear infinite;
        margin: 0 auto 1rem;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    .result-grid {
        display: grid;
        grid-template-columns: minmax(0, 0.95fr) minmax(0, 1.05fr);
        gap: 1.2rem;
        align-items: stretch;
    }
    .uploaded-panel,
    .result-panel {
        border: 1px solid #D6E8ED;
        border-radius: 16px;
        background: #fff;
        padding: 1rem;
    }
    .image-preview-container {
        width: 100%;
        min-height: 450px;
        max-height: 500px;
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 13px;
        border: 1px solid #E5EEF1;
        background: #f8fafc;
        overflow: hidden;
    }
    .image-preview-container img {
        max-width: 100%;
        max-height: 500px;
        width: auto;
        height: auto;
        object-fit: contain;
        display: block;
    }
    .back-button {
        border: 0;
        background: transparent;
        color: #0f6c85;
        font-weight: 700;
        padding: 0;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        margin-bottom: 0.9rem;
    }
    .result-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }
    .status-badge {
        border-radius: 999px;
        padding: 0.36rem 0.72rem;
        font-size: 0.8rem;
        font-weight: 800;
    }
    .status-healthy {
        background: #E8F7EF;
        color: #187044;
        border: 1px solid #8FD3AA;
    }
    .status-warning {
        background: #FFF7D8;
        color: #8A6500;
        border: 1px solid #F0D36A;
    }
    .result-block {
        border-top: 1px solid #E5EEF1;
        padding-top: 0.9rem;
        margin-top: 0.9rem;
    }
    .result-label {
        color: #52656d;
        font-size: 0.78rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 0.35rem;
    }
    .result-list {
        margin: 0;
        padding-left: 1.1rem;
    }
    .result-list li + li {
        margin-top: 0.3rem;
    }
    .confidence-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 68px;
        border-radius: 12px;
        background: #0f6c85;
        color: #fff;
        font-weight: 800;
        padding: 0.45rem 0.7rem;
    }
    .result-actions {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.65rem;
        margin-top: 1rem;
    }
    .result-actions .btn {
        width: 100%;
    }
    .result-actions .is-active {
        background: #0f6c85;
        color: #fff;
        border-color: #0f6c85;
    }
    .detail-panel {
        border: 1px solid #D6E8ED;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 4px 12px rgba(15, 87, 110, 0.08);
        padding: clamp(1rem, 3vw, 1.35rem);
        margin-top: 1rem;
    }
    .guide-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
    }
    .guide-section {
        border: 1px solid #E5EEF1;
        border-radius: 12px;
        background: #F8FCFD;
        padding: 0.95rem;
    }
    .guide-section h3 {
        color: #0f1f26;
        font-size: 1rem;
        font-weight: 800;
        margin-bottom: 0.55rem;
    }
    .btn-outline-aqua {
        border: 1px solid #0f6c85;
        border-radius: 999px;
        background: #fff;
        color: #0f6c85;
        font-weight: 800;
        padding: 0.68rem 1rem;
        text-decoration: none;
    }
    .btn-outline-aqua:hover,
    .btn-outline-aqua:focus {
        background: #F2FAFC;
        color: #0b5f76;
    }
    .low-confidence-warning {
        border: 1px solid #F0D36A;
        border-radius: 12px;
        background: #FFF9E8;
        color: #7A5A00;
        padding: 0.8rem 0.9rem;
        font-weight: 700;
    }
    .nearby-shops-panel {
        width: 100%;
    }
    .nearby-shops-toolbar {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.75rem;
        margin-bottom: 0.8rem;
    }
    .nearby-search-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto auto auto;
        gap: 0.65rem;
        align-items: center;
    }
    .nearby-search-row .form-control,
    .nearby-search-row .form-select {
        min-height: 42px;
    }
    .nearby-search-row .form-control::placeholder {
        color: #9aa8ae;
        opacity: 1;
    }
    .nearby-map {
        width: 100%;
        height: 360px;
        border: 1px solid #D6E8ED;
        border-radius: 12px;
        overflow: visible;
        background: #F8FCFD;
    }
    .nearby-message {
        border: 1px solid #D6E8ED;
        border-radius: 12px;
        background: #F8FCFD;
        color: #52656d;
        padding: 0.78rem 0.9rem;
        font-weight: 700;
        margin: 0.8rem 0;
    }
    .nearby-message.is-warning {
        border-color: #F0D36A;
        background: #FFF9E8;
        color: #7A5A00;
    }
    .shop-list {
        display: grid;
        gap: 0.65rem;
        margin-top: 0.85rem;
    }
    .shop-list-item {
        border: 1px solid #D6E8ED;
        border-radius: 12px;
        background: #fff;
        padding: 0.8rem 0.9rem;
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 0.8rem;
        align-items: center;
    }
    .shop-name {
        color: #0f1f26;
        font-weight: 800;
        margin-bottom: 0.2rem;
    }
    .shop-meta {
        color: #52656d;
        font-size: 0.86rem;
    }
    .shop-distance {
        color: #0f6c85;
        font-weight: 800;
        white-space: nowrap;
    }
    .shop-popup-title {
        font-weight: 800;
        color: #0f1f26;
        margin-bottom: 0.25rem;
    }
    .shop-popup-text {
        color: #52656d;
        font-size: 0.86rem;
        margin-bottom: 0.35rem;
    }
    .shop-popup-button {
        display: inline-flex;
        border-radius: 999px;
        background: #0f6c85;
        color: #fff !important;
        font-weight: 800;
        text-decoration: none;
        padding: 0.42rem 0.7rem;
        margin-top: 0.25rem;
    }
    .gm-style .gm-style-iw-c {
        padding: 10px 12px 12px !important;
        border-radius: 8px !important;
    }
    .gm-style .gm-style-iw-d {
        max-width: 280px !important;
        max-height: 180px !important;
        overflow-y: auto !important;
        padding-bottom: 12px !important;
    }
    .gm-style .gm-style-iw-ch,
    .gm-style .gm-style-iw-chr {
        display: none !important;
        height: 0 !important;
        padding: 0 !important;
    }
    .shop-popup-card {
        max-width: 260px;
        min-width: 190px;
        line-height: 1.3;
        padding-bottom: 2px;
    }
    .shop-popup-card .shop-popup-title {
        margin-right: 20px;
    }
    .shop-popup-action {
        margin-top: 0.45rem;
        display: flex;
    }
    .selected-place-card {
        border: 1px solid #D6E8ED;
        border-radius: 12px;
        background: #F8FCFD;
        padding: 1rem;
        margin-top: 0.85rem;
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 0.9rem;
        align-items: center;
    }
    .selected-place-title {
        color: #0f1f26;
        font-weight: 800;
        margin-bottom: 0.25rem;
    }
    .selected-place-meta {
        color: #52656d;
        font-size: 0.9rem;
        margin-top: 0.18rem;
    }
    .analysis-error {
        max-width: 680px;
        margin: 1rem auto 0;
        border: 1px solid #E8A1A1;
        background: #FCEAEA;
        color: #7f1d1d;
        border-radius: 12px;
        padding: 0.85rem 1rem;
        font-weight: 600;
    }
    .d-none {
        display: none !important;
    }
    @media (max-width: 991.98px) {
        .result-grid {
            grid-template-columns: 1fr;
        }
        .image-preview-container {
            min-height: 320px;
            max-height: 420px;
        }
        .image-preview-container img {
            max-height: 420px;
        }
        .nearby-shops-toolbar,
        .nearby-search-row,
        .selected-place-card,
        .shop-list-item {
            grid-template-columns: 1fr;
            display: grid;
        }
        .result-actions,
        .guide-grid {
            grid-template-columns: 1fr;
        }
        .nearby-shops-toolbar {
            align-items: stretch;
        }
    }
</style>

<div class="analysis-page">
    <div class="analysis-heading">
        <h1>AI Fish Health Analysis</h1>
        <p>Upload a fish image to receive an AI-assisted health assessment.</p>
    </div>

    <div class="usage-card">
        <div class="usage-header">
            <div class="usage-title">AI Analysis Usage</div>
            <div class="usage-count" id="usage-count">Today's Usage: {{ $usageCount }} / {{ $dailyLimit }} analyses</div>
        </div>
        <div class="usage-remaining" id="usage-remaining">You have {{ $remainingCount }} analyses remaining today.</div>
        <div class="usage-progress" role="progressbar" aria-label="Daily AI analysis usage"
             aria-valuemin="0" aria-valuemax="{{ $dailyLimit }}" aria-valuenow="{{ $usageCount }}" id="usage-progress">
            <div class="usage-progress-bar" id="usage-progress-bar"
                 style="width: {{ min(100, ($usageCount / max(1, $dailyLimit)) * 100) }}%;"></div>
        </div>
    </div>

    <div class="analysis-card" id="upload-state">
        <div class="upload-zone {{ $remainingCount === 0 ? 'is-disabled' : '' }}" id="upload-zone">
            <div>
                <div class="upload-icon">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M4 17.5V6.5A2.5 2.5 0 0 1 6.5 4h11A2.5 2.5 0 0 1 20 6.5v11a2.5 2.5 0 0 1-2.5 2.5h-11A2.5 2.5 0 0 1 4 17.5Z" stroke="currentColor" stroke-width="1.7"/>
                        <path d="m7 16 3.2-3.4 2.5 2.5L15.7 11 19 16" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M14.5 7.5h.01" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
                    </svg>
                </div>
                <div class="upload-title">Drag and drop or upload fish image</div>
                <div class="upload-note">JPEG, PNG, GIF, or WebP. Maximum file size 10 MB.</div>
                <button class="btn btn-aqua" type="button" id="choose-file" {{ $remainingCount === 0 ? 'disabled' : '' }}>Upload Image</button>
                <input class="d-none" type="file" id="fish-image" accept="image/jpeg,image/png,image/gif,image/webp" {{ $remainingCount === 0 ? 'disabled' : '' }}>
            </div>
        </div>

        <div class="file-preview-row d-none" id="file-preview-row">
            <img src="" alt="Selected fish image preview" id="file-preview-thumb">
            <div class="file-name" id="file-name"></div>
            <button class="delete-file" type="button" id="delete-file">Delete</button>
        </div>

        <div class="text-center mt-3">
            <button class="btn btn-aqua" type="button" id="analyze-button" disabled>Analyze Fish Health</button>
        </div>

        <div class="analysis-error d-none" id="analysis-error"></div>
    </div>

    <div class="analysis-card d-none" id="loading-state">
        <div class="loading-state">
            <div>
                <div class="spinner-aqua"></div>
                <div class="h5 fw-bold mb-1">Scanning image...</div>
                <div class="muted" id="loading-file-name"></div>
            </div>
        </div>
    </div>

    <div class="d-none" id="result-state">
        <div class="result-grid">
            <div class="uploaded-panel">
                <button class="back-button" type="button" id="new-image-button">
                    <span aria-hidden="true">&larr;</span>
                    <span>New Image</span>
                </button>
                <div class="image-preview-container">
                    <img src="" alt="Uploaded fish image" id="result-image">
                </div>
            </div>

            <div class="result-panel">
                <div class="result-header">
                    <div>
                        <div class="result-label">Status</div>
                        <div class="h5 fw-bold mb-0" id="result-status-text">-</div>
                    </div>
                    <span class="status-badge" id="result-status-badge">-</span>
                </div>

                <div class="result-block">
                    <div class="result-label">Disease Name</div>
                    <div class="fw-semibold" id="result-disease">-</div>
                </div>

                <div class="result-block">
                    <div class="result-label">Visible Symptoms</div>
                    <ul class="result-list" id="result-symptoms"></ul>
                </div>

                <div class="result-block">
                    <div class="result-label">Recommendations</div>
                    <ul class="result-list" id="result-recommendations"></ul>
                </div>

                <div class="result-block">
                    <div class="result-label">Confidence Level</div>
                    <span class="confidence-pill" id="result-confidence">0%</span>
                </div>

                <div class="result-block d-none" id="low-confidence-message">
                    <div class="low-confidence-warning">
                        The AI detected a possible issue, but confidence is low. Please retake a clearer image or monitor the fish condition.
                    </div>
                </div>
            </div>
        </div>

        <div class="result-actions d-none" id="result-actions">
            <button class="btn btn-outline-aqua" type="button" id="treatment-guide-button">View Treatment Guide</button>
            <button class="btn btn-outline-aqua" type="button" id="nearby-shops-button">Find Nearby Aquarium Shops</button>
        </div>

        <div class="detail-panel d-none" id="detail-panel">
            <div class="d-none" id="treatment-guide-panel">
                <div class="result-label">Treatment Guide</div>
                <div class="h5 fw-bold mb-2" id="guide-condition-title">Fish Treatment Guide</div>
                <div class="low-confidence-warning mb-3">
                    This AI analysis is not a medical diagnosis. It is only a preliminary guide.
                </div>
                <div class="guide-grid">
                    <div class="guide-section">
                        <h3>Symptoms</h3>
                        <ul class="result-list" id="guide-symptoms"></ul>
                    </div>
                    <div class="guide-section">
                        <h3>Possible Causes</h3>
                        <ul class="result-list" id="guide-causes"></ul>
                    </div>
                    <div class="guide-section">
                        <h3>Recommended Actions</h3>
                        <ul class="result-list" id="guide-actions-list"></ul>
                    </div>
                    <div class="guide-section">
                        <h3>Prevention Tips</h3>
                        <ul class="result-list" id="guide-prevention"></ul>
                    </div>
                </div>
            </div>

            <div class="nearby-shops-panel d-none" id="nearby-shops-panel">
                <div class="nearby-shops-toolbar">
                    <div>
                        <div class="result-label">Nearby Aquarium Shops & Veterinary Clinics</div>
                        <div class="small muted" id="nearby-subtitle">Search by current location or enter a place name. Results are sorted by nearest first.</div>
                    </div>
                    <div class="nearby-search-row">
                        <input class="form-control" type="text" id="manual-location-input" placeholder="Choose starting point" aria-label="Search location">
                        <select class="form-select" id="nearby-radius" aria-label="Search radius">
                            <option value="5">5 km</option>
                            <option value="10" selected>10 km</option>
                            <option value="20">20 km</option>
                            <option value="30">30 km</option>
                            <option value="50">50 km</option>
                        </select>
                        <button class="btn btn-outline-aqua" type="button" id="manual-location-search">Search</button>
                        <button class="btn btn-aqua" type="button" id="nearby-current-location">Use My Current Location</button>
                    </div>
                </div>
                <div class="nearby-map" id="nearby-map"></div>
                <div class="selected-place-card d-none" id="selected-place-card"></div>
                <div class="nearby-message d-none" id="nearby-message"></div>
                <div class="shop-list" id="nearby-shop-list"></div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const uploadState = document.getElementById('upload-state');
        const loadingState = document.getElementById('loading-state');
        const resultState = document.getElementById('result-state');
        const uploadZone = document.getElementById('upload-zone');
        const fileInput = document.getElementById('fish-image');
        const chooseFile = document.getElementById('choose-file');
        const previewRow = document.getElementById('file-preview-row');
        const previewThumb = document.getElementById('file-preview-thumb');
        const fileName = document.getElementById('file-name');
        const deleteFile = document.getElementById('delete-file');
        const analyzeButton = document.getElementById('analyze-button');
        const errorBox = document.getElementById('analysis-error');
        const loadingFileName = document.getElementById('loading-file-name');
        const resultImage = document.getElementById('result-image');
        const newImageButton = document.getElementById('new-image-button');
        const usageCountEl = document.getElementById('usage-count');
        const usageRemainingEl = document.getElementById('usage-remaining');
        const usageProgress = document.getElementById('usage-progress');
        const usageProgressBar = document.getElementById('usage-progress-bar');
        const lowConfidenceMessage = document.getElementById('low-confidence-message');
        const resultActions = document.getElementById('result-actions');
        const detailPanel = document.getElementById('detail-panel');
        const treatmentGuidePanel = document.getElementById('treatment-guide-panel');
        const treatmentGuideButton = document.getElementById('treatment-guide-button');
        const nearbyShopsButton = document.getElementById('nearby-shops-button');
        const nearbyShopsPanel = document.getElementById('nearby-shops-panel');
        const nearbyCurrentLocationButton = document.getElementById('nearby-current-location');
        const manualLocationInput = document.getElementById('manual-location-input');
        const manualLocationSearch = document.getElementById('manual-location-search');
        const nearbyRadius = document.getElementById('nearby-radius');
        const nearbySubtitle = document.getElementById('nearby-subtitle');
        const nearbyMessage = document.getElementById('nearby-message');
        const nearbyShopList = document.getElementById('nearby-shop-list');
        const selectedPlaceCard = document.getElementById('selected-place-card');
        const dailyLimit = {{ $dailyLimit }};
        let usageCount = {{ $usageCount }};
        let selectedFile = null;
        let previewUrl = null;
        let resultImageDataUrl = null;
        let nearbyMap = null;
        let nearbyInfoWindow = null;
        let googleGeocoder = null;
        let userLocationMarker = null;
        let currentNearbyMarkers = [];
        let selectedSearchLocation = null;
        let lastDiseaseResult = false;
        let currentNearbyPlaces = [];
        let latestAnalysisData = null;
        const radiusOptions = [5, 10, 20, 30, 50];

        const guideContent = {
            fin_rot: {
                symptoms: ['frayed fins', 'white fin edges', 'damaged tail', 'inactive behavior'],
                causes: ['poor water quality', 'bacterial infection', 'stress', 'overcrowding'],
                actions: ['isolate fish if possible', 'perform partial water change', 'check pH and turbidity', 'improve filtration', 'consult aquarium shop before medication'],
                prevention: ['keep water clean', 'avoid overfeeding', 'monitor pH', 'quarantine new fish'],
            },
            ich: {
                symptoms: ['small white spots', 'scratching body on surfaces', 'rapid breathing'],
                causes: ['parasite infection', 'sudden temperature changes', 'stress'],
                actions: ['isolate fish if possible', 'check water quality', 'maintain stable temperature', 'consult aquarium shop for suitable treatment'],
                prevention: ['quarantine new fish', 'avoid sudden water changes', 'maintain stable water parameters'],
            },
            fungus: {
                symptoms: ['cotton-like patches', 'white growth on body or fins'],
                causes: ['injury', 'poor water condition', 'weak immune system'],
                actions: ['improve water quality', 'isolate affected fish', 'remove uneaten food', 'consult aquarium shop for antifungal treatment'],
                prevention: ['maintain clean water', 'avoid sharp decorations', 'reduce stress'],
            },
            unknown: {
                symptoms: ['abnormal appearance or behavior detected'],
                causes: ['poor water quality', 'stress', 'infection', 'unsuitable tank condition'],
                actions: ['check pH, turbidity and water level', 'perform partial water change', 'monitor fish', 'seek advice from aquarium shop'],
                prevention: ['regular water changes', 'stable water parameters', 'avoid overcrowding'],
            },
        };

        function limitReached() {
            return usageCount >= dailyLimit;
        }

        function updateUsage(nextUsage) {
            usageCount = Math.min(dailyLimit, Math.max(0, Number(nextUsage) || 0));
            const remaining = Math.max(0, dailyLimit - usageCount);
            const percentage = Math.min(100, (usageCount / Math.max(1, dailyLimit)) * 100);
            usageCountEl.textContent = `Today's Usage: ${usageCount} / ${dailyLimit} analyses`;
            usageRemainingEl.textContent = `You have ${remaining} analyses remaining today.`;
            usageProgress.setAttribute('aria-valuenow', usageCount);
            usageProgressBar.style.width = `${percentage}%`;
            uploadZone.classList.toggle('is-disabled', limitReached());
            chooseFile.disabled = limitReached();
            fileInput.disabled = limitReached();
            analyzeButton.disabled = limitReached() || !selectedFile;
        }

        function showState(state) {
            uploadState.classList.toggle('d-none', state !== 'upload');
            loadingState.classList.toggle('d-none', state !== 'loading');
            resultState.classList.toggle('d-none', state !== 'result');
        }

        function setError(message = '') {
            errorBox.textContent = message;
            errorBox.classList.toggle('d-none', !message);
        }

        function clearPreviewUrl() {
            if (previewUrl) {
                URL.revokeObjectURL(previewUrl);
                previewUrl = null;
            }
        }

        function selectFile(file) {
            setError('');
            if (limitReached()) {
                setError('Daily AI analysis limit reached. Please try again tomorrow.');
                return;
            }
            if (!file) return;
            if (!file.type.startsWith('image/')) {
                setError('Please upload an image file.');
                return;
            }
            if (file.size > 10 * 1024 * 1024) {
                setError('Image must be 10 MB or smaller.');
                return;
            }

            clearPreviewUrl();
            selectedFile = file;
            previewUrl = URL.createObjectURL(file);
            resultImageDataUrl = null;
            previewThumb.src = previewUrl;
            resultImage.src = previewUrl;
            fileName.textContent = file.name;
            loadingFileName.textContent = file.name;
            previewRow.classList.remove('d-none');
            analyzeButton.disabled = limitReached();

            const reader = new FileReader();
            reader.addEventListener('load', () => {
                resultImageDataUrl = reader.result;
            });
            reader.readAsDataURL(file);
        }

        function resetUpload() {
            selectedFile = null;
            fileInput.value = '';
            clearPreviewUrl();
            resultImageDataUrl = null;
            previewThumb.src = '';
            resultImage.src = '';
            fileName.textContent = '';
            loadingFileName.textContent = '';
            previewRow.classList.add('d-none');
            analyzeButton.disabled = true;
            resetNearbyShops();
            latestAnalysisData = null;
            lastDiseaseResult = false;
            setError('');
            showState('upload');
        }

        function renderList(listEl, items) {
            listEl.innerHTML = '';
            const values = Array.isArray(items) && items.length ? items : ['None reported.'];
            values.forEach((item) => {
                const li = document.createElement('li');
                li.textContent = item;
                listEl.appendChild(li);
            });
        }

        function renderSimpleList(id, items) {
            renderList(document.getElementById(id), items);
        }

        function escapeHtml(value) {
            return String(value || '').replace(/[&<>"']/g, (char) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;',
            }[char]));
        }

        function parseConfidence(value) {
            const match = String(value || '').match(/\d+(\.\d+)?/);
            return match ? Number(match[0]) : 0;
        }

        function isHealthyAnalysis(data) {
            const status = String(data.status || '').toUpperCase();
            const disease = String(data.disease_name || '').toUpperCase();

            return status === 'HEALTHY' || disease.includes('NO VISIBLE DISEASE');
        }

        function normalizeGuideCondition(condition) {
            const value = String(condition || '').toLowerCase();

            if (value.includes('fin') && value.includes('rot')) return 'fin_rot';
            if (value.includes('ich') || value.includes('white spot')) return 'ich';
            if (value.includes('fungus') || value.includes('fungal')) return 'fungus';

            return 'unknown';
        }

        function setActiveDetailButton(activeButton) {
            [treatmentGuideButton, nearbyShopsButton].forEach((button) => {
                button.classList.toggle('is-active', button === activeButton);
            });
        }

        function hideDetailPanels() {
            detailPanel.classList.add('d-none');
            treatmentGuidePanel.classList.add('d-none');
            nearbyShopsPanel.classList.add('d-none');
            setActiveDetailButton(null);
        }

        function showTreatmentGuide() {
            if (!latestAnalysisData || !lastDiseaseResult) return;

            const guide = guideContent[normalizeGuideCondition(latestAnalysisData.disease_name)];
            document.getElementById('guide-condition-title').textContent = latestAnalysisData.disease_name || 'Unknown disease';
            renderSimpleList('guide-symptoms', guide.symptoms);
            renderSimpleList('guide-causes', guide.causes);
            renderSimpleList('guide-actions-list', guide.actions);
            renderSimpleList('guide-prevention', guide.prevention);

            detailPanel.classList.remove('d-none');
            treatmentGuidePanel.classList.remove('d-none');
            nearbyShopsPanel.classList.add('d-none');
            setActiveDetailButton(treatmentGuideButton);
        }

        function storeTreatmentGuideData(data) {
            try {
                sessionStorage.setItem('fishTreatmentGuide', JSON.stringify({
                    image: data.image_url || resultImageDataUrl || previewUrl || '',
                    status: data.status || 'UNKNOWN',
                    disease_name: data.disease_name || 'Unknown disease',
                    confidence_level: data.confidence_level || '0%',
                }));
            } catch (error) {
                sessionStorage.setItem('fishTreatmentGuide', JSON.stringify({
                    image: data.image_url || '',
                    status: data.status || 'UNKNOWN',
                    disease_name: data.disease_name || 'Unknown disease',
                    confidence_level: data.confidence_level || '0%',
                }));
            }
        }

        function showNearbyMessage(message, isWarning = false) {
            nearbyMessage.textContent = message;
            nearbyMessage.classList.toggle('d-none', !message);
            nearbyMessage.classList.toggle('is-warning', isWarning);
        }

        function setNearbyLoading(isLoading, label = 'Searching...') {
            nearbyCurrentLocationButton.disabled = isLoading;
            manualLocationSearch.disabled = isLoading;
            nearbyCurrentLocationButton.textContent = isLoading ? label : 'Use My Current Location';
            manualLocationSearch.textContent = isLoading ? 'Searching...' : 'Search';
        }

        function loadGoogleMaps() {
            if (window.google?.maps) return Promise.resolve();

            if (window.aquaWatchGoogleMapsPromise) {
                return window.aquaWatchGoogleMapsPromise;
            }

            const apiKey = @json(config('services.google_maps.browser_key'));

            if (!apiKey) {
                return Promise.reject(new Error('Google Maps API key is not configured.'));
            }

            window.aquaWatchGoogleMapsPromise = new Promise((resolve, reject) => {
                window.aquaWatchGoogleMapsReady = () => resolve();
                const script = document.createElement('script');
                script.src = `https://maps.googleapis.com/maps/api/js?key=${encodeURIComponent(apiKey)}&libraries=places&loading=async&callback=aquaWatchGoogleMapsReady`;
                script.async = true;
                script.defer = true;
                script.onerror = () => reject(new Error('Google Maps could not be loaded. Please try again later.'));
                document.head.appendChild(script);
            });

            return window.aquaWatchGoogleMapsPromise;
        }

        function invalidateNearbyMap() {
            if (!nearbyMap) return;

            setTimeout(() => {
                google.maps.event.trigger(nearbyMap, 'resize');
                if (selectedSearchLocation) {
                    nearbyMap.setCenter({ lat: selectedSearchLocation.lat, lng: selectedSearchLocation.lng });
                }
            }, 300);
        }

        function clearGoogleMarkers() {
            currentNearbyMarkers.forEach((marker) => marker.setMap(null));
            currentNearbyMarkers = [];
            if (userLocationMarker) {
                userLocationMarker.setMap(null);
                userLocationMarker = null;
            }
        }

        function openStatusText(place) {
            if (place.open_now === true) return 'Open now';
            if (place.open_now === false) return 'Closed now';
            return 'Opening status unavailable';
        }

        function directionsUrl(place) {
            return `https://www.google.com/maps/dir/?api=1&destination=${encodeURIComponent(place.lat + ',' + place.lng)}`;
        }

        async function initGoogleMap(location) {
            await loadGoogleMaps();

            if (nearbyMap) {
                clearGoogleMarkers();
                nearbyMap = null;
            }

            const center = { lat: location.lat, lng: location.lng };
            nearbyMap = new google.maps.Map(document.getElementById('nearby-map'), {
                center,
                zoom: 13,
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: true,
            });
            nearbyInfoWindow = new google.maps.InfoWindow({
                maxWidth: 340,
                headerDisabled: true,
            });
            googleGeocoder = googleGeocoder || new google.maps.Geocoder();
            invalidateNearbyMap();
        }

        function renderUserMarker(location) {
            userLocationMarker = new google.maps.Marker({
                map: nearbyMap,
                position: { lat: location.lat, lng: location.lng },
                title: location.name || 'Selected location',
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 9,
                    fillColor: '#0D6EFD',
                    fillOpacity: 1,
                    strokeColor: '#ffffff',
                    strokeWeight: 3,
                },
            });
        }

        function placeInfoHtml(place) {
            return `
                <div class="shop-popup-card">
                    <div class="shop-popup-title">${escapeHtml(place.name)}</div>
                    <div class="shop-popup-text">${Number(place.distance_km).toFixed(2)} km away</div>
                    <div class="shop-popup-text fw-bold">View details below</div>
                </div>
            `;
        }

        function renderSelectedPlace(place) {
            selectedPlaceCard.classList.remove('d-none');
            selectedPlaceCard.innerHTML = `
                <div>
                    <div class="selected-place-title">${escapeHtml(place.name)}</div>
                    <div class="selected-place-meta">${escapeHtml(place.category || 'Place')}</div>
                    <div class="selected-place-meta">${escapeHtml(place.address || 'Address not available')}</div>
                    <div class="selected-place-meta">Rating: ${place.rating ? escapeHtml(place.rating) : 'Not available'}</div>
                    <div class="selected-place-meta">${escapeHtml(openStatusText(place))}</div>
                    <div class="selected-place-meta">${Number(place.distance_km).toFixed(2)} km away</div>
                </div>
                <div>
                    <a class="btn btn-aqua" href="${directionsUrl(place)}" target="_blank" rel="noopener">Get Directions</a>
                </div>
            `;
        }

        function renderNearbyPlaces(places, radiusKm) {
            nearbyShopList.innerHTML = '';
            selectedPlaceCard.classList.add('d-none');
            selectedPlaceCard.innerHTML = '';
            clearGoogleMarkers();
            renderUserMarker(selectedSearchLocation);
            nearbySubtitle.textContent = `Showing results within ${radiusKm} km of ${selectedSearchLocation.name || 'selected location'}.`;

            const bounds = new google.maps.LatLngBounds();
            bounds.extend({ lat: selectedSearchLocation.lat, lng: selectedSearchLocation.lng });

            places.forEach((place) => {
                const position = { lat: place.lat, lng: place.lng };
                const marker = new google.maps.Marker({
                    map: nearbyMap,
                    position,
                    title: place.name,
                });

                marker.addListener('click', () => {
                    nearbyMap.panTo(position);
                    renderSelectedPlace(place);
                    nearbyInfoWindow.setContent(placeInfoHtml(place));
                    nearbyInfoWindow.open(nearbyMap, marker);
                });

                currentNearbyMarkers.push(marker);
                bounds.extend(position);

                const item = document.createElement('div');
                item.className = 'shop-list-item';
                item.innerHTML = `
                    <div>
                        <div class="shop-name">${escapeHtml(place.name)}</div>
                        <div class="shop-meta">${escapeHtml(place.category || 'Place')} - Rating: ${place.rating ? escapeHtml(place.rating) : 'Not available'}</div>
                        <div class="shop-meta">${escapeHtml(place.address || 'Address not available')}</div>
                        <div class="shop-meta">${escapeHtml(openStatusText(place))}</div>
                    </div>
                    <div>
                        <div class="shop-distance">${Number(place.distance_km).toFixed(2)} km</div>
                        <a class="small fw-bold" href="${directionsUrl(place)}" target="_blank" rel="noopener">Get Directions</a>
                    </div>
                `;
                item.addEventListener('click', () => {
                    nearbyMap.panTo(position);
                    nearbyMap.setZoom(16);
                    renderSelectedPlace(place);
                    nearbyInfoWindow.setContent(placeInfoHtml(place));
                    nearbyInfoWindow.open(nearbyMap, marker);
                });
                nearbyShopList.appendChild(item);
            });

            if (places.length) {
                nearbyMap.fitBounds(bounds);
            } else {
                nearbyMap.setCenter({ lat: selectedSearchLocation.lat, lng: selectedSearchLocation.lng });
                nearbyMap.setZoom(13);
            }

            invalidateNearbyMap();
        }

        async function fetchNearbyPlaces(location, radiusKm) {
            const params = new URLSearchParams({
                lat: location.lat,
                lng: location.lng,
                radius: radiusKm * 1000,
            });
            const response = await fetch(`{{ route('image-analysis.nearby-shops') }}?${params.toString()}`, {
                headers: { 'Accept': 'application/json' },
            });
            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Nearby search is currently unavailable. Please try again later.');
            }

            return Array.isArray(data.places) ? data.places : [];
        }

        async function setSelectedSearchLocation(location) {
            selectedSearchLocation = location;
            currentNearbyPlaces = [];
            nearbyShopList.innerHTML = '';
            selectedPlaceCard.classList.add('d-none');
            selectedPlaceCard.innerHTML = '';
            showNearbyMessage('');
            nearbySubtitle.textContent = `Selected location: ${location.name || 'selected location'}. Click Search to find nearby places.`;
            await initGoogleMap(location);
            renderNearbyPlaces([], Number(nearbyRadius.value) || 10);
            showNearbyMessage('Location selected. Click Search to find nearby aquarium shops, pet shops, and veterinary clinics.');
        }

        async function searchNearbyFromLocation(location, radiusKm = Number(nearbyRadius.value) || 10) {
            selectedSearchLocation = location;
            currentNearbyPlaces = [];
            nearbyShopList.innerHTML = '';
            selectedPlaceCard.classList.add('d-none');
            selectedPlaceCard.innerHTML = '';
            showNearbyMessage('');
            setNearbyLoading(true);

            try {
                await initGoogleMap(location);
                showNearbyMessage(`Searching within ${radiusKm} km...`);
                currentNearbyPlaces = await fetchNearbyPlaces(location, radiusKm);
                renderNearbyPlaces(currentNearbyPlaces, radiusKm);

                if (!currentNearbyPlaces.length) {
                    showNearbyMessage('No aquarium shops or veterinary clinics were found within the selected radius.', true);
                } else {
                    showNearbyMessage('');
                }
            } catch (error) {
                if (nearbyMap && selectedSearchLocation) {
                    renderNearbyPlaces([], radiusKm);
                }
                showNearbyMessage(error.message || 'Nearby search is currently unavailable. Please try again later.', true);
            } finally {
                setNearbyLoading(false);
            }
        }

        function requestNearbyLocation() {
            if (!lastDiseaseResult) return;

            detailPanel.classList.remove('d-none');
            nearbyShopsPanel.classList.remove('d-none');
            treatmentGuidePanel.classList.add('d-none');
            setActiveDetailButton(nearbyShopsButton);
            nearbyShopList.innerHTML = '';

            if (!navigator.geolocation) {
                showNearbyMessage('Unable to detect your current location. Please search your location manually.', true);
                return;
            }

            showNearbyMessage('Requesting your current location...');
            setNearbyLoading(true, 'Detecting...');

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    setSelectedSearchLocation({
                        name: 'your current location',
                        lat: position.coords.latitude,
                        lng: position.coords.longitude,
                    }).finally(() => setNearbyLoading(false));
                },
                () => {
                    showNearbyMessage('Unable to detect your current location. Please search your location manually.', true);
                    setNearbyLoading(false);
                },
                {
                    enableHighAccuracy: true,
                    timeout: 12000,
                    maximumAge: 300000,
                }
            );
        }

        async function geocodeManualLocation(query) {
            await loadGoogleMaps();
            googleGeocoder = googleGeocoder || new google.maps.Geocoder();

            return new Promise((resolve, reject) => {
                googleGeocoder.geocode({ address: query }, (results, status) => {
                    if (status !== 'OK' || !results?.[0]?.geometry?.location) {
                        reject(new Error('Location not found. Please try a more specific location.'));
                        return;
                    }

                    const result = results[0];
                    resolve({
                        name: result.formatted_address || query,
                        lat: result.geometry.location.lat(),
                        lng: result.geometry.location.lng(),
                    });
                });
            });
        }

        async function runNearbySearch() {
            const query = manualLocationInput.value.trim();

            if (!query && !selectedSearchLocation) {
                showNearbyMessage('Please enter a location or use your current location first.', true);
                return;
            }

            detailPanel.classList.remove('d-none');
            nearbyShopsPanel.classList.remove('d-none');
            treatmentGuidePanel.classList.add('d-none');
            setActiveDetailButton(nearbyShopsButton);
            setNearbyLoading(true);
            showNearbyMessage(query ? 'Searching location...' : 'Searching nearby places...');

            try {
                const location = query ? await geocodeManualLocation(query) : selectedSearchLocation;
                await searchNearbyFromLocation(location, Number(nearbyRadius.value) || 10);
            } catch (error) {
                showNearbyMessage(error.message || 'Nearby search is currently unavailable. Please try again later.', true);
            } finally {
                setNearbyLoading(false);
            }
        }

        function showNearbyPanel() {
            if (!lastDiseaseResult) return;

            detailPanel.classList.remove('d-none');
            treatmentGuidePanel.classList.add('d-none');
            nearbyShopsPanel.classList.remove('d-none');
            setActiveDetailButton(nearbyShopsButton);
            invalidateNearbyMap();
            showNearbyMessage('Use your current location or search a location manually.');
        }

        function resetNearbyShops() {
            currentNearbyPlaces = [];
            selectedSearchLocation = null;
            nearbyShopList.innerHTML = '';
            selectedPlaceCard.classList.add('d-none');
            selectedPlaceCard.innerHTML = '';
            hideDetailPanels();
            setNearbyLoading(false);
            nearbySubtitle.textContent = 'Search by current location or enter a place name. Results are sorted by nearest first.';
            showNearbyMessage('');
            clearGoogleMarkers();
            nearbyMap = null;
            nearbyInfoWindow = null;
        }

        function renderResult(data) {
            const status = data.status || 'UNKNOWN';
            const isHealthy = isHealthyAnalysis(data);
            const confidence = parseConfidence(data.confidence_level);
            const showGeneralCareGuide = !isHealthy && confidence < 70;
            const showDiseaseActions = !isHealthy && confidence >= 70;
            lastDiseaseResult = !isHealthy;
            latestAnalysisData = data;
            const statusBadge = document.getElementById('result-status-badge');
            document.getElementById('result-status-text').textContent = status;
            statusBadge.textContent = isHealthy ? 'Good' : 'Review';
            statusBadge.className = 'status-badge ' + (isHealthy ? 'status-healthy' : 'status-warning');
            document.getElementById('result-disease').textContent = data.disease_name || 'Unknown';
            renderList(document.getElementById('result-symptoms'), data.visible_symptoms);
            renderList(document.getElementById('result-recommendations'), data.recommendations);
            document.getElementById('result-confidence').textContent = data.confidence_level || '0%';
            lowConfidenceMessage.classList.toggle('d-none', !showGeneralCareGuide);
            resultActions.classList.toggle('d-none', !showDiseaseActions);
            treatmentGuideButton.textContent = showGeneralCareGuide ? 'View General Care Guide' : 'View Treatment Guide';
            if (!showDiseaseActions) {
                currentNearbyPlaces = [];
                nearbyShopList.innerHTML = '';
                hideDetailPanels();
                if (nearbyMap) {
                    clearGoogleMarkers();
                    nearbyMap = null;
                    nearbyInfoWindow = null;
                }
            }
            storeTreatmentGuideData(data);
        }

        chooseFile.addEventListener('click', () => {
            if (!limitReached()) fileInput.click();
        });
        uploadZone.addEventListener('click', (event) => {
            if (!limitReached() && event.target !== chooseFile) fileInput.click();
        });
        fileInput.addEventListener('change', () => selectFile(fileInput.files[0]));
        deleteFile.addEventListener('click', resetUpload);
        newImageButton.addEventListener('click', resetUpload);
        treatmentGuideButton.addEventListener('click', showTreatmentGuide);
        nearbyShopsButton.addEventListener('click', showNearbyPanel);
        nearbyCurrentLocationButton.addEventListener('click', requestNearbyLocation);
        manualLocationSearch.addEventListener('click', runNearbySearch);
        window.addEventListener('resize', invalidateNearbyMap);

        ['dragenter', 'dragover'].forEach((eventName) => {
            uploadZone.addEventListener(eventName, (event) => {
                event.preventDefault();
                if (limitReached()) return;
                uploadZone.classList.add('is-dragging');
            });
        });
        ['dragleave', 'drop'].forEach((eventName) => {
            uploadZone.addEventListener(eventName, (event) => {
                event.preventDefault();
                uploadZone.classList.remove('is-dragging');
            });
        });
        uploadZone.addEventListener('drop', (event) => {
            if (limitReached()) return;
            selectFile(event.dataTransfer.files[0]);
        });

        analyzeButton.addEventListener('click', async () => {
            if (!selectedFile) return;

            const formData = new FormData();
            formData.append('aquarium_photo', selectedFile);
            resetNearbyShops();
            showState('loading');

            try {
                const response = await fetch('/api/v1/vision/check-fish', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: formData,
                });
                const data = await response.json();
                if (typeof data.usage !== 'undefined') {
                    updateUsage(data.usage);
                }
                if (!response.ok || !data.success) {
                    throw new Error(data.error || 'Unable to analyze this image.');
                }
                renderResult(data);
                showState('result');
            } catch (error) {
                showState('upload');
                setError(error.message || 'Unable to analyze this image.');
            }
        });

        updateUsage(usageCount);
    });
</script>
@endsection

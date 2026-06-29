@extends('user.layouts.app')

@section('title', 'Fish Treatment Guide')

@section('content')
<style>
    .guide-page {
        max-width: 1100px;
        margin: 0 auto;
    }
    .guide-header {
        margin-bottom: 1.4rem;
    }
    .guide-header h1 {
        color: #0f1f26;
        font-size: clamp(1.8rem, 4vw, 2.6rem);
        font-weight: 800;
        letter-spacing: 0;
        margin-bottom: 0.45rem;
    }
    .guide-header p {
        color: #52656d;
        margin: 0;
    }
    .guide-layout {
        display: grid;
        grid-template-columns: minmax(0, 0.9fr) minmax(0, 1.1fr);
        gap: 1.2rem;
        align-items: start;
    }
    .guide-panel {
        border: 1px solid #D6E8ED;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 4px 12px rgba(15, 87, 110, 0.08);
        padding: clamp(1rem, 3vw, 1.35rem);
    }
    .guide-image-frame {
        min-height: 360px;
        border: 1px solid #E5EEF1;
        border-radius: 13px;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        margin-bottom: 1rem;
    }
    .guide-image-frame img {
        max-width: 100%;
        max-height: 460px;
        object-fit: contain;
    }
    .guide-empty-image {
        color: #52656d;
        font-weight: 700;
    }
    .summary-row {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 0.75rem;
        padding: 0.8rem 0;
        border-top: 1px solid #E5EEF1;
    }
    .summary-label,
    .section-label {
        color: #52656d;
        font-size: 0.78rem;
        font-weight: 800;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }
    .summary-value {
        color: #0f1f26;
        font-weight: 800;
        text-align: right;
    }
    .disclaimer {
        border: 1px solid #F0D36A;
        border-radius: 12px;
        background: #FFF9E8;
        color: #7A5A00;
        font-weight: 700;
        padding: 0.85rem 1rem;
        margin-bottom: 1rem;
    }
    .guide-section {
        border-top: 1px solid #E5EEF1;
        padding-top: 1rem;
        margin-top: 1rem;
    }
    .guide-section h2 {
        font-size: 1.05rem;
        font-weight: 800;
        color: #0f1f26;
        margin-bottom: 0.6rem;
    }
    .guide-list {
        margin: 0;
        padding-left: 1.1rem;
    }
    .guide-list li + li {
        margin-top: 0.35rem;
    }
    .sensor-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0.75rem;
    }
    .sensor-tile {
        border: 1px solid #D6E8ED;
        border-radius: 12px;
        padding: 0.85rem;
        background: #F8FCFD;
    }
    .sensor-name {
        font-weight: 800;
        color: #0f1f26;
        margin-bottom: 0.3rem;
    }
    .sensor-value {
        font-weight: 800;
        color: #0f6c85;
    }
    .sensor-range {
        color: #52656d;
        font-size: 0.84rem;
        margin-top: 0.25rem;
    }
    .sensor-status {
        display: inline-flex;
        margin-top: 0.55rem;
        border-radius: 999px;
        padding: 0.28rem 0.58rem;
        font-size: 0.78rem;
        font-weight: 800;
    }
    .status-normal {
        background: #E8F7EF;
        color: #187044;
    }
    .status-low,
    .status-high {
        background: #FFF7D8;
        color: #8A6500;
    }
    .status-missing {
        background: #EEF3F5;
        color: #52656d;
    }
    .guide-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.65rem;
        margin-top: 1.2rem;
    }
    .btn-aqua {
        border: 0;
        border-radius: 999px;
        background: #0f6c85;
        color: #fff;
        font-weight: 800;
        padding: 0.72rem 1.15rem;
        text-decoration: none;
    }
    .btn-aqua:hover,
    .btn-aqua:focus {
        background: #0b5f76;
        color: #fff;
    }
    .btn-outline-aqua {
        border: 1px solid #0f6c85;
        border-radius: 999px;
        background: #fff;
        color: #0f6c85;
        font-weight: 800;
        padding: 0.72rem 1.15rem;
        text-decoration: none;
    }
    .btn-outline-aqua:hover,
    .btn-outline-aqua:focus {
        background: #F2FAFC;
        color: #0b5f76;
    }
    @media (max-width: 991.98px) {
        .guide-layout,
        .sensor-grid {
            grid-template-columns: 1fr;
        }
        .guide-image-frame {
            min-height: 280px;
        }
    }
</style>

<div class="guide-page">
    <div class="guide-header">
        <h1>Fish Treatment Guide</h1>
        <p>Use this page as a preliminary care reference based on the latest AI image analysis.</p>
    </div>

    <div class="guide-layout">
        <div class="guide-panel">
            <div class="guide-image-frame" id="guide-image-frame">
                <div class="guide-empty-image" id="guide-empty-image">No uploaded image available.</div>
                <img class="d-none" src="" alt="Uploaded fish image" id="guide-image">
            </div>

            <div class="summary-row">
                <div class="summary-label">AI Detected Condition</div>
                <div class="summary-value" id="guide-condition">Unknown disease</div>
            </div>
            <div class="summary-row">
                <div class="summary-label">Confidence Level</div>
                <div class="summary-value" id="guide-confidence">0%</div>
            </div>
        </div>

        <div class="guide-panel">
            <div class="disclaimer">This AI analysis is not a medical diagnosis. It is only a preliminary guide.</div>

            <div class="guide-section" style="border-top: 0; padding-top: 0; margin-top: 0;">
                <h2>Symptoms</h2>
                <ul class="guide-list" id="symptoms-list"></ul>
            </div>

            <div class="guide-section">
                <h2>Possible Causes</h2>
                <ul class="guide-list" id="causes-list"></ul>
            </div>

            <div class="guide-section">
                <h2>Recommended Actions</h2>
                <ul class="guide-list" id="actions-list"></ul>
            </div>

            <div class="guide-section">
                <h2>Prevention Tips</h2>
                <ul class="guide-list" id="prevention-list"></ul>
            </div>

            <div class="guide-section">
                <h2>Current Sensor Readings</h2>
                @if($sensorReadings->isEmpty())
                    <div class="muted">No selected tank sensor readings are available.</div>
                @else
                    <div class="sensor-grid">
                        @foreach($sensorReadings as $reading)
                            @php($statusClass = match($reading['status']) {
                                'Normal' => 'status-normal',
                                'Low' => 'status-low',
                                'High' => 'status-high',
                                default => 'status-missing',
                            })
                            <div class="sensor-tile">
                                <div class="sensor-name">{{ $reading['label'] }}</div>
                                <div class="sensor-value">
                                    {{ $reading['value'] === null ? 'Not available' : rtrim(rtrim(number_format($reading['value'], 2), '0'), '.') }}
                                    @if($reading['value'] !== null)
                                        {{ $reading['unit'] }}
                                    @endif
                                </div>
                                <div class="sensor-range">
                                    Safe range: {{ rtrim(rtrim(number_format($reading['min'], 2), '0'), '.') }} - {{ rtrim(rtrim(number_format($reading['max'], 2), '0'), '.') }} {{ $reading['unit'] }}
                                </div>
                                <span class="sensor-status {{ $statusClass }}">{{ $reading['status'] }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="guide-actions">
                <a class="btn btn-aqua" href="https://www.google.com/maps/search/aquarium+shops+near+me" target="_blank" rel="noopener">Find Nearby Aquarium Shops</a>
                <a class="btn btn-outline-aqua" href="{{ route('image-analysis.index') }}">Back to Image Analysis</a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
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

        function normalizeCondition(condition) {
            const value = String(condition || '').toLowerCase();

            if (value.includes('fin') && value.includes('rot')) return 'fin_rot';
            if (value.includes('ich') || value.includes('white spot')) return 'ich';
            if (value.includes('fungus') || value.includes('fungal')) return 'fungus';

            return 'unknown';
        }

        function renderList(id, items) {
            const list = document.getElementById(id);
            list.innerHTML = '';
            items.forEach((item) => {
                const li = document.createElement('li');
                li.textContent = item;
                list.appendChild(li);
            });
        }

        let data = {};
        try {
            data = JSON.parse(sessionStorage.getItem('fishTreatmentGuide') || '{}');
        } catch (error) {
            data = {};
        }

        const condition = data.disease_name || 'Unknown disease';
        const image = data.image || '';
        const guide = guideContent[normalizeCondition(condition)];
        const imageEl = document.getElementById('guide-image');
        const emptyImageEl = document.getElementById('guide-empty-image');

        document.getElementById('guide-condition').textContent = condition;
        document.getElementById('guide-confidence').textContent = data.confidence_level || '0%';

        if (image) {
            imageEl.src = image;
            imageEl.classList.remove('d-none');
            emptyImageEl.classList.add('d-none');
        }

        renderList('symptoms-list', guide.symptoms);
        renderList('causes-list', guide.causes);
        renderList('actions-list', guide.actions);
        renderList('prevention-list', guide.prevention);
    });
</script>
@endsection

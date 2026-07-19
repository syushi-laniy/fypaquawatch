@extends('user.layouts.app')

@section('title', 'Dashboard')

@section('content')
<style>
    .dashboard-section-title {
        font-weight: 700;
        margin-bottom: 10px;
    }
    .dashboard-glass-card {
        width: calc(100% - 48px);
        max-width: 1500px;
        margin: 24px auto;
        padding: 24px;
        border-radius: 28px;
        border: 1px solid rgba(255, 255, 255, 0.45);
        background: rgba(255, 255, 255, 0.20);
        backdrop-filter: blur(18px);
        -webkit-backdrop-filter: blur(18px);
        box-shadow:
            0 12px 35px rgba(25, 73, 110, 0.16),
            inset 0 1px 0 rgba(255, 255, 255, 0.35);
        overflow: hidden;
    }
    .dashboard-glass-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1rem;
    }
    .dashboard-glass-title {
        color: #0f1f26;
        font-weight: 700;
    }
    .control-mode-panel {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        border: 1px solid rgba(255, 255, 255, 0.55);
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.36);
        padding: 0.4rem;
        box-shadow:
            0 8px 20px rgba(25, 73, 110, 0.10),
            inset 0 1px 0 rgba(255, 255, 255, 0.45);
    }
    .control-mode-label {
        color: #0f1f26;
        font-weight: 700;
        padding-left: 0.5rem;
        white-space: nowrap;
    }
    .mode-options {
        position: relative;
        display: grid;
        grid-template-columns: repeat(2, minmax(84px, 1fr));
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.44);
        overflow: hidden;
    }
    .mode-options::before {
        content: "";
        position: absolute;
        inset: 4px auto 4px 4px;
        width: calc(50% - 4px);
        border-radius: 999px;
        background: #78a2d2;
        box-shadow: 0 6px 14px rgba(120, 162, 210, 0.34);
        transition: transform 0.28s ease;
        z-index: 0;
    }
    .mode-options:has(#mode-manual:checked)::before {
        transform: translateX(100%);
    }
    .mode-option {
        position: relative;
        z-index: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 34px;
        padding: 0.35rem 0.85rem;
        border-radius: 999px;
        color: #0f1f26;
        font-size: 0.85rem;
        font-weight: 700;
        cursor: pointer;
        transition: color 0.22s ease;
    }
    .mode-options input:checked + .mode-option {
        color: #fff;
    }
    .mode-save-button {
        border: 0;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.78);
        color: #78a2d2;
        font-size: 0.78rem;
        font-weight: 700;
        padding: 0.48rem 0.95rem;
        transition: background 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
    }
    .mode-save-button:hover,
    .mode-save-button:focus {
        background: #fff;
        color: #5d8fc6;
        box-shadow: 0 0 0 4px rgba(120, 162, 210, 0.14);
    }
    .status-card {
        border: 1px solid #D6E8ED;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 4px 12px rgba(15, 87, 110, 0.08);
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .status-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background: rgba(11, 95, 118, 0.1);
        color: #0b5f76;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .status-icon svg {
        width: 19px;
        height: 19px;
    }
    .value-section,
    .status-section {
        background: transparent;
        border: 0;
        box-shadow: none;
        padding: 0;
    }
    .gauge-card {
        padding: 1rem;
        min-height: 235px;
    }
    .value-section .gauge-card {
        min-height: 230px;
        justify-content: flex-start;
        gap: 0.9rem;
    }
    .gauge-card-title {
        min-height: 1.4rem;
    }
    .gauge-meta {
        margin-top: auto;
    }
    .gauge-status-line {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
    }
    .gauge-status-dot {
        width: 6px;
        height: 6px;
        border-radius: 999px;
        background: #6c757d;
        flex: 0 0 6px;
    }
    .gauge-status-dot.is-good {
        background: #198754;
    }
    .gauge-status-dot.is-warning {
        background: #c99200;
    }
    .gauge-status-dot.is-offline {
        background: #6c757d;
    }
    .device-card {
        padding: 0.8rem;
        min-height: 126px;
    }
    .status-section .device-card {
        background: rgba(255, 255, 255, 0.82);
        border: 1px solid rgba(255, 255, 255, 0.55);
        border-radius: 14px;
        box-shadow: 0 8px 18px rgba(25, 73, 110, 0.10);
        padding: 1rem;
        min-height: 155px;
    }
    .device-card-heading {
        display: flex;
        align-items: center;
        gap: 0.65rem;
    }
    .status-section .status-icon {
        width: 28px;
        height: 28px;
        border-radius: 999px;
        background: rgba(120, 162, 210, 0.14);
        color: #5fa0d5;
    }
    .status-section .status-icon svg {
        width: 15px;
        height: 15px;
    }
    .device-card-title {
        font-weight: 700;
        line-height: 1.2;
    }
    .status-section .device-card-title {
        color: #0f1f26;
        font-size: 0.82rem;
        letter-spacing: 0.02em;
        text-transform: uppercase;
    }
    .device-card-description {
        color: #6c757d;
        font-size: 0.78rem;
        line-height: 1.25;
        margin: 0.45rem 0 0;
    }
    .status-section .device-card-description {
        font-size: 0.72rem;
        margin-top: 0.7rem;
    }
    .device-card-footer {
        border-top: 1px solid #D6E8ED;
        margin-top: 0.65rem;
        padding-top: 0.6rem;
    }
    .status-section .device-card-footer {
        border-top-color: rgba(120, 162, 210, 0.20);
        margin-top: auto;
        padding-top: 0.85rem;
    }
    .device-card-footer-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
    }
    .status-section .dose-button,
    .status-section .one-shot-button {
        border: 0;
        border-radius: 10px;
        background: #5fa8dd;
        color: #fff;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.62rem 0.75rem;
        text-transform: uppercase;
        box-shadow: 0 6px 12px rgba(95, 168, 221, 0.18);
        transition: background 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
    }
    .status-section .dose-button:hover,
    .status-section .dose-button:focus,
    .status-section .one-shot-button:hover,
    .status-section .one-shot-button:focus {
        background: #4f99cf;
        color: #fff;
        box-shadow: 0 8px 16px rgba(95, 168, 221, 0.25);
        transform: translateY(-1px);
    }
    .status-section .dose-button:disabled,
    .status-section .one-shot-button:disabled {
        background: #9fc4df;
        color: rgba(255, 255, 255, 0.85);
        box-shadow: none;
        transform: none;
    }
    .status-section .form-check-input {
        width: 42px;
        height: 22px;
        border-color: rgba(95, 168, 221, 0.35);
        background-color: #edf5fb;
        cursor: pointer;
    }
    .status-section .form-check-input:checked {
        border-color: #5fa8dd;
        background-color: #5fa8dd;
    }
    .status-section .form-check-input:focus {
        border-color: #5fa8dd;
        box-shadow: 0 0 0 0.2rem rgba(95, 168, 221, 0.16);
    }
    .status-section .device-status-badge {
        background: rgba(255, 255, 255, 0.75) !important;
        color: #0f1f26 !important;
        border: 1px solid rgba(120, 162, 210, 0.20);
        border-radius: 6px;
        font-size: 0.68rem;
        padding: 0.28rem 0.45rem;
    }
    .radial-gauge {
        --gauge-percent: 0%;
        width: 112px;
        aspect-ratio: 1;
        border-radius: 50%;
        background:
            radial-gradient(circle at center, #fff 0 58%, transparent 59%),
            conic-gradient(#0f6c85 var(--gauge-percent), #e3edf0 0);
        display: grid;
        place-items: center;
        margin: 0 auto 10px;
        border: 1px solid #D6E8ED;
    }
    .gauge-value {
        font-size: 1.25rem;
        font-weight: 700;
        line-height: 1;
        color: #0f1f26;
    }
    .gauge-unit {
        font-size: 0.72rem;
        color: #6c757d;
        margin-top: 3px;
    }
    .system-status-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-top: 1rem;
        padding: 0.85rem 1rem;
        border-radius: 999px;
        border: 1px solid rgba(255, 255, 255, 0.55);
        background: rgba(255, 255, 255, 0.30);
        box-shadow:
            0 8px 20px rgba(25, 73, 110, 0.08),
            inset 0 1px 0 rgba(255, 255, 255, 0.38);
    }
    .system-status-message {
        display: inline-flex;
        align-items: center;
        gap: 0.65rem;
        min-width: 0;
        color: #0f1f26;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.02em;
        text-transform: uppercase;
    }
    .system-status-dot {
        width: 14px;
        height: 14px;
        border-radius: 999px;
        background: rgba(240, 211, 106, 0.65);
        box-shadow: 0 0 0 4px rgba(240, 211, 106, 0.14);
        flex: 0 0 14px;
    }
    .system-status-updated {
        flex: 0 0 auto;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.88);
        color: #78a2d2;
        padding: 0.45rem 0.85rem;
        font-size: 0.75rem;
        font-weight: 700;
        box-shadow: 0 4px 12px rgba(25, 73, 110, 0.08);
        white-space: nowrap;
    }
    .system-status-bar.is-online .system-status-dot {
        background: rgba(143, 211, 170, 0.8);
        box-shadow: 0 0 0 4px rgba(143, 211, 170, 0.16);
    }
    .status-card .form-switch {
        padding-left: 0;
        margin-bottom: 0;
    }
    .status-card .form-check-input {
        float: none;
        margin-left: 0;
        width: 46px;
        height: 24px;
        cursor: pointer;
    }
    .text-bg-maroon {
        color: #fff;
        background-color: #7f1d1d;
    }
    .bg-maroon {
        background-color: #7f1d1d !important;
    }
    .btn-maroon {
        color: #fff;
        background-color: #7f1d1d;
        border-color: #7f1d1d;
    }
    .btn-maroon:hover {
        color: #fff;
        background-color: #681818;
        border-color: #681818;
    }
    @media (max-width: 767.98px) {
        .dashboard-glass-card {
            width: 100%;
            margin: 0 auto;
            padding: 16px;
            border-radius: 22px;
        }
        .dashboard-glass-header {
            align-items: flex-start;
            flex-direction: column;
        }
        .control-mode-panel {
            width: 100%;
            flex-wrap: wrap;
        }
        .mode-options {
            flex: 1 1 180px;
        }
        .system-status-bar {
            align-items: flex-start;
            border-radius: 20px;
            flex-direction: column;
        }
        .system-status-updated {
            align-self: flex-start;
        }
        .gauge-card,
        .device-card {
            min-height: auto;
        }
    }
</style>

@if(!$selectedTank)
    <div class="card card-shadow p-4">
        <div class="fw-semibold mb-1">Dashboard</div>
        <div class="small muted mb-3">No tank is selected yet. Request a tank when you want to start monitoring aquarium data.</div>
        <a class="btn btn-primary" href="{{ route('tank-requests.create') }}">Request New Tank</a>
    </div>
@else
    @php
        $lastFeeding = $actions->first(function ($action) {
            return stripos($action->action, 'feed') !== false || stripos($action->action, 'feeder') !== false;
        });
        $doseDevices = [
            [
                'key' => 'ph_up',
                'title' => 'pH Up',
                'description' => 'One short dose to raise acidic water',
                'button' => 'Dose pH Up',
            ],
            [
                'key' => 'ph_down',
                'title' => 'pH Down',
                'description' => 'One short dose to lower alkaline water',
                'button' => 'Dose pH Down',
            ],
        ];
        $statusDevices = [
            [
                'key' => 'topup',
                'title' => 'Water Pump Status',
                'description' => 'Refill or circulate water',
                'state' => !empty($deviceStates['topup']),
            ],
        ];
        $oneShotDevices = [
            [
                'key' => 'feeder',
                'title' => 'Feed Status',
                'description' => 'Run one feeding cycle',
                'button' => 'Feed Now',
            ],
        ];
    @endphp

    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1055;">
        <div id="dashboard-toast" class="toast align-items-center text-bg-maroon border-0" role="alert"
             aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="dashboard-toast-body"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
            </div>
        </div>
    </div>

    <div class="modal fade" id="manualOverrideModal" tabindex="-1" aria-labelledby="manualOverrideTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header">
                    <h5 class="modal-title" id="manualOverrideTitle">Confirm Manual Override</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="manualOverrideMessage"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-maroon" id="manualOverrideConfirm">Turn On Anyway</button>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard-glass-card">
        <div class="dashboard-glass-header">
            <div class="h4 mb-0 dashboard-glass-title">{{ $selectedTank->name }} Dashboard</div>

            <form method="POST" action="{{ route('tanks.mode.update', $selectedTank) }}"
                  class="control-mode-panel" id="mode-form">
                @csrf
                <div class="control-mode-label">Mode:</div>
                <div class="mode-options">
                    <input class="visually-hidden" type="radio" name="control_mode" id="mode-auto"
                           value="auto" {{ $selectedTank->control_mode === 'auto' ? 'checked' : '' }}>
                    <label class="mode-option" for="mode-auto">Auto</label>
                    <input class="visually-hidden" type="radio" name="control_mode" id="mode-manual"
                           value="manual" {{ $selectedTank->control_mode === 'manual' ? 'checked' : '' }}>
                    <label class="mode-option" for="mode-manual">Manual</label>
                </div>
                <button class="mode-save-button" type="submit">Save</button>
            </form>
        </div>

    <div class="value-section mb-3">
        <div class="dashboard-section-title">Current Value</div>
        <div class="row g-3">
            @foreach($sensorReadings as $sensor)
                @php
                    $value = $sensor['value'];
                    $sensorDomId = [
                        'pH' => 'ph',
                        'Turbidity' => 'turbidity',
                        'Water Level' => 'water-level',
                    ][$sensor['key']] ?? \Illuminate\Support\Str::slug($sensor['key']);
                    $gaugePercent = max(0, min(100, (($value - $sensor['gauge_min']) / max(1, ($sensor['gauge_max'] - $sensor['gauge_min']))) * 100));
                    $modeClass = $sensor['mode'] === 'Live Data' ? 'text-bg-primary' : 'text-bg-secondary';
                    $statusClass = $sensor['status'] === 'Good' ? 'text-bg-success' : 'text-bg-maroon';
                    $statusDotClass = $sensor['status'] === 'Good' ? 'is-good' : 'is-warning';
                @endphp
                <div class="col-12 col-md-4">
                    <div class="status-card gauge-card text-center sensor-card"
                         data-sensor="{{ $sensor['key'] }}"
                         data-min="{{ $sensor['min'] }}"
                         data-max="{{ $sensor['max'] }}"
                         data-gauge-min="{{ $sensor['gauge_min'] }}"
                         data-gauge-max="{{ $sensor['gauge_max'] }}"
                         data-unit="{{ $sensor['unit'] }}"
                         data-value="{{ $value }}"
                         data-low-action="{{ $sensor['device_low'] }}"
                         data-high-action="{{ $sensor['device_high'] }}">
                        <div class="fw-semibold gauge-card-title">{{ $sensor['title'] }}</div>
                        <div class="radial-gauge" id="{{ $sensorDomId }}-gauge" style="--gauge-percent: {{ $gaugePercent }}%;">
                            <div>
                                <div class="gauge-value" id="{{ $sensorDomId }}-value">{{ number_format($value, 2) }}</div>
                                <div class="gauge-unit" id="{{ $sensorDomId }}-unit">{{ $sensor['unit'] }}</div>
                            </div>
                        </div>
                        <div class="gauge-meta">
                            <div class="small muted mb-2">Safe: {{ $sensor['min'] }} - {{ $sensor['max'] }} {{ $sensor['unit'] }}</div>
                            <div class="small muted mb-2">{{ $sensor['range_source'] }}</div>
                            <div class="small fw-semibold gauge-status-line">
                                <span class="gauge-status-dot {{ $statusDotClass }}"></span>
                                <span class="sensor-status" id="{{ $sensorDomId }}-status">{{ $sensor['status'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="system-status-bar">
            <div class="system-status-message">
                <span class="system-status-dot"></span>
                <span id="sensor-connection-status">System status: connection unavailable.</span>
            </div>
            <div class="system-status-updated" id="last-updated">Updated: --:--:--</div>
        </div>
    </div>

    <div class="status-section">
        <div class="dashboard-section-title">Current Status</div>
        <div class="row g-3 align-items-stretch">
            @foreach($doseDevices as $device)
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="status-card device-card">
                        <div>
                            <div class="device-card-heading">
                                <div class="status-icon">
                                    <svg viewBox="0 0 24 24" fill="none">
                                        <path d="M7 3h10v5l-5 5-5-5V3Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                        <path d="M12 13v8" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                    </svg>
                                </div>
                                <div class="device-card-title">{{ $device['title'] }}</div>
                            </div>
                            <div class="device-card-description">{{ $device['description'] }}</div>
                        </div>
                        <div class="device-card-footer">
                            <button class="btn btn-sm btn-primary w-100 dose-button" type="button"
                                    data-device="{{ $device['key'] }}"
                                    data-label="{{ $device['title'] }}">
                                {{ $device['button'] }}
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
            @foreach($statusDevices as $device)
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="status-card device-card">
                        <div>
                            <div class="device-card-heading">
                                <div class="status-icon">
                                    <svg viewBox="0 0 24 24" fill="none">
                                        <path d="M6 4h12v8a6 6 0 0 1-12 0V4Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                        <path d="M9 4V2m6 2V2M8 20h8" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                    </svg>
                                </div>
                                <div class="device-card-title">{{ $device['title'] }}</div>
                            </div>
                            <div class="device-card-description">{{ $device['description'] }}</div>
                        </div>
                        <div class="device-card-footer">
                            <div class="device-card-footer-row">
                                <div class="form-check form-switch">
                                    <input class="form-check-input device-toggle" type="checkbox"
                                           id="{{ $device['key'] }}-toggle"
                                           data-device="{{ $device['key'] }}"
                                           {{ $device['state'] ? 'checked' : '' }}>
                                    <label class="form-check-label visually-hidden" for="{{ $device['key'] }}-toggle">
                                        Toggle {{ $device['title'] }}
                                    </label>
                                </div>
                                <span class="badge {{ $device['state'] ? 'text-bg-success' : 'text-bg-secondary' }} device-status-badge"
                                      data-device-status="{{ $device['key'] }}">
                                    {{ $device['state'] ? 'ON' : 'OFF' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            @foreach($oneShotDevices as $device)
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="status-card device-card">
                        <div>
                            <div class="device-card-heading">
                                <div class="status-icon">
                                    <svg viewBox="0 0 24 24" fill="none">
                                        <path d="M5 11h14v9H5v-9Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                        <path d="M8 11V7a4 4 0 0 1 8 0v4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                    </svg>
                                </div>
                                <div class="device-card-title">{{ $device['title'] }}</div>
                            </div>
                            <div class="device-card-description">{{ $device['description'] }}</div>
                        </div>
                        <div class="device-card-footer">
                            <button class="btn btn-sm btn-outline-primary w-100 one-shot-button" type="button"
                                    data-device="{{ $device['key'] }}"
                                    data-label="{{ $device['title'] }}">
                                {{ $device['button'] }}
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    </div>

    <script>
        const sensorCards = Array.from(document.querySelectorAll('.sensor-card'));
        const overallStatus = document.getElementById('overall-status');
        const tankHealth = document.getElementById('tank-health');
        const recommendationList = document.getElementById('recommendation-list');
        const toastEl = document.getElementById('dashboard-toast');
        const toastBody = document.getElementById('dashboard-toast-body');
        let toastInstance = null;
        const deviceToggles = Array.from(document.querySelectorAll('.device-toggle'));
        const doseButtons = Array.from(document.querySelectorAll('.dose-button'));
        const oneShotButtons = Array.from(document.querySelectorAll('.one-shot-button'));
        let sensorStatusMap = {};
        let recommendedActions = [];
        let pendingManualOverride = null;
        let modeSubmitTimer = null;
        const csrfToken = '{{ csrf_token() }}';
        const deviceStateUrl = '{{ route('tanks.devices.index', $selectedTank) }}';
        const deviceUpdateUrl = '{{ route('tanks.devices.update', $selectedTank) }}';
        const doseCommandUrl = '{{ route('tanks.devices.dose', $selectedTank) }}';
        const latestReadingsUrl = '{{ url('/api/tanks/' . $selectedTank->id . '/latest-readings') }}';
        const sensorConnectionStatus = document.getElementById('sensor-connection-status');
        const lastUpdatedEl = document.getElementById('last-updated');
        const systemStatusBar = document.querySelector('.system-status-bar');
        const modeForm = document.getElementById('mode-form');
        const manualOverrideEl = document.getElementById('manualOverrideModal');
        const manualOverrideMessage = document.getElementById('manualOverrideMessage');
        const manualOverrideConfirm = document.getElementById('manualOverrideConfirm');
        let manualOverrideModal = null;
        function getControlMode() {
            const selected = modeForm ? modeForm.querySelector('input[name="control_mode"]:checked') : null;
            return selected ? selected.value : '{{ $selectedTank->control_mode ?? 'auto' }}';
        }

        const deviceMap = {
            topup: document.getElementById('topup-toggle'),
        };
        const deviceStatusBadges = Array.from(document.querySelectorAll('[data-device-status]'));
        let currentDeviceStates = @json($deviceStates);

        function setAlert(message) {
            if (!toastInstance) {
                toastInstance = new bootstrap.Toast(toastEl, { delay: 4000 });
            }
            if (!message) {
                toastBody.textContent = '';
                return;
            }
            toastBody.textContent = message;
            toastInstance.show();
        }

        function setSensorConnectionAvailable(isAvailable) {
            if (!systemStatusBar) return;
            systemStatusBar.classList.toggle('is-online', isAvailable);
        }

        function setSensorConnectionMessage(message) {
            if (!sensorConnectionStatus) return;
            sensorConnectionStatus.textContent = message || 'System status: connection unavailable.';
        }

        function formatReadingTime(value) {
            const date = value ? new Date(value) : new Date();
            if (Number.isNaN(date.getTime())) {
                return new Date().toLocaleTimeString();
            }
            return date.toLocaleTimeString();
        }

        function updateLastUpdated(recordedAt) {
            if (!lastUpdatedEl) return;
            lastUpdatedEl.textContent = 'Updated: ' + formatReadingTime(recordedAt);
        }

        function applyLatestReadings(data) {
            const readings = data.readings || {};
            const responseKeyToSensor = {
                ph: 'pH',
                turbidity: 'Turbidity',
                water_level: 'Water Level',
            };
            let newestRecordedAt = null;
            let didApplyReading = false;
            let hasOfflineReading = false;

            Object.keys(responseKeyToSensor).forEach((responseKey) => {
                const reading = readings[responseKey];
                if (!reading) {
                    return;
                }

                const sensorName = responseKeyToSensor[responseKey];
                const card = sensorCards.find((item) => item.dataset.sensor === sensorName);
                if (!card) {
                    return;
                }

                card.dataset.connectionStatus = reading.status || 'offline';
                card.dataset.isStale = reading.is_stale ? 'true' : 'false';
                if (card.dataset.connectionStatus === 'offline' || card.dataset.isStale === 'true') {
                    hasOfflineReading = true;
                }

                if (reading.value !== null && reading.value !== undefined) {
                    const value = parseFloat(reading.value);
                    if (!Number.isNaN(value)) {
                        card.dataset.value = String(value);
                        card.dataset.unit = reading.unit || card.dataset.unit || '';
                        didApplyReading = true;

                        const unitEl = card.querySelector('.gauge-unit');
                        if (unitEl) {
                            unitEl.textContent = card.dataset.unit;
                        }
                    }
                }

                if (reading.recorded_at) {
                    const recordedDate = new Date(reading.recorded_at);
                    const currentNewest = newestRecordedAt ? new Date(newestRecordedAt) : null;
                    if (!currentNewest || recordedDate > currentNewest) {
                        newestRecordedAt = reading.recorded_at;
                    }
                }
            });

            if (didApplyReading) {
                updateLastUpdated(newestRecordedAt);
            }
            if (data.device_status === 'offline') {
                setSensorConnectionMessage('System status: IoT device offline. Displaying cached readings.');
            } else {
                setSensorConnectionMessage('System status: one or more sensors are offline or stale.');
            }
            setSensorConnectionAvailable(!hasOfflineReading);
            updateDashboard();
        }

        function pollLatestReadings() {
            fetch(latestReadingsUrl + '?timestamp=' + Date.now(), {
                headers: {
                    'Accept': 'application/json',
                    'Cache-Control': 'no-store',
                },
                cache: 'no-store',
            })
                .then((response) => {
                    if (!response.ok) {
                        throw new Error('Failed to load latest readings.');
                    }
                    return response.json();
                })
                .then((data) => {
                    console.log('Latest readings response', data);
                    if (!data.success) {
                        throw new Error('Latest readings response was not successful.');
                    }
                    applyLatestReadings(data);
                })
                .catch(() => {
                    setSensorConnectionMessage('System status: connection unavailable.');
                    setSensorConnectionAvailable(false);
                });
        }

        function syncDeviceStates() {
            fetch(deviceStateUrl, {
                headers: { 'Accept': 'application/json' },
                cache: 'no-store',
            })
                .then((response) => {
                    if (!response.ok) {
                        throw new Error('Failed to load device states.');
                    }
                    return response.json();
                })
                .then((data) => {
                    const states = data.states || {};
                    Object.keys(deviceMap).forEach((key) => {
                        const state = states.hasOwnProperty(key) ? states[key] : false;
                        if (deviceMap[key]) {
                            deviceMap[key].checked = !!state;
                        }
                    });
                    currentDeviceStates = states;
                    updateDeviceStatusLabels(states);
                })
                .catch(() => {});
        }

        function updateDeviceStatusLabels(states) {
            deviceStatusBadges.forEach((badge) => {
                const key = badge.dataset.deviceStatus;
                const isOn = !!states[key];
                badge.textContent = isOn ? 'ON' : 'OFF';
                badge.className = 'badge device-status-badge ' + (isOn ? 'text-bg-success' : 'text-bg-secondary');
            });
        }

        function scoreStatus(value, min, max) {
            if (value === null || min === null || max === null) {
                return 'unknown';
            }
            if (value >= min && value <= max) {
                return 'good';
            }
            return 'warning';
        }

        function statusBadge(status) {
            if (status === 'good') return { label: 'Good', className: 'text-bg-success' };
            if (status === 'warning') return { label: 'Warning', className: 'text-bg-maroon' };
            if (status === 'offline') return { label: 'Offline', className: 'text-bg-secondary' };
            return { label: 'Unknown', className: 'text-bg-secondary' };
        }

        function updateAutoDeviceStates() {
            if (getControlMode() !== 'auto') return;

            const desired = {
                topup: false,
            };

            recommendedActions.forEach((rec) => {
                if (isActionForDevice(rec.action, 'topup')) desired.topup = true;
            });

            Object.keys(desired).forEach((key) => {
                const toggle = deviceMap[key];
                if (toggle && toggle.checked !== desired[key]) {
                    toggle.checked = desired[key];
                    persistDeviceState(key, desired[key]);
                }
                if (!toggle && !!currentDeviceStates[key] !== desired[key]) {
                    currentDeviceStates[key] = desired[key];
                    persistDeviceState(key, desired[key]);
                }
            });
            updateDeviceStatusLabels(currentDeviceStates);
        }

        function persistDeviceState(deviceKey, state) {
            const toggle = deviceMap[deviceKey];
            const previousState = !!currentDeviceStates[deviceKey];
            if (toggle) {
                toggle.disabled = true;
            }

            currentDeviceStates[deviceKey] = !!state;
            updateDeviceStatusLabels(currentDeviceStates);
            return fetch(deviceUpdateUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    device_key: deviceKey,
                    state: !!state,
                }),
            })
                .then((response) => {
                    if (!response.ok) {
                        return response.json().catch(() => ({})).then((data) => {
                            throw new Error(data.error || data.message || 'Failed to update device state.');
                        });
                    }

                    return response.json();
                })
                .then((data) => {
                    if (data.states) {
                        currentDeviceStates = data.states;
                        Object.keys(deviceMap).forEach((key) => {
                            if (deviceMap[key]) {
                                deviceMap[key].checked = !!currentDeviceStates[key];
                            }
                        });
                        updateDeviceStatusLabels(currentDeviceStates);
                    }

                    setAlert((state ? 'ON' : 'OFF') + ' command sent.');
                })
                .catch((error) => {
                    currentDeviceStates[deviceKey] = previousState;
                    if (toggle) {
                        toggle.checked = previousState;
                    }
                    updateDeviceStatusLabels(currentDeviceStates);
                    setAlert(error.message || 'Failed to update device state.');
                })
                .finally(() => {
                    if (toggle) {
                        toggle.disabled = false;
                    }
                });
        }

        function requestDose(deviceKey, label, button) {
            if (getControlMode() === 'auto') {
                setAlert('Auto mode is enabled. Switch to manual before dosing pH.');
                return;
            }

            const relatedStatus = sensorStatusMap.pH || null;
            if (relatedStatus && relatedStatus.status === 'good') {
                const confirmed = window.confirm('pH is in good condition. Request one ' + label + ' dose anyway?');
                if (!confirmed) {
                    return;
                }
            }

            button.disabled = true;
            fetch(doseCommandUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    device_key: deviceKey,
                }),
            })
                .then((response) => {
                    if (!response.ok) {
                        return response.json().catch(() => ({})).then((data) => {
                            throw new Error(data.error || data.message || 'Failed to request pH dose.');
                        });
                    }

                    return response.json();
                })
                .then((data) => {
                    setAlert(data.message || ('One ' + label + ' dose has been requested.'));
                })
                .catch((error) => {
                    setAlert(error.message || 'Failed to request pH dose.');
                })
                .finally(() => {
                    button.disabled = false;
                });
        }

        function requestOneShot(deviceKey, label, button) {
            if (getControlMode() === 'auto') {
                setAlert('Auto mode is enabled. Switch to manual before using this control.');
                return;
            }

            button.disabled = true;
            persistDeviceState(deviceKey, true)
                .finally(() => {
                    button.disabled = false;
                });
        }

        function updateDashboard() {
            const recommendations = [];
            let worst = 'good';
            const nextStatusMap = {};
            const nextRecommended = [];
            let hasOfflineSensor = false;

            if (getControlMode() === 'auto') {
                deviceToggles.forEach((toggle) => {
                    toggle.checked = false;
                });
            }

            sensorCards.forEach((card) => {
                const min = parseFloat(card.dataset.min);
                const max = parseFloat(card.dataset.max);
                const unit = card.dataset.unit || '';
                const value = parseFloat(card.dataset.value);
                const isOffline = card.dataset.connectionStatus === 'offline' || card.dataset.isStale === 'true';
                if (isOffline) {
                    hasOfflineSensor = true;
                }
                const status = isOffline
                    ? 'offline'
                    : scoreStatus(isNaN(value) ? null : value, isNaN(min) ? null : min, isNaN(max) ? null : max);
                const badge = statusBadge(status);

                if (status === 'offline') {
                    worst = 'offline';
                } else if (status === 'warning' && worst !== 'offline') {
                    worst = 'warning';
                }

                const statusEl = card.querySelector('.sensor-status');
                if (statusEl) {
                    statusEl.textContent = badge.label;
                    statusEl.className = 'sensor-status';
                }
                const statusDot = card.querySelector('.gauge-status-dot');
                if (statusDot) {
                    statusDot.className = 'gauge-status-dot '
                        + (status === 'good' ? 'is-good' : (status === 'offline' ? 'is-offline' : 'is-warning'));
                }

                const valueEl = card.querySelector('.sensor-value');
                if (valueEl) {
                    valueEl.innerHTML = (isNaN(value) ? '--' : value.toFixed(2)) + ' <span class="fs-6">' + unit + '</span>';
                }

                const gauge = card.querySelector('.radial-gauge');
                const gaugeValue = card.querySelector('.gauge-value');
                const gaugeMin = parseFloat(card.dataset.gaugeMin);
                const gaugeMax = parseFloat(card.dataset.gaugeMax);
                if (gauge) {
                    const percent = !isNaN(value) && !isNaN(gaugeMin) && !isNaN(gaugeMax)
                        ? Math.max(0, Math.min(100, ((value - gaugeMin) / (gaugeMax - gaugeMin)) * 100))
                        : 0;
                    gauge.style.setProperty('--gauge-percent', percent + '%');
                }
                if (gaugeValue) {
                    gaugeValue.textContent = isNaN(value) ? '--' : value.toFixed(2);
                }

                const bar = card.querySelector('.sensor-bar');
                if (bar) {
                    if (!isNaN(min) && !isNaN(max) && !isNaN(value)) {
                        const percent = Math.max(0, Math.min(100, ((value - min) / (max - min)) * 100));
                        bar.style.width = percent + '%';
                        bar.className = 'progress-bar sensor-bar ' + (status === 'good' ? 'bg-success' : 'bg-maroon');
                    } else {
                        bar.style.width = '0%';
                        bar.className = 'progress-bar sensor-bar bg-secondary';
                    }
                }

                const recommendationEl = card.querySelector('.sensor-recommendation');
                const automationEl = card.querySelector('.sensor-automation');

                nextStatusMap[card.dataset.sensor] = {
                    status,
                    action: null,
                };

                if (status === 'good') {
                    if (recommendationEl) recommendationEl.textContent = 'Recommendation: Stable';
                    if (automationEl) automationEl.textContent = 'Automation: None';
                } else if (status === 'warning') {
                    const action = value < min ? card.dataset.lowAction : card.dataset.highAction;
                    const detail = buildActionDetail(card.dataset.sensor, action);
                    if (recommendationEl) recommendationEl.textContent = 'Recommendation: Check parameter';
                    if (automationEl) automationEl.textContent = 'Automation: ' + action;
                    recommendations.push(card.dataset.sensor + ' is out of range. ' + detail);
                    nextStatusMap[card.dataset.sensor].action = action;
                    nextRecommended.push({ sensor: card.dataset.sensor, status, action });
                } else if (status === 'offline') {
                    if (recommendationEl) recommendationEl.textContent = 'Recommendation: Check sensor or WiFi';
                    if (automationEl) automationEl.textContent = 'Automation: Paused';
                    recommendations.push(card.dataset.sensor + ' has no fresh reading. Check sensor cable, power, and hotspot/WiFi.');
                } else {
                    if (recommendationEl) recommendationEl.textContent = 'Recommendation: No data';
                    if (automationEl) automationEl.textContent = 'Automation: --';
                }
            });

            const overallBadge = statusBadge(worst);
            if (overallStatus) {
                overallStatus.textContent = overallBadge.label;
                overallStatus.className = 'badge ' + overallBadge.className;
            }
            if (tankHealth) {
                tankHealth.textContent = overallBadge.label;
                tankHealth.className = 'badge ' + overallBadge.className;
            }

            if (recommendationList) {
                recommendationList.innerHTML = '';
                if (recommendations.length === 0) {
                    const li = document.createElement('li');
                    li.textContent = 'No recommendations yet.';
                    recommendationList.appendChild(li);
                } else {
                    recommendations.forEach((rec) => {
                        const li = document.createElement('li');
                        li.textContent = rec;
                        recommendationList.appendChild(li);
                    });
                }
            }

            if (sensorConnectionStatus) {
                setSensorConnectionAvailable(!hasOfflineSensor);
            }

            sensorStatusMap = nextStatusMap;
            recommendedActions = nextRecommended;

            updateAutoDeviceStates();
            if (getControlMode() === 'manual') {
                syncDeviceStates();
            }
        }

        function buildActionDetail(sensor, action) {
            const key = sensor.toLowerCase();
            if (key.includes('turbidity')) {
                return 'Check filter condition.';
            }
            if (key === 'ph') {
                return action + ' may be needed.';
            }
            if (key.includes('water level')) {
                return 'Check water pump and tank level.';
            }
            return 'Check sensor value and safe range.';
        }

        function saveReadings() {
            const readings = sensorCards.map((card) => {
                const value = card.dataset.value;
                return {
                    parameter: card.dataset.sensor,
                    value: value === null || value === undefined ? '' : value,
                    unit: card.dataset.unit || '',
                };
            });

            fetch('{{ route('tanks.readings.store', $selectedTank) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ readings }),
            }).catch(() => {
                setAlert('Failed to save readings.');
            });
        }

        function isActionForDevice(action, device) {
            const text = action.toLowerCase();
            if (device === 'ph_up') return text.includes('ph up');
            if (device === 'ph_down') return text.includes('ph down');
            if (device === 'topup') return text.includes('top-up') || text.includes('topup') || text.includes('valve') || text.includes('pump');
            if (device === 'feeder') return text.includes('feeder') || text.includes('feed');
            return false;
        }

        function deviceRelatedSensor(device) {
            if (device === 'ph_up') return 'pH';
            if (device === 'ph_down') return 'pH';
            if (device === 'topup') return 'Water Level';
            if (device === 'feeder') return 'Feeding';
            return null;
        }

        function showManualOverride(toggle, message) {
            pendingManualOverride = {
                toggle,
                device: toggle.dataset.device,
            };
            manualOverrideMessage.textContent = message;
            if (!manualOverrideModal) {
                manualOverrideModal = new bootstrap.Modal(manualOverrideEl);
            }
            manualOverrideModal.show();
        }

        if (manualOverrideConfirm) {
            manualOverrideConfirm.addEventListener('click', () => {
                if (!pendingManualOverride) return;

                pendingManualOverride.toggle.checked = true;
                persistDeviceState(pendingManualOverride.device, true);
                setAlert('Manual override applied. Device turned on.');

                pendingManualOverride = null;
                manualOverrideModal.hide();
            });
        }

        if (manualOverrideEl) {
            manualOverrideEl.addEventListener('hidden.bs.modal', () => {
                if (pendingManualOverride) {
                    pendingManualOverride.toggle.checked = false;
                    pendingManualOverride = null;
                }
            });
        }

        deviceToggles.forEach((toggle) => {
            toggle.addEventListener('click', (event) => {
                if (getControlMode() === 'auto') {
                    event.preventDefault();
                    toggle.checked = !!currentDeviceStates[toggle.dataset.device];
                    setAlert('Auto mode is enabled. System controls this device automatically.');
                    return;
                }
                const device = toggle.dataset.device;
                const desiredState = toggle.checked;
                const relatedSensor = deviceRelatedSensor(device);
                const relatedStatus = relatedSensor ? sensorStatusMap[relatedSensor] : null;

                if (desiredState && relatedStatus && relatedStatus.status === 'good') {
                    event.preventDefault();
                    toggle.checked = false;
                    const msg = 'Device not needed. ' + relatedSensor + ' is in good condition. Turning on this pump may affect water quality. Continue?';
                    setAlert(msg);
                    showManualOverride(toggle, msg);
                    return;
                }

                const recommended = recommendedActions.find((rec) => isActionForDevice(rec.action, device));
                if (desiredState && recommendedActions.length > 0 && !recommended) {
                    event.preventDefault();
                    toggle.checked = false;
                    const msg = 'Not recommended now. ' + recommendedActions[0].sensor + ' is ' +
                        recommendedActions[0].status + '. Suggested: ' + recommendedActions[0].action + '. Continue?';
                    setAlert(msg);
                    showManualOverride(toggle, msg);
                    return;
                }

                setAlert('');
                persistDeviceState(device, desiredState);
            });
        });

        doseButtons.forEach((button) => {
            button.addEventListener('click', () => {
                requestDose(button.dataset.device, button.dataset.label, button);
            });
        });

        oneShotButtons.forEach((button) => {
            button.addEventListener('click', () => {
                requestOneShot(button.dataset.device, button.dataset.label, button);
            });
        });

        function updateCommandButtons() {
            const disabled = getControlMode() === 'auto';
            doseButtons.forEach((button) => {
                button.disabled = disabled;
            });
            oneShotButtons.forEach((button) => {
                button.disabled = disabled;
            });
        }

        updateDashboard();
        updateCommandButtons();
        pollLatestReadings();
        if (window.aquaWatchLatestReadingsInterval) {
            clearInterval(window.aquaWatchLatestReadingsInterval);
        }
        window.aquaWatchLatestReadingsInterval = setInterval(pollLatestReadings, 2000);
        syncDeviceStates();
        setInterval(syncDeviceStates, 3000);

        if (modeForm) {
            modeForm.querySelectorAll('input[name="control_mode"]').forEach((radio) => {
                radio.addEventListener('change', () => {
                    updateCommandButtons();
                    clearTimeout(modeSubmitTimer);
                    modeSubmitTimer = setTimeout(() => modeForm.submit(), 280);
                });
            });
        }

    </script>
@endif
@endsection

@extends('user.layouts.app')

@section('title', 'Dashboard')

@section('content')
<style>
    .dashboard-section-title {
        font-weight: 700;
        margin-bottom: 10px;
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
        background: #fff;
        padding: 1rem;
    }
    .gauge-card {
        padding: 1rem;
        min-height: 235px;
    }
    .device-card {
        padding: 0.8rem;
        min-height: 126px;
    }
    .device-card-heading {
        display: flex;
        align-items: center;
        gap: 0.65rem;
    }
    .device-card-title {
        font-weight: 700;
        line-height: 1.2;
    }
    .device-card-description {
        color: #6c757d;
        font-size: 0.78rem;
        line-height: 1.25;
        margin: 0.45rem 0 0;
    }
    .device-card-footer {
        border-top: 1px solid #D6E8ED;
        margin-top: 0.65rem;
        padding-top: 0.6rem;
    }
    .device-card-footer-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
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
        .gauge-card,
        .device-card {
            min-height: auto;
        }
    }
</style>

@if(!$selectedTank)
    <div class="card card-shadow p-4">
        <div class="fw-semibold mb-1">Dashboard</div>
        <div class="small muted mb-3">No tank is selected yet. Add a tank when you want to start monitoring aquarium data.</div>
        <a class="btn btn-primary" href="{{ route('tanks.create') }}">Add Tank</a>
    </div>
@else
    @php
        $lastFeeding = $actions->first(function ($action) {
            return stripos($action->action, 'feed') !== false || stripos($action->action, 'feeder') !== false;
        });
        $statusDevices = [
            [
                'key' => 'ph_up',
                'title' => 'pH Up',
                'description' => 'Raises acidic water',
                'state' => !empty($deviceStates['ph_up']),
            ],
            [
                'key' => 'ph_down',
                'title' => 'pH Down',
                'description' => 'Lowers alkaline water',
                'state' => !empty($deviceStates['ph_down']),
            ],
            [
                'key' => 'topup',
                'title' => 'Water Pump Status',
                'description' => 'Refill or circulate water',
                'state' => !empty($deviceStates['topup']),
            ],
            [
                'key' => 'feeder',
                'title' => 'Feed Status',
                'description' => 'Manual feeding control',
                'state' => !empty($deviceStates['feeder']),
            ],
        ];
    @endphp

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div class="h4 mb-0 fw-bold">{{ $selectedTank->name }} Dashboard</div>
    </div>

    <div class="card card-shadow p-3 mb-3">
        <form method="POST" action="{{ route('tanks.mode.update', $selectedTank) }}"
              class="d-flex flex-wrap align-items-center gap-3" id="mode-form">
            @csrf
            <div class="fw-semibold">Control Mode</div>
            <div class="form-check form-check-inline mb-0">
                <input class="form-check-input" type="radio" name="control_mode" id="mode-auto"
                       value="auto" {{ $selectedTank->control_mode === 'auto' ? 'checked' : '' }}>
                <label class="form-check-label" for="mode-auto">Auto</label>
            </div>
            <div class="form-check form-check-inline mb-0">
                <input class="form-check-input" type="radio" name="control_mode" id="mode-manual"
                       value="manual" {{ $selectedTank->control_mode === 'manual' ? 'checked' : '' }}>
                <label class="form-check-label" for="mode-manual">Manual</label>
            </div>
            <button class="btn btn-sm btn-outline-primary" type="submit">Save Mode</button>
        </form>
    </div>

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

    <div class="card card-shadow value-section mb-3">
        <div class="dashboard-section-title">Current Value</div>
        <div class="row g-3">
            @foreach($sensorReadings as $sensor)
                @php
                    $value = $sensor['value'];
                    $gaugePercent = max(0, min(100, (($value - $sensor['gauge_min']) / max(1, ($sensor['gauge_max'] - $sensor['gauge_min']))) * 100));
                    $modeClass = $sensor['mode'] === 'Live Data' ? 'text-bg-primary' : 'text-bg-secondary';
                    $statusClass = $sensor['status'] === 'Good' ? 'text-bg-success' : 'text-bg-maroon';
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
                        <div class="radial-gauge" style="--gauge-percent: {{ $gaugePercent }}%;">
                            <div>
                                <div class="gauge-value">{{ number_format($value, 2) }}</div>
                                <div class="gauge-unit">{{ $sensor['unit'] }}</div>
                            </div>
                        </div>
                        <div class="fw-semibold">{{ $sensor['title'] }} Gauge</div>
                        <div class="small muted mb-2">Safe: {{ $sensor['min'] }} - {{ $sensor['max'] }} {{ $sensor['unit'] }}</div>
                        <div class="small muted mb-2">{{ $sensor['range_source'] }}</div>
                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                            <span class="badge {{ $modeClass }}">{{ $sensor['mode'] }}</span>
                            <span class="badge {{ $statusClass }} sensor-status">{{ $sensor['status'] }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="card card-shadow status-section">
        <div class="dashboard-section-title">Current Status</div>
        <div class="row g-3 align-items-stretch">
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
        let sensorStatusMap = {};
        let recommendedActions = [];
        let pendingManualOverride = null;
        const csrfToken = '{{ csrf_token() }}';
        const deviceStateUrl = '{{ route('tanks.devices.index', $selectedTank) }}';
        const deviceUpdateUrl = '{{ route('tanks.devices.update', $selectedTank) }}';
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
            ph_up: document.getElementById('ph_up-toggle'),
            ph_down: document.getElementById('ph_down-toggle'),
            topup: document.getElementById('topup-toggle'),
            feeder: document.getElementById('feeder-toggle'),
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
            return { label: 'Unknown', className: 'text-bg-secondary' };
        }

        function updateAutoDeviceStates() {
            if (getControlMode() !== 'auto') return;

            const desired = {
                ph_up: false,
                ph_down: false,
                topup: false,
                feeder: false,
            };

            recommendedActions.forEach((rec) => {
                if (isActionForDevice(rec.action, 'ph_up')) desired.ph_up = true;
                if (isActionForDevice(rec.action, 'ph_down')) desired.ph_down = true;
                if (isActionForDevice(rec.action, 'topup')) desired.topup = true;
                if (isActionForDevice(rec.action, 'feeder')) desired.feeder = true;
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
            currentDeviceStates[deviceKey] = !!state;
            updateDeviceStatusLabels(currentDeviceStates);
            fetch(deviceUpdateUrl, {
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
            }).catch(() => {
                setAlert('Failed to update device state.');
            });
        }

        function updateDashboard() {
            const recommendations = [];
            let worst = 'good';
            const nextStatusMap = {};
            const nextRecommended = [];

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
                const status = scoreStatus(isNaN(value) ? null : value, isNaN(min) ? null : min, isNaN(max) ? null : max);
                const badge = statusBadge(status);

                if (status === 'warning') worst = 'warning';

                const statusEl = card.querySelector('.sensor-status');
                if (statusEl) {
                    statusEl.textContent = badge.label;
                    statusEl.className = 'badge ' + badge.className + ' sensor-status';
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

        updateDashboard();
        syncDeviceStates();
        setInterval(syncDeviceStates, 3000);

        if (modeForm) {
            modeForm.querySelectorAll('input[name="control_mode"]').forEach((radio) => {
                radio.addEventListener('change', () => {
                    modeForm.submit();
                });
            });
        }
    </script>
@endif
@endsection

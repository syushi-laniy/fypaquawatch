@extends('user.layouts.app')

@section('title', 'Sensor History')

@section('content')
<style>
    .history-page {
        max-width: 1180px;
        margin: 0 auto;
    }
    .history-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1rem;
    }
    .history-title {
        font-size: 1.65rem;
        font-weight: 800;
        margin: 0 0 0.25rem;
    }
    .history-subtitle {
        color: #52656d;
        margin: 0;
    }
    .history-filter {
        min-width: 210px;
    }
    .history-card {
        border: 1px solid #D6E8ED;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 4px 12px rgba(15, 87, 110, 0.08);
    }
    .demo-note {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        border: 1px solid #F0D36A;
        border-radius: 999px;
        background: #FFF7D8;
        color: #765800;
        font-size: 0.82rem;
        font-weight: 700;
        padding: 0.4rem 0.7rem;
        margin-bottom: 1rem;
    }
    .demo-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #C99200;
    }
    .chart-card {
        padding: 1rem 1rem 0.75rem;
        margin-bottom: 1rem;
    }
    .section-heading {
        font-weight: 800;
        margin-bottom: 0.9rem;
    }
    .chart-wrap {
        position: relative;
        width: 100%;
        height: 330px;
    }
    .table-card {
        overflow: hidden;
    }
    .table-card-header {
        padding: 1rem 1rem 0.2rem;
    }
    .history-table {
        margin: 0;
        vertical-align: middle;
    }
    .history-table thead th {
        border-bottom-color: #D6E8ED;
        background: #fff;
        color: #52656d;
        font-size: 0.78rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        white-space: nowrap;
    }
    .history-table tbody td {
        border-color: #E8EFF1;
    }
    .status-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 72px;
        border-radius: 999px;
        padding: 0.3rem 0.65rem;
        font-size: 0.78rem;
        font-weight: 800;
    }
    .status-good {
        border: 1px solid #8FD3AA;
        background: #E8F7EF;
        color: #187044;
    }
    .status-warning {
        border: 1px solid #E8A1A1;
        background: #FCEAEA;
        color: #7f1d1d;
    }
    .empty-history {
        padding: 3rem 1rem;
        text-align: center;
        color: #52656d;
    }
    .history-pager {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem;
        border-top: 1px solid #E8EFF1;
    }
    .history-page-btn {
        border: 2px solid #E2EDF3;
        border-radius: 10px;
        background: #fff;
        color: #52656d;
        font-weight: 700;
        text-decoration: none;
        padding: 0.55rem 0.95rem;
        box-shadow: 0 2px 6px rgba(31, 58, 95, 0.04);
    }
    .history-page-btn:hover,
    .history-page-btn:focus {
        border-color: #CFE2EC;
        background: #F8FCFD;
        color: #0b5f76;
    }
    .history-page-btn.disabled {
        color: #A0AEB6;
        pointer-events: none;
        opacity: 0.75;
    }
    @media (max-width: 767.98px) {
        .history-filter {
            width: 100%;
        }
        .chart-wrap {
            height: 280px;
        }
    }
</style>

<div class="history-page">
    <div class="history-header">
        <div>
            <h1 class="history-title">Sensor History</h1>
            <p class="history-subtitle">
                {{ $selectedTank ? 'Viewing sensor trends for ' . $selectedTank->name . '.' : 'Select or add a tank to view sensor history.' }}
            </p>
        </div>

        @if($selectedTank)
            <form method="GET" action="{{ route('sensor-history.index') }}" class="history-filter">
                <label class="form-label small fw-semibold" for="parameter-filter">Filter by parameter</label>
                <select class="form-select" id="parameter-filter" name="parameter" onchange="this.form.submit()">
                    <option value="all" {{ $selectedParameter === 'all' ? 'selected' : '' }}>All Parameters</option>
                    @foreach($parameters as $parameter)
                        <option value="{{ $parameter }}" {{ $selectedParameter === $parameter ? 'selected' : '' }}>{{ $parameter }}</option>
                    @endforeach
                </select>
            </form>
        @endif
    </div>

    @if(!$selectedTank)
        <div class="history-card empty-history">
            <div class="fw-semibold mb-2">No tank selected</div>
            <div class="small mb-3">Request a tank before viewing sensor history.</div>
            <a class="btn btn-primary" href="{{ route('tank-requests.create') }}">Request New Tank</a>
        </div>
    @else
        @if($isDemo)
            <div class="demo-note">
                <span class="demo-dot"></span>
                <span>Demo data shown until IoT device is connected.</span>
            </div>
        @endif

        <section class="history-card chart-card">
            <div class="section-heading">Sensor Trend</div>
            <div class="chart-wrap">
                <canvas id="sensor-history-chart" aria-label="Sensor history line chart"></canvas>
            </div>
        </section>

        <section class="history-card table-card">
            <div class="table-card-header">
                <div class="section-heading">Reading History</div>
            </div>
            <div class="table-responsive">
                <table class="table history-table">
                    <thead>
                        <tr>
                            <th class="ps-3">Date/Time</th>
                            <th>Parameter</th>
                            <th>Value</th>
                            <th>Unit</th>
                            <th class="pe-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($historyRows as $row)
                            <tr>
                                <td class="ps-3 text-nowrap">{{ $row['recorded_at']->format('d M Y, h:i A') }}</td>
                                <td class="fw-semibold">{{ $row['parameter'] }}</td>
                                <td>{{ is_numeric($row['display_value']) ? rtrim(rtrim(number_format((float) $row['display_value'], 2), '0'), '.') : $row['display_value'] }}</td>
                                <td>{{ $row['unit'] }}</td>
                                <td class="pe-3">
                                    <span class="status-pill {{ $row['status'] === 'Good' ? 'status-good' : 'status-warning' }}">
                                        {{ $row['status'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="empty-history">No readings available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="history-pager">
                @if($historyRows->onFirstPage())
                    <span class="history-page-btn disabled">Prev</span>
                @else
                    <a class="history-page-btn" href="{{ $historyRows->previousPageUrl() }}">Prev</a>
                @endif

                @if($historyRows->hasMorePages())
                    <a class="history-page-btn" href="{{ $historyRows->nextPageUrl() }}">Next</a>
                @else
                    <span class="history-page-btn disabled">Next</span>
                @endif
            </div>
        </section>
    @endif
</div>

@if($selectedTank)
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const canvas = document.getElementById('sensor-history-chart');
            if (!canvas || typeof Chart === 'undefined') return;

            const datasets = @json($chartDatasets).map((dataset) => ({
                ...dataset,
                borderWidth: 2.5,
                pointRadius: 3,
                pointHoverRadius: 5,
                tension: 0.3,
                spanGaps: true,
                fill: false,
            }));

            new Chart(canvas, {
                type: 'line',
                data: {
                    labels: @json($chartLabels),
                    datasets,
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { usePointStyle: true, boxWidth: 8, padding: 18 },
                        },
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 8 },
                        },
                        y: {
                            beginAtZero: false,
                            grid: { color: 'rgba(15, 87, 110, 0.08)' },
                        },
                    },
                },
            });
        });
    </script>
@endif
@endsection

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'My Tanks') - AquaWatch</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #D1E4E8;
            color: #0f1f26;
        }
        .page-bg {
            background: #D1E4E8;
            min-height: 100vh;
        }
        .card {
            border: 1px solid #D6E8ED;
            border-radius: 1rem;
            background: #fff;
        }
        .card-shadow {
            box-shadow: 0 4px 12px rgba(15, 87, 110, 0.08);
        }
        .muted { color: #6c757d; }
        .app-shell {
            display: flex;
            min-height: 100vh;
        }
        .side-navbar {
            width: 270px;
            flex: 0 0 270px;
            background: rgba(255, 255, 255, 0.96);
            border-right: 1px solid rgba(11, 95, 118, 0.12);
            box-shadow: 8px 0 24px rgba(31, 58, 95, 0.06);
            padding: 22px 16px;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }
        .app-main {
            flex: 1;
            min-width: 0;
        }
        .top-navbar {
            min-height: 68px;
            background: rgba(255, 255, 255, 0.96);
            border-bottom: 1px solid rgba(11, 95, 118, 0.12);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 14px 24px;
            position: sticky;
            top: 0;
            z-index: 20;
        }
        .top-user {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }
        .tank-switcher {
            min-width: 180px;
            max-width: 260px;
        }
        .top-icon-btn {
            width: 38px;
            height: 38px;
            border: 1px solid rgba(11, 95, 118, 0.25);
            border-radius: 8px;
            background: #fff;
            color: #0b5f76;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        .top-icon-btn:hover {
            background: rgba(11, 95, 118, 0.08);
            color: #0b5f76;
        }
        .top-icon {
            width: 19px;
            height: 19px;
        }
        .notification-dot {
            position: absolute;
            top: 7px;
            right: 7px;
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: #dc3545;
            border: 2px solid #fff;
        }
        .brand-link {
            color: #0f1f26;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 24px;
        }
        .brand-mark {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: #0f6c85;
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }
        .side-nav-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .nav-link-soft {
            color: #52656d;
            text-decoration: none;
            transition: background .2s ease, color .2s ease;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.94rem;
            padding: 11px 12px;
            border-radius: 8px;
            line-height: 1.2;
        }
        .nav-link-soft:hover {
            background: rgba(11, 95, 118, 0.08);
            color: #0b5f76;
        }
        .nav-link-active {
            background: rgba(11, 95, 118, 0.12);
            color: #0b5f76;
            font-weight: 700;
        }
        .nav-icon {
            width: 20px;
            height: 20px;
            color: currentColor;
            flex: 0 0 20px;
        }
        .btn-logout {
            border-color: rgba(11, 95, 118, 0.35);
            color: #0b5f76;
        }
        .btn-logout:hover {
            background: rgba(11, 95, 118, 0.08);
            color: #0b5f76;
        }
        .aquawatch-toast-container {
            position: fixed;
            top: 84px;
            right: 24px;
            z-index: 1080;
            width: min(360px, calc(100vw - 32px));
            pointer-events: none;
        }
        .aquawatch-toast {
            pointer-events: auto;
            border-radius: 14px;
            box-shadow: 0 10px 24px rgba(15, 87, 110, 0.16);
            overflow: hidden;
        }
        .aquawatch-toast .toast-body {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 16px;
            color: #0f1f26;
        }
        .toast-icon {
            width: 28px;
            height: 28px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 28px;
        }
        .toast-icon svg {
            width: 17px;
            height: 17px;
        }
        .aquawatch-toast-success {
            background: #E8F7EF;
            border: 1px solid #8FD3AA;
        }
        .aquawatch-toast-success .toast-icon {
            background: #CDEEDB;
            color: #187044;
        }
        .aquawatch-toast-error {
            background: #FCEAEA;
            border: 1px solid #E8A1A1;
        }
        .aquawatch-toast-error .toast-icon {
            background: #F7D0D0;
            color: #9F1D1D;
        }
        .aquawatch-toast-warning {
            background: #FFF7D8;
            border: 1px solid #F0D36A;
        }
        .aquawatch-toast-warning .toast-icon {
            background: #FCEAA6;
            color: #8A6500;
        }
        @media (max-width: 992px) {
            .app-shell {
                display: block;
            }
            .side-navbar {
                position: static;
                width: 100%;
                height: auto;
                padding: 14px;
                border-right: 0;
                border-bottom: 1px solid rgba(11, 95, 118, 0.12);
            }
            .brand-link {
                margin-bottom: 12px;
            }
            .top-navbar {
                position: static;
                padding: 14px;
            }
            .side-nav-group {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            .nav-link-soft {
                padding: 10px;
            }
        }
        @media (max-width: 576px) {
            .side-nav-group {
                grid-template-columns: 1fr;
            }
            .aquawatch-toast-container {
                top: 16px;
                right: 16px;
            }
        }
    </style>
</head>
<body>
<div class="page-bg">
@php
    $userTanks = auth()->check()
        ? \App\Models\Tank::where('user_id', auth()->id())->orderBy('name')->get()
        : collect();
    $currentTank = $selectedTank ?? null;
    if (!$currentTank instanceof \App\Models\Tank && auth()->check()) {
        $sessionTankId = session('selected_tank_id');
        $currentTank = $sessionTankId ? $userTanks->firstWhere('id', $sessionTankId) : null;
    }
    if (!$currentTank instanceof \App\Models\Tank) {
        $currentTank = $userTanks->first();
    }
    $userName = auth()->user()->name ?? 'User';
    $latestNotice = null;
    if ($currentTank instanceof \App\Models\Tank) {
        $latestAction = \App\Models\TankAction::where('tank_id', $currentTank->id)->latest('id')->first();
        $latestReading = \App\Models\TankReading::where('tank_id', $currentTank->id)->latest('recorded_at')->latest('id')->first();
        $latestNotice = $latestAction
            ? $latestAction->action . ' - ' . $latestAction->created_at->format('Y-m-d H:i')
            : ($latestReading ? $latestReading->parameter . ' updated - ' . $latestReading->recorded_at?->format('Y-m-d H:i') : 'No alerts or status yet');
    }
@endphp

<div class="app-shell">
<aside class="side-navbar">
    <a class="brand-link" href="{{ route('tanks.index') }}">
        <span class="brand-mark">{{ strtoupper(substr($userName, 0, 1)) }}</span>
        <span class="fw-bold fs-5">{{ $userName }}</span>
    </a>

    <nav class="side-nav-group" aria-label="Main navigation">
        <a class="nav-link-soft {{ request()->routeIs('dashboard', 'tanks.dashboard') ? 'nav-link-active' : '' }}" href="{{ route('dashboard') }}">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none">
                <path d="M4 13h7V4H4v9Zm9 7h7V4h-7v16ZM4 20h7v-5H4v5Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
            </svg>
            <span>Dashboard</span>
        </a>
        <a class="nav-link-soft {{ request()->routeIs('sensor-history.index') ? 'nav-link-active' : '' }}" href="{{ route('sensor-history.index') }}">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none">
                <path d="M4 19V5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                <path d="M4 19h17" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                <path d="m7 15 3-4 3 2 5-7" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>Sensor History</span>
        </a>
        <a class="nav-link-soft {{ request()->routeIs('species.index', 'species.update') ? 'nav-link-active' : '' }}" href="{{ route('species.index') }}">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none">
                <path d="M12 21c4-2.5 7-6 7-11V5l-7-3-7 3v5c0 5 3 8.5 7 11Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                <path d="M9 12h6M12 9v6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
            </svg>
            <span>Species Selection</span>
        </a>
        <a class="nav-link-soft {{ request()->routeIs('community.index', 'community.calculate', 'community.apply') ? 'nav-link-active' : '' }}" href="{{ route('community.index') }}">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none">
                <path d="M5 20V8m7 12V4m7 16v-9" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                <path d="M3 20h19" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
            </svg>
            <span>Community Calculation</span>
        </a>
        <a class="nav-link-soft {{ request()->routeIs('image-analysis.index') ? 'nav-link-active' : '' }}" href="{{ route('image-analysis.index') }}">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none">
                <path d="M4 7a2 2 0 0 1 2-2h3l1.5 2H18a2 2 0 0 1 2 2v10H4V7Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                <path d="m8 16 3-4 2 2 2-3 3 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>Image Analysis</span>
        </a>
        <a class="nav-link-soft {{ request()->routeIs('tanks.*') && !request()->routeIs('tanks.dashboard', 'tanks.thresholds.*') ? 'nav-link-active' : '' }}" href="{{ route('tanks.index') }}">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none">
                <path d="M3 12c2.2-2.5 5.1-4 9-4 3.9 0 6.8 1.5 9 4-2.2 2.5-5.1 4-9 4-3.9 0-6.8-1.5-9-4Z" stroke="currentColor" stroke-width="1.7"/>
                <path d="M6 12c0 2.5-1.5 4-3 4 .5-1.5.5-2.5 0-4 .5-1.5.5-2.5 0-4 1.5 0 3 1.5 3 4Z" stroke="currentColor" stroke-width="1.7"/>
                <path d="M15.5 10.5h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <span>Tanks</span>
        </a>
        <a class="nav-link-soft {{ request()->routeIs('telegram.index', 'telegram.link') ? 'nav-link-active' : '' }}" href="{{ route('telegram.index') }}">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none">
                <path d="m21 4-4.8 16-4.4-6.8L5 10.8 21 4Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                <path d="m11.8 13.2 4.6-4.7" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
            </svg>
            <span>Telegram Integration</span>
        </a>
        <a class="nav-link-soft {{ request()->routeIs('profile.edit') ? 'nav-link-active' : '' }}" href="{{ route('profile.edit') }}">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none">
                <path d="M20 20a8 8 0 1 0-16 0" stroke="currentColor" stroke-width="1.7"/>
                <path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="1.7"/>
            </svg>
            <span>Setting/Profile</span>
        </a>
    </nav>
</aside>

<main class="app-main">
<header class="top-navbar">
    <div class="fw-bold fs-5">AquaWatch</div>
    <div class="top-user">
        @if($userTanks->isNotEmpty())
            <form method="POST" action="{{ route('dashboard.tank.select') }}" class="mb-0">
                @csrf
                <select class="form-select form-select-sm tank-switcher" name="tank_id" aria-label="Select tank"
                        onchange="this.form.submit()">
                    @foreach($userTanks as $tankOption)
                        <option value="{{ $tankOption->id }}" {{ $currentTank && $currentTank->id === $tankOption->id ? 'selected' : '' }}>
                            {{ $tankOption->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        @endif
        <div class="dropdown">
            <button class="top-icon-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Latest alert or status">
                <svg class="top-icon" viewBox="0 0 24 24" fill="none">
                    <path d="M18 9a6 6 0 1 0-12 0c0 7-3 7-3 7h18s-3 0-3-7Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                    <path d="M10 20a2 2 0 0 0 4 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
                @if($latestNotice && $latestNotice !== 'No alerts or status yet')
                    <span class="notification-dot"></span>
                @endif
            </button>
            <div class="dropdown-menu dropdown-menu-end p-3" style="min-width: 280px;">
                <div class="fw-semibold small mb-1">Latest Alert / Status</div>
                <div class="small muted">{{ $latestNotice ?? 'No alerts or status yet' }}</div>
            </div>
        </div>
        <span class="small fw-semibold text-truncate">{{ $userName }}</span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="top-icon-btn" type="submit" title="Logout" aria-label="Logout">
                <svg class="top-icon" viewBox="0 0 24 24" fill="none">
                    <path d="M10 17l5-5-5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M15 12H3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    <path d="M12 3h6a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
            </button>
        </form>
    </div>
</header>
<div class="container-fluid px-3 px-md-4 py-4">
    @yield('content')
</div>
</main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@php
    $toastMessages = [];
    if (session('success')) {
        $toastMessages[] = ['type' => 'success', 'message' => session('success')];
    }
    if (session('error')) {
        $toastMessages[] = ['type' => 'error', 'message' => session('error')];
    }
    if (session('warning')) {
        $toastMessages[] = ['type' => 'warning', 'message' => session('warning')];
    }
    if ($errors->any()) {
        $toastMessages[] = [
            'type' => 'error',
            'message' => 'Please fix: ' . $errors->all()[0] . ($errors->count() > 1 ? ' +' . ($errors->count() - 1) . ' more' : ''),
        ];
    }
@endphp
@if($toastMessages)
    <div class="aquawatch-toast-container" aria-live="polite" aria-atomic="true">
        @foreach($toastMessages as $toast)
            <div class="toast aquawatch-toast aquawatch-toast-{{ $toast['type'] }} mb-2 aquawatch-session-toast"
                 role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3000">
                <div class="toast-body">
                    <span class="toast-icon">
                        @if($toast['type'] === 'success')
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="m6 12 4 4 8-9" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        @else
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M12 8v5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M12 17h.01" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/>
                                <path d="M10.3 4.7 2.8 18a2 2 0 0 0 1.7 3h15a2 2 0 0 0 1.7-3L13.7 4.7a2 2 0 0 0-3.4 0Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                            </svg>
                        @endif
                    </span>
                    <div class="small fw-semibold">{{ $toast['message'] }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        @endforeach
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.aquawatch-session-toast').forEach((toastEl) => {
                new bootstrap.Toast(toastEl, { delay: 3000 }).show();
            });
        });
    </script>
@endif
</div>
</body>
</html>

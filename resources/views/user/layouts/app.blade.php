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
            overflow-x: hidden;
        }
        .page-bg {
            background: #a8c0db;
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
            min-height: 100vh;
        }
        .app-main {
            width: 100%;
            min-width: 0;
        }
        .top-navbar {
            min-height: 64px;
            background: rgba(255, 255, 255, 0.97);
            border-bottom: 1px solid rgba(11, 95, 118, 0.12);
            box-shadow: 0 8px 22px rgba(31, 58, 95, 0.08);
            padding: 0.65rem 1.25rem;
            position: sticky;
            top: 0;
            z-index: 1040;
        }
        .navbar-inner {
            display: grid;
            grid-template-columns: minmax(240px, 1fr) auto minmax(180px, 1fr);
            align-items: center;
            gap: 1rem;
            width: 100%;
        }
        .brand-area {
            display: flex;
            align-items: center;
            gap: 1rem;
            min-width: 0;
        }
        .brand-link {
            color: #0f1f26;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.6rem;
            letter-spacing: normal;
            white-space: nowrap;
        }
        .brand-link:hover {
            color: #78a2d2;
        }
        .tank-area {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            min-width: 0;
            color: #0f1f26;
        }
        .tank-current {
            max-width: 170px;
            font-weight: 500;
            font-size: 0.95rem;
            letter-spacing: normal;
        }
        .tank-switcher-button {
            max-width: 260px;
            border: 1px solid rgba(120, 162, 210, 0.45);
            border-radius: 999px;
            background: rgba(120, 162, 210, 0.1);
            color: #0f1f26;
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.45rem 0.8rem;
            font-size: 0.95rem;
            font-weight: 500;
            letter-spacing: normal;
            cursor: pointer;
        }
        .tank-switcher-button:hover,
        .tank-switcher-button:focus,
        .tank-switcher-button.show {
            color: #78a2d2;
            border-color: rgba(120, 162, 210, 0.72);
            box-shadow: 0 0 0 4px rgba(120, 162, 210, 0.12);
        }
        .tank-dropdown-menu {
            min-width: 220px;
        }
        .tank-option-form {
            margin: 0;
        }
        .tank-option-button {
            border: 1px solid transparent;
            width: 100%;
            background: transparent;
            color: #0f1f26;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 500;
            letter-spacing: normal;
            text-align: left;
            padding: 0.65rem 0.8rem;
        }
        .tank-option-button:hover,
        .tank-option-button:focus {
            color: #78a2d2;
            background: rgba(120, 162, 210, 0.1);
        }
        .tank-option-button.active {
            color: #0f1f26;
            background: rgba(120, 162, 210, 0.16);
            border: 1px solid rgba(120, 162, 210, 0.5);
            box-shadow: 0 0 0 4px rgba(120, 162, 210, 0.12);
        }
        .nav-groups {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1.1rem;
        }
        .mobile-collapse,
        .mobile-collapse.collapse:not(.show) {
            display: contents;
        }
        .nav-group {
            position: relative;
        }
        .nav-group-button,
        .user-menu-button {
            border: 1px solid transparent;
            background: transparent;
            color: #0f1f26;
            font-size: 1rem;
            font-weight: 600;
            letter-spacing: normal;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.5rem 0.65rem;
            border-radius: 12px;
            white-space: nowrap;
        }
        .nav-group-button:hover,
        .nav-group-button:focus,
        .nav-group-button.show,
        .user-menu-button:hover,
        .user-menu-button:focus,
        .user-menu-button.show {
            color: #78a2d2;
        }
        .nav-group-button.active,
        .user-menu-button.active {
            color: #0f1f26;
            background: rgba(120, 162, 210, 0.16);
            border: 1px solid rgba(120, 162, 210, 0.5);
            box-shadow: 0 0 0 4px rgba(120, 162, 210, 0.12);
            font-weight: 700;
        }
        .user-menu-button span {
            min-width: 0;
            max-width: 180px;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .user-menu-button {
            font-size: 0.95rem;
            font-weight: 600;
        }
        .chevron {
            width: 12px;
            height: 12px;
            flex: 0 0 12px;
        }
        .nav-dropdown-menu {
            min-width: 250px;
            max-width: min(320px, calc(100vw - 24px));
            padding: 0.65rem;
            border: 1px solid rgba(11, 95, 118, 0.12);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.98);
            box-shadow: 0 12px 26px rgba(31, 58, 95, 0.18);
            z-index: 1100;
        }
        .nav-dropdown-menu .dropdown-item {
            color: #0f1f26;
            border: 1px solid transparent;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            letter-spacing: normal;
            padding: 0.65rem 0.8rem;
        }
        .nav-dropdown-menu .dropdown-item:hover,
        .nav-dropdown-menu .dropdown-item:focus {
            background: rgba(120, 162, 210, 0.1);
            color: #78a2d2;
        }
        .nav-dropdown-menu .dropdown-item.active {
            background: rgba(120, 162, 210, 0.16);
            color: #0f1f26;
            border: 1px solid rgba(120, 162, 210, 0.5);
            box-shadow: 0 0 0 4px rgba(120, 162, 210, 0.12);
            font-weight: 700;
        }
        .right-area {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            min-width: 0;
        }
        .user-dropdown-menu {
            min-width: 150px;
            padding: 0.45rem;
            border-radius: 10px;
            border: 1px solid rgba(11, 95, 118, 0.14);
            box-shadow: 0 12px 26px rgba(31, 58, 95, 0.16);
            z-index: 1100;
        }
        .logout-item {
            border: 1px solid transparent;
            width: 100%;
            background: transparent;
            color: #dc3545;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 600;
            letter-spacing: normal;
            text-align: left;
            padding: 0.55rem 0.7rem;
        }
        .logout-item:hover,
        .logout-item:focus,
        .logout-item:active {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            border-color: rgba(220, 53, 69, 0.35);
            box-shadow: 0 0 0 4px rgba(220, 53, 69, 0.1);
        }
        .hamburger-btn {
            display: none;
            width: 40px;
            height: 40px;
            border: 1px solid rgba(11, 95, 118, 0.18);
            border-radius: 8px;
            background: transparent;
            color: #0f1f26;
            align-items: center;
            justify-content: center;
            padding: 0;
        }
        .hamburger-btn svg {
            width: 22px;
            height: 22px;
        }
        .content-wrap {
            width: 100%;
            max-width: none;
            padding: 1.5rem;
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
        @media (max-width: 991.98px) {
            .top-navbar {
                padding: 0.65rem 0.85rem;
                position: sticky;
            }
            .navbar-inner {
                grid-template-columns: auto 1fr auto;
                grid-template-areas:
                    "brand brand toggle"
                    "menu menu menu";
                gap: 0.65rem;
            }
            .brand-area {
                grid-area: brand;
                flex-wrap: wrap;
                gap: 0.65rem 1rem;
            }
            .hamburger-btn {
                grid-area: toggle;
                display: inline-flex;
                justify-self: end;
            }
            .mobile-collapse {
                grid-area: menu;
                display: none;
                width: 100%;
                border-top: 1px solid rgba(11, 95, 118, 0.12);
                padding-top: 0.55rem;
            }
            .mobile-collapse.show {
                display: block;
            }
            .nav-groups,
            .right-area {
                display: block;
                width: 100%;
            }
            .nav-group {
                width: 100%;
            }
            .nav-group-button,
            .user-menu-button {
                width: 100%;
                justify-content: space-between;
                padding: 0.75rem 0.2rem;
            }
            .nav-dropdown-menu,
            .user-dropdown-menu {
                position: static !important;
                transform: none !important;
                width: 100%;
                max-width: none;
                margin: 0 0 0.35rem;
                background: rgba(255, 255, 255, 0.98);
                border-color: rgba(11, 95, 118, 0.12);
                box-shadow: 0 8px 18px rgba(31, 58, 95, 0.08);
            }
            .content-wrap {
                padding: 1rem;
            }
        }
        @media (max-width: 575.98px) {
            .tank-current {
                max-width: calc(100vw - 130px);
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
    $currentTankName = $currentTank instanceof \App\Models\Tank ? $currentTank->name : 'No Tank';
    $monitoringActive = request()->routeIs('dashboard', 'tanks.dashboard', 'sensor-history.index', 'tanks.*', 'tank-requests.*');
    $analysisActive = request()->routeIs('species.*', 'community.*', 'image-analysis.*');
    $setupActive = request()->routeIs('telegram.*', 'profile.*');
@endphp

<div class="app-shell">
    <header class="top-navbar">
        <div class="navbar-inner">
            <div class="brand-area">
                <a class="brand-link" href="{{ route('dashboard') }}">AquaWatch</a>
                <div class="tank-area">
                    @if($userTanks->isNotEmpty())
                        <div class="dropdown">
                            <button class="tank-switcher-button" type="button" data-bs-toggle="dropdown"
                                    data-bs-auto-close="outside" data-bs-boundary="viewport" aria-expanded="false">
                                <span class="tank-current text-truncate">{{ $currentTankName }}</span>
                                <svg class="chevron" viewBox="0 0 24 24" fill="none">
                                    <path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                            <div class="dropdown-menu nav-dropdown-menu tank-dropdown-menu">
                                @foreach($userTanks as $tankOption)
                                    <form method="POST" action="{{ route('dashboard.tank.select') }}" class="tank-option-form">
                                        @csrf
                                        <input type="hidden" name="tank_id" value="{{ $tankOption->id }}">
                                        <button class="tank-option-button {{ $currentTank && $currentTank->id === $tankOption->id ? 'active' : '' }}" type="submit">
                                            {{ $tankOption->name }}
                                        </button>
                                    </form>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <span class="tank-current text-truncate">{{ $currentTankName }}</span>
                    @endif
                </div>
            </div>

            <button class="hamburger-btn" type="button" data-bs-toggle="collapse" data-bs-target="#userNavbarMenu"
                    aria-controls="userNavbarMenu" aria-expanded="false" aria-label="Toggle navigation">
                <svg viewBox="0 0 24 24" fill="none">
                    <path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>

            <div class="mobile-collapse collapse" id="userNavbarMenu">
                <nav class="nav-groups" aria-label="Main navigation">
                    <div class="dropdown nav-group">
                        <button class="nav-group-button {{ $monitoringActive ? 'active' : '' }}" type="button"
                                data-bs-toggle="dropdown" data-bs-auto-close="outside" data-bs-boundary="viewport"
                                aria-expanded="false">
                            <span>Monitoring</span>
                            <svg class="chevron" viewBox="0 0 24 24" fill="none">
                                <path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <div class="dropdown-menu nav-dropdown-menu">
                            <a class="dropdown-item {{ request()->routeIs('dashboard', 'tanks.dashboard') ? 'active' : '' }}"
                               href="{{ route('dashboard') }}">Dashboard</a>
                            <a class="dropdown-item {{ request()->routeIs('sensor-history.index') ? 'active' : '' }}"
                               href="{{ route('sensor-history.index') }}">Sensor History</a>
                            <a class="dropdown-item {{ request()->routeIs('tanks.*', 'tank-requests.*') && !request()->routeIs('tanks.dashboard') ? 'active' : '' }}"
                               href="{{ route('tanks.index') }}">Tanks</a>
                        </div>
                    </div>

                    <div class="dropdown nav-group">
                        <button class="nav-group-button {{ $analysisActive ? 'active' : '' }}" type="button"
                                data-bs-toggle="dropdown" data-bs-auto-close="outside" data-bs-boundary="viewport"
                                aria-expanded="false">
                            <span>Analysis</span>
                            <svg class="chevron" viewBox="0 0 24 24" fill="none">
                                <path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <div class="dropdown-menu nav-dropdown-menu">
                            <a class="dropdown-item {{ request()->routeIs('species.*') ? 'active' : '' }}"
                               href="{{ route('species.index') }}">Species Selection</a>
                            <a class="dropdown-item {{ request()->routeIs('community.*') ? 'active' : '' }}"
                               href="{{ route('community.index') }}">Community Calculation</a>
                            <a class="dropdown-item {{ request()->routeIs('image-analysis.*') ? 'active' : '' }}"
                               href="{{ route('image-analysis.index') }}">Image Analysis</a>
                        </div>
                    </div>

                    <div class="dropdown nav-group">
                        <button class="nav-group-button {{ $setupActive ? 'active' : '' }}" type="button"
                                data-bs-toggle="dropdown" data-bs-auto-close="outside" data-bs-boundary="viewport"
                                aria-expanded="false">
                            <span>Setup</span>
                            <svg class="chevron" viewBox="0 0 24 24" fill="none">
                                <path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <div class="dropdown-menu nav-dropdown-menu">
                            <a class="dropdown-item {{ request()->routeIs('telegram.*') ? 'active' : '' }}"
                               href="{{ route('telegram.index') }}">Telegram Integration</a>
                            <a class="dropdown-item {{ request()->routeIs('profile.*') ? 'active' : '' }}"
                               href="{{ route('profile.edit') }}">Setting/Profile</a>
                        </div>
                    </div>
                </nav>

                <div class="right-area">
                    <div class="dropdown nav-group">
                        <button class="user-menu-button" type="button" data-bs-toggle="dropdown"
                                data-bs-auto-close="outside" data-bs-boundary="viewport" aria-expanded="false">
                            <span>USER: {{ $userName }}</span>
                            <svg class="chevron" viewBox="0 0 24 24" fill="none">
                                <path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end user-dropdown-menu">
                            <form method="POST" action="{{ route('logout') }}" class="mb-0">
                                @csrf
                                <button class="logout-item" type="submit">Sign Out</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="app-main">
        <div class="content-wrap">
            @yield('content')
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const navbarMenu = document.getElementById('userNavbarMenu');

        document.querySelectorAll('.top-navbar .dropdown').forEach((dropdown) => {
            dropdown.addEventListener('show.bs.dropdown', () => {
                document.querySelectorAll('.top-navbar .dropdown-toggle.show, .top-navbar [data-bs-toggle="dropdown"].show').forEach((toggle) => {
                    const openDropdown = bootstrap.Dropdown.getInstance(toggle);
                    if (openDropdown && !dropdown.contains(toggle)) {
                        openDropdown.hide();
                    }
                });
            });
        });

        document.querySelectorAll('.nav-dropdown-menu .dropdown-item, .tank-option-button, .user-dropdown-menu .logout-item').forEach((item) => {
            item.addEventListener('click', () => {
                if (window.innerWidth < 992 && navbarMenu) {
                    bootstrap.Collapse.getOrCreateInstance(navbarMenu, { toggle: false }).hide();
                }
            });
        });
    });
</script>
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

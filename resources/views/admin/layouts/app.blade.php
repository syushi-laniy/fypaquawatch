<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Admin') - AquaWatch</title>

    {{-- Bootstrap (CDN) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Simple admin styling --}}
    <style>
        body { background: #f6f7fb; }
        .sidebar {
            width: 260px; min-height: 100vh;
            background: #0b1220;
        }
        .sidebar .brand { font-weight: 700; letter-spacing: .3px; }
        .sidebar a {
            color: rgba(255,255,255,.85);
            text-decoration: none;
        }
        .sidebar a:hover { color: #fff; }
        .sidebar .nav-link.active {
            background: rgba(255,255,255,.10);
            border-radius: .6rem;
        }
        .content { min-height: 100vh; }
        .card { border: 0; border-radius: 1rem; }
        .card-shadow { box-shadow: 0 10px 25px rgba(0,0,0,.06); }
        .muted { color: #6c757d; }
        .admin-icon-button {
            width: 38px;
            height: 38px;
            border: 1px solid #ced4da;
            border-radius: .55rem;
            background: #fff;
            color: #212529;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .admin-icon-button:hover {
            background: #f1f3f5;
            color: #0b5f76;
        }
        .admin-icon-button svg {
            width: 19px;
            height: 19px;
        }
    </style>
</head>

<body>
<div class="d-flex">
    {{-- Sidebar --}}
    <aside class="sidebar p-3">
        <div class="d-flex align-items-center gap-2 mb-4">
            <div class="rounded-circle bg-light" style="width:34px;height:34px;"></div>
            <div class="text-white">
                <div class="brand">AquaWatch</div>
                <div class="small text-white-50">Admin Panel</div>
            </div>
        </div>

        <nav class="nav flex-column gap-1">
            <a class="nav-link px-3 py-2 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
               href="{{ route('admin.dashboard') }}">Dashboard</a>

            <div class="text-uppercase small text-white-50 px-3 mt-3 mb-1">Management</div>

            <a class="nav-link px-3 py-2 {{ request()->routeIs('admin.tanks.*') ? 'active' : '' }}"
               href="{{ route('admin.tanks.index') }}">Tanks</a>

            <a class="nav-link px-3 py-2 {{ request()->routeIs('admin.tank-requests.*') ? 'active' : '' }}"
               href="{{ route('admin.tank-requests.index') }}">Tank Requests</a>

            <a class="nav-link px-3 py-2 {{ request()->routeIs('parameters.*') ? 'active' : '' }}"
               href="{{ route('parameters.index') }}">Parameters</a>

            <a class="nav-link px-3 py-2 {{ request()->routeIs('thresholds.*') ? 'active' : '' }}"
               href="{{ route('thresholds.index') }}">Thresholds</a>

            <a class="nav-link px-3 py-2 {{ request()->routeIs('admin.species.*') ? 'active' : '' }}"
               href="{{ route('admin.species.index') }}">Fish Species</a>

            <a class="nav-link px-3 py-2 {{ request()->routeIs('automation-rules.*') ? 'active' : '' }}"
               href="{{ route('automation-rules.index') }}">Automation Rules</a>

            <a class="nav-link px-3 py-2 {{ request()->routeIs('users.*') ? 'active' : '' }}"
               href="{{ route('users.index') }}">Users</a>

            <a class="nav-link px-3 py-2 {{ request()->routeIs('admin.fish-analyses.*') ? 'active' : '' }}"
               href="{{ route('admin.fish-analyses.index') }}">Fish Analysis</a>
        </nav>

        <hr class="border-light opacity-25 my-4" />

        <div class="text-white-50 small">
            Logged in as: <span class="text-white">{{ auth()->user()->name ?? 'Admin' }}</span>
        </div>
    </aside>

    {{-- Main --}}
    <main class="content flex-grow-1">
        {{-- Topbar --}}
        <div class="bg-white border-bottom">
            <div class="container-fluid px-4 py-3 d-flex justify-content-between align-items-center">
                <div>
                    <div class="h5 mb-0">@yield('page_title', 'Dashboard')</div>
                    <div class="small muted">@yield('page_subtitle', 'Manage system settings and monitoring rules')</div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="admin-icon-button" type="submit" title="Logout" aria-label="Logout">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M10 17l5-5-5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M15 12H3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                <path d="M12 3h6a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Page content --}}
        <div class="container-fluid px-4 py-4">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <div class="fw-semibold mb-1">Please fix the following:</div>
                    <ul class="mb-0">
                        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

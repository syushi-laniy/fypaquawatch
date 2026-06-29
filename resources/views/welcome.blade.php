<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $pageTitle ?? 'Welcome' }} | AquaWatch</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            overflow: hidden;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: #fff;
            background: #062f3c;
        }

        .video-bg {
            position: fixed;
            inset: 0;
            z-index: -2;
            background: #062f3c;
        }

        .video-bg video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .page-overlay {
            position: fixed;
            inset: 0;
            z-index: -1;
            background:
                radial-gradient(circle at 50% 35%, rgba(18, 150, 170, .16), transparent 40%),
                linear-gradient(135deg, rgba(3, 31, 40, .74), rgba(4, 58, 72, .52));
        }

        .login-page {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
        }

        .glass-panel {
            width: min(400px, 92vw);
            max-height: calc(100vh - 48px);
            padding: 24px 34px;
            border: 1px solid rgba(255, 255, 255, .28);
            border-radius: 28px;
            background: rgba(255, 255, 255, .16);
            box-shadow: 0 24px 60px rgba(0, 24, 32, .34);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
        }

        .brand-mark {
            width: 42px;
            height: 42px;
            margin: 0 auto 10px;
            border-radius: 14px;
            background: rgba(255, 255, 255, .2);
            display: grid;
            place-items: center;
            color: #dffcff;
        }

        .brand-mark svg {
            width: 22px;
            height: 22px;
        }

        .brand-name {
            text-align: center;
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 18px;
        }

        .auth-title {
            text-align: center;
            font-size: 1.72rem;
            font-weight: 700;
            line-height: 1.1;
            margin-bottom: 18px;
        }

        .auth-slider {
            overflow: hidden;
        }

        .auth-track {
            width: 200%;
            display: flex;
            transition: transform 300ms ease;
        }

        .auth-pane {
            width: 50%;
            flex: 0 0 50%;
            padding: 0 1px;
        }

        .glass-panel.is-register .auth-track {
            transform: translateX(-50%);
        }

        .form-label {
            color: rgba(255, 255, 255, .9);
            font-size: .84rem;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .form-control {
            height: 40px;
            border: 1px solid rgba(255, 255, 255, .32);
            border-radius: 12px;
            background: rgba(255, 255, 255, .88);
            color: #10242b;
            box-shadow: none;
        }

        .form-control:focus {
            border-color: #7ce5ef;
            box-shadow: 0 0 0 .22rem rgba(124, 229, 239, .18);
        }

        .btn-login {
            height: 42px;
            border: 0;
            border-radius: 12px;
            background: linear-gradient(135deg, #0b7891, #12a0b8);
            color: #fff;
            font-weight: 700;
            box-shadow: 0 12px 22px rgba(0, 36, 46, .24);
        }

        .btn-login:hover,
        .btn-login:focus {
            background: linear-gradient(135deg, #09667c, #0d899f);
            color: #fff;
        }

        .auth-switch-text {
            text-align: center;
            color: rgba(255, 255, 255, .82);
            font-size: .9rem;
        }

        .auth-switch-text button {
            padding: 0;
            border: 0;
            background: transparent;
            color: #9ff5ff;
            font-weight: 700;
            text-decoration: none;
        }

        .auth-switch-text button:hover {
            color: #fff;
            text-decoration: underline;
        }

        .auth-alert {
            border-radius: 12px;
            font-size: .9rem;
            padding: 9px 12px;
            margin-bottom: 12px;
        }

        .register-pane .auth-title {
            margin-bottom: 14px;
        }

        .register-pane .form-control {
            height: 38px;
        }

        .register-pane .mb-2 {
            margin-bottom: .55rem !important;
        }

        .register-pane .mb-3 {
            margin-bottom: .85rem !important;
        }

        @media (max-width: 480px) {
            .login-page {
                padding: 16px;
            }

            .glass-panel {
                padding: 22px;
            }
        }
    </style>
</head>
<body>
<div class="video-bg" aria-hidden="true">
    <video autoplay muted loop playsinline preload="metadata">
        <source src="{{ asset('videos/welcome-bg.mp4') }}" type="video/mp4">
    </video>
</div>
<div class="page-overlay" aria-hidden="true"></div>

<main class="login-page">
    @php($initialAuthMode = $initialAuthMode ?? (old('name') ? 'register' : 'login'))
    <section class="glass-panel {{ $initialAuthMode === 'register' ? 'is-register' : '' }}" id="auth-panel" aria-label="AquaWatch authentication">
        <div class="brand-mark">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M12 3c3.6 3.2 6 6.3 6 9.2A6 6 0 1 1 6 12.2C6 9.3 8.4 6.2 12 3Z" stroke="currentColor" stroke-width="1.8"/>
                <path d="M9 14.5c.6 1.2 1.7 2 3 2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="brand-name">AquaWatch</div>

        @if(session('success'))
            <div class="alert alert-success auth-alert">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger auth-alert">{{ $errors->first() }}</div>
        @endif

        <div class="auth-slider">
            <div class="auth-track">
                <div class="auth-pane" data-auth-pane="login">
                    <h1 class="auth-title">Welcome Back</h1>

                    <form method="POST" action="{{ route('login.submit') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label" for="login-email">Email</label>
                            <input class="form-control" id="login-email" type="email" name="email" value="{{ old('email') }}" required autofocus>
                        </div>

                        <div class="mb-4">
                            <label class="form-label" for="login-password">Password</label>
                            <input class="form-control" id="login-password" type="password" name="password" required>
                        </div>

                        <button class="btn btn-login w-100" type="submit">Login</button>
                    </form>

                    <div class="auth-switch-text mt-3">
                        Don't Have an Account Yet?
                        <button type="button" data-auth-switch="register">Register Here</button>
                    </div>
                </div>

                <div class="auth-pane register-pane" data-auth-pane="register">
                    <h1 class="auth-title">Create Account</h1>

                    <form method="POST" action="{{ route('register.submit') }}">
                        @csrf

                        <div class="mb-2">
                            <label class="form-label" for="register-name">Name</label>
                            <input class="form-control" id="register-name" type="text" name="name" value="{{ old('name') }}" required>
                        </div>

                        <div class="mb-2">
                            <label class="form-label" for="register-email">Email</label>
                            <input class="form-control" id="register-email" type="email" name="email" value="{{ old('email') }}" required>
                        </div>

                        <div class="mb-2">
                            <label class="form-label" for="register-password">Password</label>
                            <input class="form-control" id="register-password" type="password" name="password" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="register-password-confirmation">Confirm Password</label>
                            <input class="form-control" id="register-password-confirmation" type="password" name="password_confirmation" required>
                        </div>

                        <button class="btn btn-login w-100" type="submit">Register</button>
                    </form>

                    <div class="auth-switch-text mt-3">
                        Already have an account?
                        <button type="button" data-auth-switch="login">Login now</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const panel = document.getElementById('auth-panel');
        const switches = document.querySelectorAll('[data-auth-switch]');

        function setMode(mode) {
            const isRegister = mode === 'register';
            panel.classList.toggle('is-register', isRegister);

            panel.querySelectorAll('[data-auth-pane]').forEach((pane) => {
                pane.setAttribute('aria-hidden', pane.dataset.authPane === mode ? 'false' : 'true');
            });

            const focusTarget = document.getElementById(isRegister ? 'register-name' : 'login-email');
            window.setTimeout(() => focusTarget?.focus(), 300);
        }

        switches.forEach((button) => {
            button.addEventListener('click', () => setMode(button.dataset.authSwitch));
        });

        setMode(panel.classList.contains('is-register') ? 'register' : 'login');
    });
</script>
</body>
</html>

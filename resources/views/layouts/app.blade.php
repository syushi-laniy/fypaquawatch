<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Aquawatch')</title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
    <div class="page">
        <header class="site-header">
            <div class="brand">
                <div class="brand__logo">🐠</div>
                <div>
                    <p class="brand__name">Aquawatch</p>
                    <p class="brand__tag">Aquarium Water Quality Monitoring</p>
                </div>
            </div>
            <nav class="nav">
                <a href="/" class="nav__link">Home</a>
                <a href="/login" class="nav__link">Login</a>
                <a href="/register" class="nav__link nav__link--primary">Register</a>
            </nav>
        </header>

        <main class="content">
            @yield('content')
        </main>

        <footer class="site-footer">
            <p>Telegram Integration - Laravel 10</p>
        </footer>
    </div>
</body>
</html>

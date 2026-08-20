<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIGAP - Sistem Informasi Gangguan & Antrean Pelayanan')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body id="sigap-guest-body">
    <header id="sigap-guest-header" class="sigap-navbar">
        <a href="{{ route('guest.landing') }}" class="sigap-navbar__brand">SIGAP</a>
        <nav class="sigap-navbar__nav">
            <a href="{{ route('guest.ticket.create') }}" class="sigap-navbar__link">Lapor</a>
            <a href="{{ route('guest.track.form') }}" class="sigap-navbar__link">Lacak Tiket</a>
            <a href="{{ route('login') }}" class="sigap-navbar__link sigap-navbar__link--cta">Login</a>
        </nav>
    </header>

    <main id="sigap-guest-main">
        @if (session('success'))
            <div class="sigap-alert sigap-alert--success">{{ session('success') }}</div>
        @endif
        @yield('content')
    </main>

    <footer id="sigap-guest-footer" class="sigap-footer">
        <p>&copy; {{ date('Y') }} SIGAP - Sistem Helpdesk Terpadu.</p>
    </footer>
</body>
</html>
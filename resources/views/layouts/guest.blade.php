<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIGAP - Sistem Informasi Gangguan & Antrean Pelayanan')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    {{-- Muat font Inter secara non-blocking agar tidak menghambat First Paint --}}
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    </noscript>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body id="sigap-guest-body" class="bg-white" x-data="{ menuOpen: false }">
    <header id="sigap-guest-header" class="sigap-navbar">
        <a href="{{ route('guest.landing') }}" class="sigap-navbar__brand">
            <span class="sigap-navbar__brand-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <path d="M13 2 4 14h6l-1 8 9-12h-6l1-8Z" fill="currentColor"/>
                </svg>
            </span>
            SIGAP
        </a>
        <nav class="sigap-navbar__nav">
            <a href="{{ route('guest.landing') }}" class="sigap-navbar__link">Beranda</a>
            <a href="{{ route('guest.ticket.create') }}" class="sigap-navbar__link">Lapor</a>
            <a href="{{ route('guest.track.form') }}" class="sigap-navbar__link">Lacak Tiket</a>
            <a href="{{ route('login') }}" class="sigap-navbar__link sigap-navbar__link--cta">Masuk</a>
        </nav>
    </header>

    <main id="sigap-guest-main">
        @if (session('success'))
            <div class="sigap-alert sigap-alert--success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="sigap-alert sigap-alert--error">{{ session('error') }}</div>
        @endif
        @yield('content')
    </main>

    <footer id="sigap-guest-footer" class="sigap-footer">
        <p>&copy; {{ date('Y') }} SIGAP - Sistem Helpdesk Terpadu. Seluruh hak cipta dilindungi.</p>
    </footer>
</body>
</html>
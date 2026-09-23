<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIGAP - Sistem Helpdesk Terpadu')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&display=swap" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    </noscript>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body id="sigap-guest-body" class="bg-white" x-data="{ menuOpen: false }">
    <header id="sigap-guest-header" class="sigap-navbar">
        <div class="sigap-navbar__container">
            <a href="{{ route('guest.landing') }}" class="sigap-navbar__brand">
                <span class="sigap-navbar__brand-icon" aria-hidden="true">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                        <path d="M13 2 4 14h6l-1 8 9-12h-6l1-8Z" fill="currentColor"/>
                    </svg>
                </span>
                SIGAP
            </a>
            <nav class="sigap-navbar__nav" aria-label="Navigasi utama">
                <a href="{{ route('guest.landing') }}" class="sigap-navbar__link">Beranda</a>
                <a href="{{ route('guest.ticket.create') }}" class="sigap-navbar__link">Lapor</a>
                <a href="{{ route('guest.track.form') }}" class="sigap-navbar__link">Lacak Tiket</a>
                <a href="{{ route('login') }}" class="sigap-btn sigap-btn--primary">
                    Masuk
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <line x1="5" y1="12" x2="19" y2="12"/>
                        <polyline points="12 5 19 12 12 19"/>
                    </svg>
                </a>
            </nav>
            <button type="button" class="sigap-icon-btn md:hidden" @click="menuOpen = !menuOpen" title="Buka menu" aria-label="Buka menu" aria-expanded="menuOpen ? 'true' : 'false'">
                <svg x-show="!menuOpen" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <line x1="3" y1="12" x2="21" y2="12"/>
                    <line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
                <svg x-show="menuOpen" x-cloak width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
            <div class="sigap-navbar__mobile" x-show="menuOpen" x-cloak @click.away="menuOpen = false">
                <a href="{{ route('guest.landing') }}" class="sigap-navbar__link" @click="menuOpen = false">Beranda</a>
                <a href="{{ route('guest.ticket.create') }}" class="sigap-navbar__link" @click="menuOpen = false">Lapor</a>
                <a href="{{ route('guest.track.form') }}" class="sigap-navbar__link" @click="menuOpen = false">Lacak Tiket</a>
                <a href="{{ route('login') }}" class="sigap-btn sigap-btn--primary justify-center" @click="menuOpen = false">Masuk</a>
            </div>
        </div>
    </header>

    <main id="sigap-guest-main">
        @if (session('success'))
            <div class="sigap-alert sigap-alert--success mt-6">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="sigap-alert sigap-alert--error mt-6">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="15" y1="9" x2="9" y2="15"/>
                    <line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif
        @yield('content')
    </main>

    <footer id="sigap-guest-footer" class="sigap-footer">
        <div class="sigap-footer__inner">
            <div class="sigap-footer__grid">
                <div>
                    <a href="{{ route('guest.landing') }}" class="sigap-footer__brand">
                        <span class="sigap-sidebar__brand-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M13 2 4 14h6l-1 8 9-12h-6l1-8Z" fill="currentColor"/>
                            </svg>
                        </span>
                        SIGAP
                    </a>
                    <p class="sigap-footer__desc">
                        Sistem Helpdesk Terpadu untuk mengelola, melacak, dan menyelesaikan
                        setiap laporan gangguan secara efisien dan transparan.
                    </p>
                </div>
                <div>
                    <h4 class="sigap-footer__heading">Layanan</h4>
                    <a href="{{ route('guest.ticket.create') }}" class="sigap-footer__link">Lapor Tiket</a>
                    <a href="{{ route('guest.track.form') }}" class="sigap-footer__link">Lacak Tiket</a>
                </div>
                <div>
                    <h4 class="sigap-footer__heading">Akses</h4>
                    <a href="{{ route('login') }}" class="sigap-footer__link">Masuk Karyawan</a>
                    <a href="{{ route('register') }}" class="sigap-footer__link">Daftar Akun</a>
                </div>
                <div>
                    <h4 class="sigap-footer__heading">Bantuan</h4>
                    <a href="{{ route('guest.track.form') }}" class="sigap-footer__link">Pertanyaan Umum</a>
                    <a href="{{ route('guest.landing') }}" class="sigap-footer__link">Tentang SIGAP</a>
                </div>
            </div>
            <div class="sigap-footer__bottom">
                <span>&copy; {{ date('Y') }} SIGAP - Sistem Helpdesk Terpadu.</span>
                <span>Seluruh hak cipta dilindungi.</span>
            </div>
        </div>
    </footer>
</body>
</html>
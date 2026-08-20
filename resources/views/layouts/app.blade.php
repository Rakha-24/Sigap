<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIGAP')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body id="sigap-app-body" class="sigap-app-shell" x-data="{ sidebarOpen: false }">

    {{-- Overlay mobile --}}
    <div class="sigap-sidebar__overlay" :class="sidebarOpen ? 'sigap-sidebar__overlay--open' : ''" @click="sidebarOpen = false"></div>

    <aside id="sigap-app-sidebar" class="sigap-sidebar" :class="sidebarOpen ? 'sigap-sidebar--open' : ''">
        {{-- Brand --}}
        <div>
            <div class="sigap-sidebar__brand-wrapper">
                <div class="sigap-sidebar__brand-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <path d="M13 2 4 14h6l-1 8 9-12h-6l1-8Z" fill="currentColor"/>
                    </svg>
                </div>
                <span class="sigap-sidebar__brand">SIGAP</span>
                <button type="button" class="sigap-sidebar__close" @click="sidebarOpen = false" title="Tutup menu">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>

            {{-- Navigation --}}
            <nav class="sigap-sidebar__nav">
                <a href="{{ route('dashboard') }}"
                   class="sigap-sidebar__link {{ request()->routeIs('dashboard') ? 'sigap-sidebar__link--active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="9" rx="1.5"/>
                        <rect x="14" y="3" width="7" height="5" rx="1.5"/>
                        <rect x="14" y="12" width="7" height="9" rx="1.5"/>
                        <rect x="3" y="16" width="7" height="5" rx="1.5"/>
                    </svg>
                    Dashboard
                </a>
                <a href="{{ route('tickets.index') }}"
                   class="sigap-sidebar__link {{ request()->routeIs('tickets.*') ? 'sigap-sidebar__link--active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/>
                        <path d="M14 2v4a1 1 0 0 0 1 1h3"/>
                        <line x1="8" y1="13" x2="16" y2="13"/>
                        <line x1="8" y1="17" x2="13" y2="17"/>
                    </svg>
                    Tiket Saya
                </a>
                @role('agent')
                    <a href="{{ route('agent.queue') }}"
                       class="sigap-sidebar__link {{ request()->routeIs('agent.queue') ? 'sigap-sidebar__link--active' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="4" rx="1"/>
                            <rect x="3" y="10" width="18" height="4" rx="1"/>
                            <rect x="3" y="16" width="18" height="4" rx="1"/>
                        </svg>
                        Antrean Tiket
                    </a>
                @endrole
                @role('admin')
                    <div class="sigap-sidebar__separator"></div>
                    <span class="sigap-sidebar__section-label">Admin</span>
                    <a href="{{ route('admin.tickets.index') }}"
                       class="sigap-sidebar__link {{ request()->routeIs('admin.tickets.*') ? 'sigap-sidebar__link--active' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <path d="M9 13h6"/>
                            <path d="M9 17h6"/>
                        </svg>
                        Tiket
                    </a>
                    <a href="{{ route('admin.analytics') }}"
                       class="sigap-sidebar__link {{ request()->routeIs('admin.analytics*') ? 'sigap-sidebar__link--active' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="20" x2="18" y2="10"/>
                            <line x1="12" y1="20" x2="12" y2="4"/>
                            <line x1="6" y1="20" x2="6" y2="14"/>
                        </svg>
                        Analitik
                    </a>
                    <a href="{{ route('admin.master-data.departemen.index') }}"
                       class="sigap-sidebar__link {{ request()->routeIs('admin.master-data.*') ? 'sigap-sidebar__link--active' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="3"/>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1Z"/>
                        </svg>
                        Master Data
                    </a>
                    <a href="{{ route('admin.users.index') }}"
                       class="sigap-sidebar__link {{ request()->routeIs('admin.users.*') ? 'sigap-sidebar__link--active' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                        Manajemen Pengguna
                    </a>
                @endrole
            </nav>
        </div>

        {{-- Bottom: user info + logout --}}
        <div class="sigap-sidebar__bottom">
            <div class="sigap-sidebar__user-info">
                <div class="sigap-sidebar__avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="sigap-sidebar__user-detail">
                    <span class="sigap-sidebar__user-name">{{ auth()->user()->name }}</span>
                    <span class="sigap-sidebar__user-role">{{ ucfirst(auth()->user()->role ?? 'User') }}</span>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sigap-sidebar__logout" title="Keluar">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <div id="sigap-app-content" class="sigap-app-content">
        {{-- Mobile bar --}}
        <div class="sigap-mobile-bar">
            <button type="button" class="sigap-icon-btn" @click="sidebarOpen = true" title="Buka menu">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <line x1="3" y1="12" x2="21" y2="12"/>
                    <line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
            </button>
            <a href="{{ route('dashboard') }}" class="sigap-mobile-bar__brand">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="text-sigap-600">
                    <path d="M13 2 4 14h6l-1 8 9-12h-6l1-8Z" fill="currentColor"/>
                </svg>
                SIGAP
            </a>
        </div>

        <header id="sigap-app-topbar" class="sigap-topbar">
            <div class="sigap-topbar__left">
                <div class="sigap-topbar__search-wrapper">
                    <svg class="sigap-topbar__search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input type="search" class="sigap-topbar__search hidden sm:block" placeholder="Cari tiket...">
                </div>
            </div>
            <div class="sigap-topbar__right">
                <span class="sigap-topbar__user">{{ auth()->user()->name }}</span>
                <span class="sigap-avatar sigap-avatar--sm">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </span>
            </div>
        </header>
        <main id="sigap-app-main" class="flex-1">
            @if (session('success'))
                <div class="sigap-alert sigap-alert--success mx-6 mt-6">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="sigap-alert sigap-alert--error mx-6 mt-6">{{ session('error') }}</div>
            @endif
            @yield('content')
        </main>
    </div>
</body>
</html>
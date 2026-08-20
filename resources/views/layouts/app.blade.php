<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'SIGAP')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body id="sigap-app-body" class="sigap-app-shell">
    <aside id="sigap-app-sidebar" class="sigap-sidebar">
        <div class="sigap-sidebar__brand">SIGAP</div>
        <nav class="sigap-sidebar__nav">
            <a href="{{ route('dashboard') }}" class="sigap-sidebar__link">Dashboard</a>
            <a href="{{ route('tickets.index') }}" class="sigap-sidebar__link">Tiket Saya</a>
            @role('agent')
                <a href="{{ route('agent.queue') }}" class="sigap-sidebar__link">Antrean Tiket</a>
            @endrole
            @role('admin')
                <a href="{{ route('admin.analytics') }}" class="sigap-sidebar__link">Analitik</a>
                <a href="{{ route('admin.master-data.departemen.index') }}" class="sigap-sidebar__link">Master Data</a>
                <a href="{{ route('admin.users.index') }}" class="sigap-sidebar__link">Manajemen Pengguna</a>
            @endrole
        </nav>
    </aside>

    <div id="sigap-app-content" class="sigap-app-content">
        <header id="sigap-app-topbar" class="sigap-topbar">
            <input type="search" class="sigap-topbar__search" placeholder="Cari tiket...">
            <span class="sigap-topbar__user">{{ auth()->user()->name }}</span>
        </header>
        <main id="sigap-app-main">
            @yield('content')
        </main>
    </div>
</body>
</html>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIGAP')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body id="sigap-auth-body">
    <div id="sigap-auth-shell" class="sigap-auth-shell">
        <aside id="sigap-auth-shell__brand" class="sigap-auth-shell__brand">
            <h1 class="sigap-auth-shell__logo">SIGAP</h1>
            <p class="sigap-auth-shell__tagline">Helpdesk System</p>
            <h2 class="sigap-auth-shell__headline">Solusi Cepat untuk Kebutuhan IT Anda.</h2>
            <p class="sigap-auth-shell__desc">
                Sistem helpdesk terintegrasi untuk mengelola, melacak, dan menyelesaikan
                setiap kendala dengan efisien dan transparan.
            </p>
        </aside>

        <section id="sigap-auth-shell__panel" class="sigap-auth-shell__panel">
            @yield('content')
        </section>
    </div>
</body>
</html>
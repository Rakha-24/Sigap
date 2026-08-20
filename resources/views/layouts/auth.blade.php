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
<body id="sigap-auth-body">
    <div id="sigap-auth-shell" class="sigap-auth-shell">
        <aside id="sigap-auth-shell__brand" class="sigap-auth-shell__brand">
            {{-- Elemen dekoratif murni CSS/SVG — dua orb blur untuk kedalaman,
                 plus SATU watermark ikon petir besar sebagai signature panel ini
                 (bukan pola berulang yang terkesan wallpaper). --}}
            <div class="sigap-auth-shell__orb sigap-auth-shell__orb--top" aria-hidden="true"></div>
            <div class="sigap-auth-shell__orb sigap-auth-shell__orb--bottom" aria-hidden="true"></div>
            <svg class="sigap-auth-shell__watermark" viewBox="0 0 100 100" aria-hidden="true">
                <path d="M56 3 17 57h27l-5 41 42-58H54l2-37Z" fill="none" stroke="white" stroke-width="1.5"/>
            </svg>

            <div class="sigap-auth-shell__content">
                <div class="sigap-auth-shell__mark">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M13 2 4 14h6l-1 8 9-12h-6l1-8Z" fill="#FFFFFF"/>
                    </svg>
                </div>

                <h1 class="sigap-auth-shell__logo">SIGAP</h1>
                <p class="sigap-auth-shell__tagline">Helpdesk System</p>

                <h2 class="sigap-auth-shell__headline">Solusi Cepat untuk Kebutuhan IT Anda.</h2>
                <p class="sigap-auth-shell__desc">
                    Sistem helpdesk terintegrasi untuk mengelola, melacak, dan menyelesaikan
                    setiap kendala dengan efisien dan transparan.
                </p>

                <ul class="sigap-auth-shell__features">
                    @foreach(['Cepat' => 'M8 1 2.5 8h4L6 15l6.5-8h-4L10 1Z', 'Tepat' => 'M8 1a7 7 0 1 0 0 14A7 7 0 0 0 8 1Zm0 10.5a.9.9 0 1 1 0-1.8.9.9 0 0 1 0 1.8Zm.9-4.1c-.05.5-.35.7-.9.7s-.85-.2-.9-.7L6.9 4.3c-.05-.6.35-1.05.95-1.05h.3c.6 0 1 .45.95 1.05l-.25 3.1Z', 'Terpantau' => 'M8 1a5 5 0 0 0-5 5c0 3.75 5 8 5 8s5-4.25 5-8a5 5 0 0 0-5-5Zm0 7a2 2 0 1 1 0-4 2 2 0 0 1 0 4Z'] as $fitur => $path)
                        <li class="sigap-auth-shell__feature">
                            <svg width="14" height="14" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                <path d="{{ $path }}" fill="#FFFFFF" fill-opacity="0.85"/>
                            </svg>
                            {{ $fitur }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </aside>

        <section id="sigap-auth-shell__panel" class="sigap-auth-shell__panel">
            @yield('content')
        </section>
    </div>
</body>
</html>
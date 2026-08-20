@extends('layouts.guest')
@section('title', 'SIGAP - Sistem Informasi Gangguan & Antrean Pelayanan')

@section('content')
<section id="sigap-landing-hero" class="sigap-hero">
    <div class="sigap-hero__text">
        <h1 class="sigap-hero__title">Solusi Cepat Penanganan Keluhan</h1>
        <p class="sigap-hero__desc">
            Sistem helpdesk terintegrasi untuk mengelola, melacak, dan menyelesaikan
            keluhan masyarakat dan instansi dengan efisien dan transparan.
        </p>
        <div class="sigap-hero__actions">
            <a href="{{ route('guest.ticket.create') }}" class="sigap-btn sigap-btn--primary" id="sigap-landing-hero__cta-primary">
                Mulai Sekarang →
            </a>
            <a href="#sigap-landing-features" class="sigap-btn sigap-btn--secondary" id="sigap-landing-hero__cta-secondary">
                Pelajari Lebih Lanjut
            </a>
        </div>
    </div>
    <div class="sigap-hero__illustration" id="sigap-landing-hero__illustration">
        {{-- Ilustrasi SVG custom (bukan foto stok) — ringkasan visual dashboard tiket:
             tren volume, distribusi kategori, dan dua baris tiket dengan status. --}}
        <svg viewBox="0 0 480 360" class="w-full h-full" role="img" aria-label="Ilustrasi dashboard tiket SIGAP">
            <defs>
                <filter id="sigapHeroShadow" x="-20%" y="-20%" width="140%" height="140%">
                    <feDropShadow dx="0" dy="8" stdDeviation="12" flood-color="#122868" flood-opacity="0.12"/>
                </filter>
            </defs>

            <!-- Kartu dashboard utama -->
            <g filter="url(#sigapHeroShadow)">
                <rect x="40" y="24" width="400" height="272" rx="16" fill="#FFFFFF" stroke="#E2E8F0"/>
                <rect x="40" y="24" width="400" height="40" rx="16" fill="#EFF4FF"/>
                <rect x="40" y="52" width="400" height="12" fill="#EFF4FF"/>
                <circle cx="60" cy="44" r="4" fill="#CBD5E1"/>
                <circle cx="74" cy="44" r="4" fill="#CBD5E1"/>
                <circle cx="88" cy="44" r="4" fill="#CBD5E1"/>
                <rect x="360" y="38" width="60" height="12" rx="6" fill="#BFD0FD"/>
            </g>

            <!-- Mini bar chart: tren volume tiket -->
            <g>
                <rect x="64" y="204" width="20" height="52" rx="4" fill="#BFD0FD"/>
                <rect x="94" y="180" width="20" height="76" rx="4" fill="#3D63E8"/>
                <rect x="124" y="160" width="20" height="96" rx="4" fill="#2554D6"/>
                <rect x="154" y="192" width="20" height="64" rx="4" fill="#3D63E8"/>
                <rect x="184" y="144" width="20" height="112" rx="4" fill="#17348A"/>
            </g>

            <!-- Mini donut chart: distribusi kategori -->
            <g transform="translate(340,204)">
                <circle r="36" fill="none" stroke="#EFF4FF" stroke-width="14"/>
                <circle r="36" fill="none" stroke="#2554D6" stroke-width="14"
                        stroke-dasharray="101 226" stroke-linecap="round"/>
                <circle r="36" fill="none" stroke="#10B981" stroke-width="14"
                        stroke-dasharray="60 226" stroke-dashoffset="-101" stroke-linecap="round"/>
                <circle r="36" fill="none" stroke="#F59E0B" stroke-width="14"
                        stroke-dasharray="45 226" stroke-dashoffset="-161" stroke-linecap="round"/>
            </g>

            <!-- Badge mengambang: tiket selesai -->
            <g filter="url(#sigapHeroShadow)">
                <circle cx="424" cy="88" r="26" fill="#10B981"/>
                <path d="M414 88l7 7 14-14" stroke="#FFFFFF" stroke-width="3.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
            </g>

            <!-- Badge mengambang: prioritas tinggi -->
            <g filter="url(#sigapHeroShadow)">
                <circle cx="56" cy="272" r="22" fill="#FFFFFF"/>
                <circle cx="56" cy="272" r="22" fill="none" stroke="#F59E0B" stroke-width="2"/>
                <circle cx="56" cy="272" r="6" fill="#F59E0B"/>
            </g>
        </svg>
    </div>
</section>

<section id="sigap-landing-features" class="sigap-features">
    <h2 class="sigap-features__title">Fitur Unggulan Kami</h2>
    <p class="sigap-features__subtitle">
        Dibangun untuk kecepatan dan keandalan, SIGAP menyediakan perangkat
        yang Anda butuhkan untuk penanganan keluhan yang optimal.
    </p>

    <div class="sigap-features__grid" id="sigap-landing-features__grid">
        <div class="sigap-feature-card" id="sigap-feature-card-antrean">
            <h3 class="sigap-feature-card__title">Antrean Prioritas</h3>
            <p class="sigap-feature-card__desc">
                Sistem antrean cerdas yang mengurutkan tiket berdasarkan tingkat urgensi dan dampak.
            </p>
        </div>
        <div class="sigap-feature-card" id="sigap-feature-card-pelacakan">
            <h3 class="sigap-feature-card__title">Pelacakan Real-time</h3>
            <p class="sigap-feature-card__desc">
                Pantau status penyelesaian keluhan Anda secara langsung dan dapatkan notifikasi instan.
            </p>
        </div>
        <div class="sigap-feature-card" id="sigap-feature-card-bukti">
            <h3 class="sigap-feature-card__title">Bukti Penanganan</h3>
            <p class="sigap-feature-card__desc">
                Lampirkan foto dan dokumen sebagai bukti validasi penanganan keluhan yang transparan.
            </p>
        </div>
        <div class="sigap-feature-card" id="sigap-feature-card-analitik">
            <h3 class="sigap-feature-card__title">Analitik & Laporan</h3>
            <p class="sigap-feature-card__desc">
                Hasilkan laporan komprehensif untuk mengevaluasi kinerja tim dan tren keluhan.
            </p>
        </div>
    </div>
</section>
@endsection
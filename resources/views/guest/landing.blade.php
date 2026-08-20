@extends('layouts.guest')
@section('title', 'SIGAP - Sistem Informasi Gangguan & Antrean Pelayanan')

@section('content')
<section id="sigap-landing-hero" class="sigap-hero">
    <div class="sigap-hero__text">
        <span class="sigap-hero__badge">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12 6 12 12 16 14"/>
            </svg>
            Helpdesk Terpadu &amp; Real-time
        </span>
        <h1 class="sigap-hero__title">
            Solusi Cepat<br>
            <span class="sigap-hero__title-accent">Penanganan Keluhan</span>
        </h1>
        <p class="sigap-hero__desc">
            Sistem helpdesk terintegrasi untuk mengelola, melacak, dan menyelesaikan
            keluhan masyarakat dan instansi dengan efisien dan transparan.
        </p>
        <div class="sigap-hero__actions">
            <a href="{{ route('guest.ticket.create') }}" class="sigap-btn sigap-btn--primary" id="sigap-landing-hero__cta-primary">
                Laporkan Sekarang
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12"/>
                    <polyline points="12 5 19 12 12 19"/>
                </svg>
            </a>
            <a href="#sigap-landing-features" class="sigap-btn sigap-btn--secondary" id="sigap-landing-hero__cta-secondary">
                Pelajari Lebih Lanjut
            </a>
        </div>
        <div class="sigap-hero__trust">
            <div class="sigap-hero__trust-item">
                <span class="sigap-hero__trust-value">100%</span>
                <span>Transparan</span>
            </div>
            <div class="sigap-hero__trust-item">
                <span class="sigap-hero__trust-value">24/7</span>
                <span>Dukungan</span>
            </div>
            <div class="sigap-hero__trust-item">
                <span class="sigap-hero__trust-value">SLA</span>
                <span>Terukur</span>
            </div>
        </div>
    </div>
    <div class="sigap-hero__illustration" id="sigap-landing-hero__illustration">
        <svg viewBox="0 0 480 360" class="w-full h-full" role="img" aria-label="Ilustrasi dashboard tiket SIGAP">
            <defs>
                <filter id="sigapHeroShadow" x="-20%" y="-20%" width="140%" height="140%">
                    <feDropShadow dx="0" dy="8" stdDeviation="12" flood-color="#122868" flood-opacity="0.12"/>
                </filter>
                <linearGradient id="heroGrad" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#2554D6"/>
                    <stop offset="100%" stop-color="#17348A"/>
                </linearGradient>
            </defs>

            {{-- Kartu dashboard utama --}}
            <g filter="url(#sigapHeroShadow)">
                <rect x="40" y="24" width="400" height="272" rx="16" fill="#FFFFFF" stroke="#E2E8F0"/>
                <rect x="40" y="24" width="400" height="40" rx="16" fill="#EFF4FF"/>
                <rect x="40" y="52" width="400" height="12" fill="#EFF4FF"/>
                <circle cx="60" cy="44" r="4" fill="#CBD5E1"/>
                <circle cx="74" cy="44" r="4" fill="#CBD5E1"/>
                <circle cx="88" cy="44" r="4" fill="#CBD5E1"/>
                <rect x="360" y="38" width="60" height="12" rx="6" fill="#BFD0FD"/>
            </g>

            {{-- Mini bar chart --}}
            <g>
                <rect x="64" y="204" width="20" height="52" rx="4" fill="#BFD0FD"/>
                <rect x="94" y="180" width="20" height="76" rx="4" fill="#3D63E8"/>
                <rect x="124" y="160" width="20" height="96" rx="4" fill="url(#heroGrad)"/>
                <rect x="154" y="192" width="20" height="64" rx="4" fill="#3D63E8"/>
                <rect x="184" y="144" width="20" height="112" rx="4" fill="#17348A"/>
            </g>

            {{-- Garis tren --}}
            <polyline points="74,200 104,176 134,156 164,188 194,140"
                      fill="none" stroke="#2554D6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" opacity="0.3"/>

            {{-- Mini donut chart --}}
            <g transform="translate(340,204)">
                <circle r="36" fill="none" stroke="#EFF4FF" stroke-width="14"/>
                <circle r="36" fill="none" stroke="#2554D6" stroke-width="14"
                        stroke-dasharray="101 226" stroke-linecap="round"/>
                <circle r="36" fill="none" stroke="#10B981" stroke-width="14"
                        stroke-dasharray="60 226" stroke-dashoffset="-101" stroke-linecap="round"/>
                <circle r="36" fill="none" stroke="#F59E0B" stroke-width="14"
                        stroke-dasharray="45 226" stroke-dashoffset="-161" stroke-linecap="round"/>
            </g>

            {{-- Badge: tiket selesai --}}
            <g filter="url(#sigapHeroShadow)">
                <circle cx="424" cy="88" r="26" fill="#10B981"/>
                <path d="M414 88l7 7 14-14" stroke="#FFFFFF" stroke-width="3.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
            </g>

            {{-- Badge: prioritas --}}
            <g filter="url(#sigapHeroShadow)">
                <circle cx="56" cy="272" r="22" fill="#FFFFFF"/>
                <circle cx="56" cy="272" r="22" fill="none" stroke="#F59E0B" stroke-width="2"/>
                <circle cx="56" cy="272" r="6" fill="#F59E0B"/>
            </g>

            {{-- Tiket row mini --}}
            <g>
                <rect x="64" y="84" width="320" height="28" rx="6" fill="#F8FAFC"/>
                <rect x="72" y="92" width="80" height="12" rx="3" fill="#E2E8F0"/>
                <rect x="160" y="92" width="120" height="12" rx="3" fill="#E2E8F0"/>
                <rect x="340" y="92" width="36" height="12" rx="6" fill="#DCFCE7"/>
            </g>
            <g>
                <rect x="64" y="118" width="320" height="28" rx="6" fill="#F8FAFC"/>
                <rect x="72" y="126" width="80" height="12" rx="3" fill="#E2E8F0"/>
                <rect x="160" y="126" width="140" height="12" rx="3" fill="#E2E8F0"/>
                <rect x="340" y="126" width="36" height="12" rx="6" fill="#FEF3C7"/>
            </g>
        </svg>
    </div>
</section>

<section id="sigap-landing-stats" class="sigap-stats-band">
    <div class="sigap-stats-band__inner">
        <div class="sigap-stats-band__item">
            <span class="sigap-stats-band__value">100%</span>
            <span class="sigap-stats-band__label">Pelacakan Transparan</span>
        </div>
        <div class="sigap-stats-band__item">
            <span class="sigap-stats-band__value">24/7</span>
            <span class="sigap-stats-band__label">Dukungan Berkelanjutan</span>
        </div>
        <div class="sigap-stats-band__item">
            <span class="sigap-stats-band__value">3</span>
            <span class="sigap-stats-band__label">Tingkat Prioritas</span>
        </div>
        <div class="sigap-stats-band__item">
            <span class="sigap-stats-band__value">SLA</span>
            <span class="sigap-stats-band__label">Terukur &amp; Terpantau</span>
        </div>
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
            <div class="sigap-feature-card__icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="4" rx="1"/>
                    <rect x="3" y="10" width="18" height="4" rx="1"/>
                    <rect x="3" y="16" width="18" height="4" rx="1"/>
                </svg>
            </div>
            <h3 class="sigap-feature-card__title">Antrean Prioritas</h3>
            <p class="sigap-feature-card__desc">
                Sistem antrean cerdas yang mengurutkan tiket berdasarkan tingkat urgensi dan dampak.
            </p>
        </div>
        <div class="sigap-feature-card" id="sigap-feature-card-pelacakan">
            <div class="sigap-feature-card__icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
            <h3 class="sigap-feature-card__title">Pelacakan Real-time</h3>
            <p class="sigap-feature-card__desc">
                Pantau status penyelesaian keluhan Anda secara langsung dan dapatkan notifikasi instan.
            </p>
        </div>
        <div class="sigap-feature-card" id="sigap-feature-card-bukti">
            <div class="sigap-feature-card__icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                    <polyline points="10 9 9 9 8 9"/>
                </svg>
            </div>
            <h3 class="sigap-feature-card__title">Bukti Penanganan</h3>
            <p class="sigap-feature-card__desc">
                Lampirkan foto dan dokumen sebagai bukti validasi penanganan keluhan yang transparan.
            </p>
        </div>
        <div class="sigap-feature-card" id="sigap-feature-card-analitik">
            <div class="sigap-feature-card__icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="20" x2="18" y2="10"/>
                    <line x1="12" y1="20" x2="12" y2="4"/>
                    <line x1="6" y1="20" x2="6" y2="14"/>
                </svg>
            </div>
            <h3 class="sigap-feature-card__title">Analitik &amp; Laporan</h3>
            <p class="sigap-feature-card__desc">
                Hasilkan laporan komprehensif untuk mengevaluasi kinerja tim dan tren keluhan.
            </p>
        </div>
    </div>
</section>

<section id="sigap-landing-cta" class="sigap-cta">
    <div class="sigap-cta__inner">
        <h2 class="sigap-cta__title">Ada kendala? Kami siap membantu.</h2>
        <p class="sigap-cta__desc">
            Laporkan masalah Anda sekarang dan tim kami akan segera menindaklanjutinya
            sesuai prioritas dan SLA yang telah ditetapkan.
        </p>
        <div class="sigap-cta__actions">
            <a href="{{ route('guest.ticket.create') }}" class="sigap-cta__btn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Buat Laporan
            </a>
            <a href="{{ route('guest.track.form') }}" class="sigap-btn sigap-btn--secondary bg-white/10 border-white/25 text-white hover:bg-white/20 hover:border-white/40">
                Lacak Tiket Saya
            </a>
        </div>
    </div>
</section>
@endsection
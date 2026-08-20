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
        {{-- Slot untuk ilustrasi/mockup dashboard, isi sesuai asset desainmu --}}
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
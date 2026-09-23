@extends('layouts.guest')
@section('title', 'SIGAP - Sistem Helpdesk Terpadu')

@section('content')
{{-- ===================== HERO ===================== --}}
<section id="sigap-landing-hero" class="sigap-hero">
    <div class="sigap-hero__container">
        <div>
            <span class="sigap-hero__badge">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
                Sistem Helpdesk Terpadu
            </span>
            <h1 class="sigap-hero__title">
                Solusi Cepat
            </h1>
            <h1 class="sigap-hero__title">
                <span class="sigap-hero__title-accent">Setiap laporan.</span>
            </h1>
            <p class="sigap-hero__desc">
                SIGAP menyatukan pelaporan, antrean penanganan, dan analitik SLA
                dalam satu alur kerja yang jelas — untuk tim IT dan seluruh unit kerja Anda.
            </p>
            <div class="sigap-hero__actions">
                <a href="{{ route('guest.ticket.create') }}" class="sigap-btn sigap-btn--primary" id="sigap-landing-hero__cta-primary">
                    Laporkan Kendala
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <line x1="5" y1="12" x2="19" y2="12"/>
                        <polyline points="12 5 19 12 12 19"/>
                    </svg>
                </a>
                <a href="{{ route('guest.track.form') }}" class="sigap-btn sigap-btn--secondary" id="sigap-landing-hero__cta-secondary">
                    Lacak Tiket
                </a>
            </div>
            <div class="sigap-hero__trust">
                <div class="sigap-hero__trust-item">
                    <span class="sigap-hero__trust-value">24/7</span>
                    <span class="sigap-hero__trust-label">Dukungan</span>
                </div>
                <div class="sigap-hero__trust-item">
                    <span class="sigap-hero__trust-value">SLA</span>
                    <span class="sigap-hero__trust-label">Terukur</span>
                </div>
                <div class="sigap-hero__trust-item">
                    <span class="sigap-hero__trust-value">100%</span>
                    <span class="sigap-hero__trust-label">Transparan</span>
                </div>
            </div>
        </div>

        {{-- Mockup dashboard: browser chrome + KPI + chart + daftar tiket --}}
        <div class="hidden lg:block" aria-hidden="true">
            <div class="relative">
                <div class="absolute -inset-x-6 -inset-y-4 bg-sigap-50 border border-sigap-100 rounded-3xl" aria-hidden="true"></div>
                <div id="sigap-landing-hero__mockup" class="relative rounded-xl border border-slate-200 bg-white shadow-sigap-pop overflow-hidden">
                    {{-- Browser chrome --}}
                    <div class="flex items-center gap-2 px-4 py-3 border-b border-slate-100 bg-slate-50/70">
                        <span class="w-2.5 h-2.5 rounded-full bg-slate-200"></span>
                        <span class="w-2.5 h-2.5 rounded-full bg-slate-200"></span>
                        <span class="w-2.5 h-2.5 rounded-full bg-slate-200"></span>
                        <div class="ml-2 flex-1 max-w-xs bg-white border border-slate-200 rounded-md px-3 py-1 text-[11px] text-slate-400 font-medium flex items-center gap-1.5">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                            helpdesk.sigap.local
                        </div>
                    </div>

                    <div class="p-5 flex flex-col gap-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-slate-900">Ringkasan Tiket</p>
                                <p class="text-[11px] text-slate-400">Pembaruan terkini seluruh unit</p>
                            </div>
                            <span class="sigap-badge sigap-badge--resolved">Aktif</span>
                        </div>

                        {{-- KPI cards --}}
                        <div class="grid grid-cols-3 gap-3">
                            <div class="rounded-lg border border-slate-200 bg-white p-3">
                                <p class="text-[11px] text-slate-400 font-medium">Total</p>
                                <p class="text-xl font-bold text-slate-900 mt-0.5">128</p>
                            </div>
                            <div class="rounded-lg border border-slate-200 bg-white p-3">
                                <p class="text-[11px] text-slate-400 font-medium">Terbuka</p>
                                <p class="text-xl font-bold text-amber-600 mt-0.5">14</p>
                            </div>
                            <div class="rounded-lg border border-slate-200 bg-white p-3">
                                <p class="text-[11px] text-slate-400 font-medium">Resolusi</p>
                                <p class="text-xl font-bold text-emerald-600 mt-0.5">96%</p>
                            </div>
                        </div>

                        {{-- Bar chart --}}
                        <div class="rounded-lg border border-slate-200 bg-white p-4">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-[11px] font-semibold text-slate-700">Tiket per Minggu</p>
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-slate-300">
                                    <polyline points="6 9 6 2"/><polyline points="18 9 18 2"/>
                                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                                </svg>
                            </div>
                            <div class="flex items-end gap-2 h-20">
                                @php $bars = [45, 70, 55, 85, 65, 95, 75]; @endphp
                                @foreach($bars as $i => $h)
                                    <div class="flex-1 flex flex-col justify-end">
                                        <div class="rounded-sm {{ $i === 5 ? 'bg-sigap-600' : 'bg-sigap-100' }}" style="height: {{ $h }}%"></div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="flex gap-2 mt-1.5">
                                @foreach(['S','S','R','K','J','S','M'] as $d)
                                    <div class="flex-1 text-center text-[9px] text-slate-400">{{ $d }}</div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Ticket rows --}}
                        <div class="flex flex-col gap-2">
                            <div class="flex items-center gap-3 rounded-lg border border-slate-200 px-3 py-2.5 bg-white">
                                <span class="w-7 h-7 rounded-md bg-sigap-50 text-sigap-600 flex items-center justify-center shrink-0">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/>
                                        <path d="M14 2v4a1 1 0 0 0 1 1h3"/>
                                    </svg>
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-slate-800 truncate">Perbaikan jaringan kampus</p>
                                    <p class="text-[10px] text-slate-400">#TKT-2026-0412</p>
                                </div>
                                <span class="sigap-badge sigap-badge--in_progress">Diproses</span>
                            </div>
                            <div class="flex items-center gap-3 rounded-lg border border-slate-200 px-3 py-2.5 bg-white">
                                <span class="w-7 h-7 rounded-md bg-slate-50 text-slate-500 flex items-center justify-center shrink-0">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/>
                                        <path d="M14 2v4a1 1 0 0 0 1 1h3"/>
                                    </svg>
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-slate-800 truncate">Reset akun email divisi</p>
                                    <p class="text-[10px] text-slate-400">#TKT-2026-0407</p>
                                </div>
                                <span class="sigap-badge sigap-badge--closed">Selesai</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ===================== STATS BAND ===================== --}}
<section id="sigap-landing-stats" class="sigap-stats-band">
    <div class="sigap-stats-band__inner">
        <div class="sigap-stats-band__item">
            <span class="sigap-stats-band__value">3 Tier</span>
            <span class="sigap-stats-band__label">Prioritas Penanganan</span>
        </div>
        <div class="sigap-stats-band__item">
            <span class="sigap-stats-band__value">24/7</span>
            <span class="sigap-stats-band__label">Pelaporan Berkelanjutan</span>
        </div>
        <div class="sigap-stats-band__item">
            <span class="sigap-stats-band__value">SLA</span>
            <span class="sigap-stats-band__label">Terukur per Kategori</span>
        </div>
        <div class="sigap-stats-band__item">
            <span class="sigap-stats-band__value">100%</span>
            <span class="sigap-stats-band__label">Pelacakan Transparan</span>
        </div>
    </div>
</section>

{{-- ===================== FEATURES ===================== --}}
<section id="sigap-landing-features" class="sigap-features">
    <div class="sigap-features__inner">
        <span class="sigap-features__eyebrow">Fitur Inti</span>
        <h2 class="sigap-features__title">Alur penanganan yang terarah</h2>
        <p class="sigap-features__subtitle">
            Dari laporan masuk hingga pencatatan penyelesaian, setiap tahapan
            terdokumentasi dan dapat dipertanggungjawabkan.
        </p>

        <div class="sigap-features__grid" id="sigap-landing-features__grid">
            <div class="sigap-feature-card" id="sigap-feature-card-antrean">
                <div class="sigap-feature-card__icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="3" y="4" width="18" height="4" rx="1"/>
                        <rect x="3" y="10" width="18" height="4" rx="1"/>
                        <rect x="3" y="16" width="18" height="4" rx="1"/>
                    </svg>
                </div>
                <h3 class="sigap-feature-card__title">Antrean Prioritas</h3>
                <p class="sigap-feature-card__desc">
                    Tiket diurutkan otomatis berdasarkan tingkat urgensi agar penanganan tepat sasaran.
                </p>
            </div>
            <div class="sigap-feature-card" id="sigap-feature-card-pelacakan">
                <div class="sigap-feature-card__icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
                <h3 class="sigap-feature-card__title">Pelacakan Real-time</h3>
                <p class="sigap-feature-card__desc">
                    Pantau status laporan Anda kapan saja melalui halaman pelacakan publik.
                </p>
            </div>
            <div class="sigap-feature-card" id="sigap-feature-card-bukti">
                <div class="sigap-feature-card__icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                        <polyline points="10 9 9 9 8 9"/>
                    </svg>
                </div>
                <h3 class="sigap-feature-card__title">Bukti Penanganan</h3>
                <p class="sigap-feature-card__desc">
                    Lampirkan foto dan dokumen sebagai bukti validasi penanganan yang transparan.
                </p>
            </div>
            <div class="sigap-feature-card" id="sigap-feature-card-analitik">
                <div class="sigap-feature-card__icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <line x1="18" y1="20" x2="18" y2="10"/>
                        <line x1="12" y1="20" x2="12" y2="4"/>
                        <line x1="6" y1="20" x2="6" y2="14"/>
                    </svg>
                </div>
                <h3 class="sigap-feature-card__title">Analitik &amp; Laporan</h3>
                <p class="sigap-feature-card__desc">
                    Evaluasi kinerja tim dan tren keluhan melalui laporan yang dapat diekspor.
                </p>
            </div>
        </div>
    </div>
</section>

{{-- ===================== WORKFLOW ===================== --}}
<section id="sigap-landing-workflow" class="sigap-workflow">
    <div class="sigap-workflow__inner">
        <span class="sigap-features__eyebrow">Cara Kerja</span>
        <h2 class="sigap-features__title">Tiga langkah sederhana</h2>
        <p class="sigap-features__subtitle">
            Proses yang ringkas dan terukur, dari laporan hingga penyelesaian.
        </p>

        <div class="sigap-workflow__grid">
            <div class="sigap-workflow__item">
                <span class="sigap-workflow__step">1</span>
                <h3 class="sigap-workflow__title">Laporkan kendala</h3>
                <p class="sigap-workflow__desc">
                    Ajukan laporan melalui form publik yang terstruktur, lengkap dengan lampiran bukti.
                </p>
            </div>
            <div class="sigap-workflow__item">
                <span class="sigap-workflow__step">2</span>
                <h3 class="sigap-workflow__title">Antre &amp; ditangani</h3>
                <p class="sigap-workflow__desc">
                    Petugas helpdesk mengambil tiket sesuai prioritas dan kategori penanganan.
                </p>
            </div>
            <div class="sigap-workflow__item">
                <span class="sigap-workflow__step">3</span>
                <h3 class="sigap-workflow__title">Selidiki &amp; selesai</h3>
                <p class="sigap-workflow__desc">
                    Penyelesaian dicatat lengkap dengan bukti, dan pelapor dapat memantau seluruh riwayat.
                </p>
            </div>
        </div>
    </div>
</section>

{{-- ===================== CTA ===================== --}}
<section id="sigap-landing-cta" class="sigap-cta">
    <div class="sigap-cta__inner">
        <h2 class="sigap-cta__title">Ada kendala yang perlu ditindaklanjuti?</h2>
        <p class="sigap-cta__desc">
            Laporkan sekarang. Tim helpdesk akan menindaklanjuti sesuai prioritas
            dan batas waktu SLA yang telah ditetapkan.
        </p>
        <div class="sigap-cta__actions">
            <a href="{{ route('guest.ticket.create') }}" class="sigap-cta__btn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Buat Laporan
            </a>
            <a href="{{ route('guest.track.form') }}" class="sigap-btn sigap-btn--secondary bg-white/10 border-white/20 text-white hover:bg-white/20 hover:border-white/40">
                Lacak Tiket Saya
            </a>
        </div>
    </div>
</section>
@endsection
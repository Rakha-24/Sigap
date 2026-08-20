@extends('layouts.app')
@section('title', 'Analitik Sistem')

@section('content')
<section id="sigap-admin-analytics" class="sigap-page">
    <div class="sigap-page__header">
        <div>
            <h1 class="sigap-page__title">Analitik Sistem</h1>
            <p class="sigap-page__subtitle">Ringkasan performa dan statistik penanganan tiket.</p>
        </div>
        <div class="sigap-page__actions" id="sigap-admin-analytics__actions">
            <a href="{{ route('admin.analytics.export') }}" class="sigap-btn sigap-btn--secondary" id="sigap-admin-analytics__export">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Export Laporan (.xlsx)
            </a>
        </div>
    </div>

    @php
        $maxDept = max($metrics['per_departemen']->pluck('jumlah')->push(1)->max(), 1);
        $avgSla = $metrics['kepatuhan_sla']->avg('persentase') ?: 0;
    @endphp

    <div class="sigap-stats-grid" id="sigap-admin-analytics__stats">
        <div class="sigap-stat-card">
            <div class="sigap-stat-card__icon sigap-stat-card__icon--blue">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/>
                    <path d="M14 2v4a1 1 0 0 0 1 1h3"/>
                </svg>
            </div>
            <span class="sigap-stat-card__label">Total Tiket</span>
            <span class="sigap-stat-card__value">{{ $metrics['total_tiket'] }}</span>
        </div>
        <div class="sigap-stat-card">
            <div class="sigap-stat-card__icon sigap-stat-card__icon--amber">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
            <span class="sigap-stat-card__label">Rata-rata Resolusi</span>
            <span class="sigap-stat-card__value">{{ $metrics['rata_rata_resolusi_jam'] }} <span class="text-base font-semibold text-slate-400">jam</span></span>
        </div>
        <div class="sigap-stat-card">
            <div class="sigap-stat-card__icon sigap-stat-card__icon--red">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/>
                    <line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
            </div>
            <span class="sigap-stat-card__label">Melewati SLA</span>
            <span class="sigap-stat-card__value">{{ $metrics['melewati_sla'] }}</span>
        </div>
    </div>

    <div class="sigap-chart-grid" id="sigap-admin-analytics__charts">
        <div class="sigap-chart-card" id="sigap-admin-analytics__chart-volume">
            <span class="sigap-chart-card__title">Volume Tiket per Departemen</span>
            <span class="sigap-chart-card__subtitle">Total laporan masuk berdasarkan departemen tujuan.</span>
            @if($metrics['per_departemen']->count())
                <div class="sigap-bar-chart">
                    @foreach($metrics['per_departemen'] as $row)
                        <div class="sigap-bar-chart__row">
                            <span class="sigap-bar-chart__label" title="{{ $row->nama }}">{{ $row->nama }}</span>
                            <div class="sigap-bar-chart__track">
                                <div class="sigap-bar-chart__fill" style="width: {{ round(($row->jumlah / $maxDept) * 100) }}%"></div>
                            </div>
                            <span class="sigap-bar-chart__value">{{ $row->jumlah }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="sigap-empty">
                    <p class="sigap-empty__text">Belum ada data tiket.</p>
                </div>
            @endif
        </div>

        <div class="sigap-chart-card" id="sigap-admin-analytics__chart-kategori">
            <span class="sigap-chart-card__title">Kepatuhan SLA</span>
            <span class="sigap-chart-card__subtitle">Persentase tiket selesai sebelum batas waktu SLA.</span>
            <div class="sigap-donut-chart">
                <div class="sigap-donut-chart__ring">
                    <svg viewBox="0 0 36 36">
                        <circle cx="18" cy="18" r="15.9155" fill="none" stroke="#EFF4FF" stroke-width="4"/>
                        <circle cx="18" cy="18" r="15.9155" fill="none"
                                stroke="{{ $avgSla >= 80 ? '#10B981' : ($avgSla >= 50 ? '#F59E0B' : '#EF4444') }}"
                                stroke-width="4" stroke-dasharray="{{ round($avgSla) }}, 100"
                                stroke-linecap="round"/>
                    </svg>
                    <div class="sigap-donut-chart__center">
                        <span class="sigap-donut-chart__center-value">{{ round($avgSla) }}%</span>
                        <span class="sigap-donut-chart__center-label">Rata-rata</span>
                    </div>
                </div>
                <div class="sigap-donut-chart__legend">
                    @forelse($metrics['kepatuhan_sla'] as $row)
                        <div class="sigap-donut-chart__legend-item">
                            <span class="sigap-donut-chart__legend-dot" style="background: {{ $row['persentase'] >= 80 ? '#10B981' : ($row['persentase'] >= 50 ? '#F59E0B' : '#EF4444') }}"></span>
                            {{ $row['nama'] }}
                            <span class="sigap-donut-chart__legend-value">{{ round($row['persentase']) }}%</span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400">Belum ada tiket yang diselesaikan.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
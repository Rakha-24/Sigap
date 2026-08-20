@extends('layouts.app')
@section('title', 'Analitik Sistem')

@section('content')
<section id="sigap-admin-analytics" class="sigap-page">
    <h1 class="sigap-page__title">Analitik Sistem</h1>

    <div class="sigap-page__actions" id="sigap-admin-analytics__actions">
        <a href="{{ route('admin.analytics.export') }}" class="sigap-btn sigap-btn--secondary" id="sigap-admin-analytics__export">
            Export Laporan (.xlsx)
        </a>
    </div>

    <div class="sigap-stats-grid" id="sigap-admin-analytics__stats">
        <div class="sigap-stat-card">
            <span class="sigap-stat-card__label">Total Tiket</span>
            <span class="sigap-stat-card__value">{{ $metrics['total_tiket'] }}</span>
        </div>
        <div class="sigap-stat-card">
            <span class="sigap-stat-card__label">Rata-rata Resolusi (Jam)</span>
            <span class="sigap-stat-card__value">{{ $metrics['rata_rata_resolusi_jam'] }}</span>
        </div>
        <div class="sigap-stat-card">
            <span class="sigap-stat-card__label">Melewati SLA</span>
            <span class="sigap-stat-card__value">{{ $metrics['melewati_sla'] }}</span>
        </div>
    </div>

    <div class="sigap-chart-grid" id="sigap-admin-analytics__charts">
        <div class="sigap-chart-card" id="sigap-admin-analytics__chart-volume">
            {{-- Kaitkan data $metrics['per_departemen'] ke library chart pilihanmu --}}
        </div>
        <div class="sigap-chart-card" id="sigap-admin-analytics__chart-kategori">
            {{-- Kaitkan data $metrics['kepatuhan_sla'] --}}
        </div>
    </div>
</section>
@endsection
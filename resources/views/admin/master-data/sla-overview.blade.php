@extends('layouts.app')
@section('title', 'SLA & Prioritas')

@section('content')
<section id="sigap-admin-master-data-sla" class="sigap-page">
    <div class="sigap-page__header">
        <div>
            <h1 class="sigap-page__title">Manajemen Master Data</h1>
            <p class="sigap-page__subtitle">Kelola data referensi utama sistem.</p>
        </div>
    </div>

    @include('admin.master-data._tabs')

    <div class="sigap-card" id="sigap-admin-master-data-sla__content">
        <div class="sigap-card__header">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Ringkasan SLA per Departemen</h2>
                <p class="text-sm text-slate-500 mt-0.5">
                    SLA final tiket = Default SLA kategori × faktor prioritas (Tinggi ×0.5, Sedang ×1.0, Rendah ×1.5).
                </p>
            </div>
        </div>

        @forelse($departemens as $dept)
            <div class="sigap-sla-group" id="sigap-admin-master-data-sla__dept-{{ $dept->id }}">
                <h3 class="sigap-sla-group__title flex items-center gap-2">
                    <span class="sigap-avatar sigap-avatar--sm">{{ strtoupper(substr($dept->nama, 0, 1)) }}</span>
                    {{ $dept->nama }}
                </h3>
                <div class="sigap-table-wrapper !border-0 !shadow-none !rounded-none !border-t !border-slate-100">
                    <table class="sigap-table">
                        <thead>
                            <tr><th>Kategori</th><th>Default SLA (Sedang)</th><th>SLA Tinggi</th><th>SLA Rendah</th><th>Aksi</th></tr>
                        </thead>
                        <tbody>
                            @forelse($dept->kategoris as $kategori)
                                <tr>
                                    <td class="font-medium text-slate-800">{{ $kategori->nama }}</td>
                                    <td><span class="sigap-badge sigap-badge--role-agent">{{ $kategori->default_sla_jam }} jam</span></td>
                                    <td class="text-slate-500">{{ round($kategori->default_sla_jam * 0.5) }} jam</td>
                                    <td class="text-slate-500">{{ round($kategori->default_sla_jam * 1.5) }} jam</td>
                                    <td>
                                        <a href="{{ route('admin.master-data.kategori.edit', $kategori) }}" class="sigap-btn sigap-btn--sm sigap-btn--secondary">
                                            Ubah SLA
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-slate-400 text-center py-6">Belum ada kategori di departemen ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <p class="text-sm text-slate-400">Belum ada departemen terdaftar.</p>
        @endforelse
    </div>
</section>
@endsection
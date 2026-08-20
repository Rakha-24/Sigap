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
            <h2>Ringkasan SLA per Departemen</h2>
            <p class="sigap-page__subtitle">
                SLA final tiket = Default SLA kategori × faktor prioritas (Tinggi ×0.5, Sedang ×1.0, Rendah ×1.5).
            </p>
        </div>

        @foreach($departemens as $dept)
            <div class="sigap-sla-group" id="sigap-admin-master-data-sla__dept-{{ $dept->id }}">
                <h3 class="sigap-sla-group__title">{{ $dept->nama }}</h3>
                <table class="sigap-table">
                    <thead>
                        <tr><th>Kategori</th><th>Default SLA (Sedang)</th><th>SLA Tinggi</th><th>SLA Rendah</th><th>Aksi</th></tr>
                    </thead>
                    <tbody>
                        @forelse($dept->kategoris as $kategori)
                            <tr>
                                <td>{{ $kategori->nama }}</td>
                                <td>{{ $kategori->default_sla_jam }} jam</td>
                                <td>{{ round($kategori->default_sla_jam * 0.5) }} jam</td>
                                <td>{{ round($kategori->default_sla_jam * 1.5) }} jam</td>
                                <td>
                                    <a href="{{ route('admin.master-data.kategori.edit', $kategori) }}">Ubah SLA</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5">Belum ada kategori di departemen ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>
</section>
@endsection
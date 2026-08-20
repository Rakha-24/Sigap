@extends('layouts.app')
@section('title', 'Kategori Masalah')

@section('content')
<section id="sigap-admin-master-data-kategori" class="sigap-page">
    <div class="sigap-page__header">
        <div>
            <h1 class="sigap-page__title">Manajemen Master Data</h1>
            <p class="sigap-page__subtitle">Kelola data referensi utama sistem.</p>
        </div>
        <a href="{{ route('admin.master-data.kategori.create') }}" class="sigap-btn sigap-btn--primary" id="sigap-admin-master-data-kategori__add">
            + Tambah Data
        </a>
    </div>

    @if (session('success'))
        <div class="sigap-alert sigap-alert--success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="sigap-alert sigap-alert--error">{{ session('error') }}</div>
    @endif

    @include('admin.master-data._tabs')

    <div class="sigap-card" id="sigap-admin-master-data-kategori__list">
        <div class="sigap-card__header">
            <h2>Daftar Kategori Masalah</h2>
            <form method="GET" action="{{ route('admin.master-data.kategori.index') }}" class="sigap-toolbar">
                <input type="text" name="cari" class="sigap-form__input" placeholder="Cari kategori..."
                       value="{{ request('cari') }}">
                <select name="departemen_id" class="sigap-form__select" onchange="this.form.submit()">
                    <option value="">Semua Departemen</option>
                    @foreach($departemens as $dept)
                        <option value="{{ $dept->id }}" {{ request('departemen_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->nama }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        <table class="sigap-table">
            <thead>
                <tr><th>Nama Kategori</th><th>Departemen</th><th>Default SLA</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @foreach($kategoris as $kategori)
                    <tr>
                        <td>{{ $kategori->nama }}</td>
                        <td>{{ $kategori->departemen->nama }}</td>
                        <td>{{ $kategori->default_sla_jam }} jam</td>
                        <td class="sigap-table__actions">
                            <a href="{{ route('admin.master-data.kategori.edit', $kategori) }}" title="Edit">✏️</a>
                            <form method="POST" action="{{ route('admin.master-data.kategori.destroy', $kategori) }}"
                                  style="display:inline" onsubmit="return confirm('Hapus kategori {{ $kategori->nama }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Hapus">🗑️</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $kategoris->links() }}
    </div>
</section>
@endsection
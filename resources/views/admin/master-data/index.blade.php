@extends('layouts.app')
@section('title', 'Manajemen Master Data')

@section('content')
<section id="sigap-admin-master-data" class="sigap-page">
    <div class="sigap-page__header">
        <div>
            <h1 class="sigap-page__title">Manajemen Master Data</h1>
            <p class="sigap-page__subtitle">Kelola data referensi utama sistem.</p>
        </div>
        <a href="{{ route('admin.master-data.departemen.create') }}" class="sigap-btn sigap-btn--primary" id="sigap-admin-master-data__add">
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

    <div class="sigap-card" id="sigap-admin-master-data__departemen">
        <div class="sigap-card__header">
            <h2>Daftar Departemen</h2>
            <form method="GET" action="{{ route('admin.master-data.departemen.index') }}">
                <input type="text" name="cari" class="sigap-form__input" placeholder="Cari departemen..."
                       value="{{ request('cari') }}">
            </form>
        </div>

        <table class="sigap-table">
            <thead>
                <tr><th>ID</th><th>Nama Departemen</th><th>Jumlah Kategori</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @foreach($departemens as $dept)
                    <tr>
                        <td>{{ $dept->kode }}</td>
                        <td>{{ $dept->nama }}</td>
                        <td>{{ $dept->kategoris_count }}</td>
                        <td>
                            <span class="sigap-badge sigap-badge--{{ $dept->is_active ? 'active' : 'inactive' }}">
                                {{ $dept->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="sigap-table__actions">
                            <a href="{{ route('admin.master-data.departemen.edit', $dept) }}" title="Edit">✏️</a>
                            <form method="POST" action="{{ route('admin.master-data.departemen.destroy', $dept) }}"
                                  style="display:inline" onsubmit="return confirm('Hapus departemen {{ $dept->nama }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Hapus">🗑️</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $departemens->links() }}
    </div>
</section>
@endsection
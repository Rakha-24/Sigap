@extends('layouts.app')
@section('title', 'Tambah Departemen')

@section('content')
<section id="sigap-admin-master-data-create" class="sigap-page">
    <div class="sigap-page__header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.master-data.departemen.index') }}" class="sigap-icon-btn" title="Kembali">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"/>
                    <polyline points="12 19 5 12 12 5"/>
                </svg>
            </a>
            <div>
                <h1 class="sigap-page__title">Tambah Departemen</h1>
                <p class="sigap-page__subtitle">Tambahkan unit atau bagian baru penerima laporan.</p>
            </div>
        </div>
    </div>

    <div class="sigap-card max-w-2xl">
        <form method="POST" action="{{ route('admin.master-data.departemen.store') }}" class="sigap-form max-w-none" id="sigap-admin-master-data-create__form">
            @csrf

            <div class="sigap-form__group">
                <label for="nama" class="sigap-form__label">Nama Departemen</label>
                <input type="text" name="nama" id="nama" class="sigap-form__input"
                       placeholder="Contoh: IT Support" value="{{ old('nama') }}" required>
                @error('nama') <span class="sigap-form__error">{{ $message }}</span> @enderror
            </div>

            <div class="sigap-form__group">
                <label for="deskripsi" class="sigap-form__label">Deskripsi (Opsional)</label>
                <textarea name="deskripsi" id="deskripsi" class="sigap-form__textarea" rows="3">{{ old('deskripsi') }}</textarea>
                @error('deskripsi') <span class="sigap-form__error">{{ $message }}</span> @enderror
            </div>

            <div class="flex gap-3 flex-wrap">
                <button type="submit" class="sigap-form__submit sm:!w-auto sm:px-8">Simpan Departemen</button>
                <a href="{{ route('admin.master-data.departemen.index') }}" class="sigap-btn sigap-btn--secondary">Batal</a>
            </div>
        </form>
    </div>
</section>
@endsection
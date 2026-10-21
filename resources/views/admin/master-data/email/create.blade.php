@extends('layouts.app')
@section('title', 'Tambah Email Terdaftar')

@section('content')
<section id="sigap-admin-master-data-email-create" class="sigap-page">
    <div class="sigap-page__header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.master-data.email.index') }}" class="sigap-icon-btn" title="Kembali">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"/>
                    <polyline points="12 19 5 12 12 5"/>
                </svg>
            </a>
            <div>
                <h1 class="sigap-page__title">Tambah Email Terdaftar</h1>
                <p class="sigap-page__subtitle">Email di sini boleh membuat laporan sebagai guest tanpa perlu mendaftar akun.</p>
            </div>
        </div>
    </div>

    <div class="sigap-card max-w-2xl">
        <form method="POST" action="{{ route('admin.master-data.email.store') }}" class="sigap-form max-w-none" id="sigap-admin-master-data-email-create__form">
            @csrf

            <div class="sigap-form__group">
                <label for="email" class="sigap-form__label">Alamat Email</label>
                <input type="email" name="email" id="email" class="sigap-form__input"
                       placeholder="karyawan@perusahaan.com" value="{{ old('email') }}" required>
                @error('email') <span role="alert" class="sigap-form__error">{{ $message }}</span> @enderror
            </div>

            <div class="sigap-form__group">
                <label for="nama" class="sigap-form__label">Nama Karyawan (opsional)</label>
                <input type="text" name="nama" id="nama" class="sigap-form__input"
                       placeholder="Nama pemilik email" value="{{ old('nama') }}">
                <p class="sigap-form__hint">Hanya keterangan internal, tidak dikirim ke pelapor.</p>
                @error('nama') <span role="alert" class="sigap-form__error">{{ $message }}</span> @enderror
            </div>

            <div class="sigap-form__group">
                <label class="sigap-form__checkbox-row">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    <span>Email aktif (boleh melapor)</span>
                </label>
            </div>

            <div class="flex gap-3 flex-wrap">
                <button type="submit" class="sigap-form__submit sm:!w-auto sm:px-8">Simpan Email</button>
                <a href="{{ route('admin.master-data.email.index') }}" class="sigap-btn sigap-btn--secondary">Batal</a>
            </div>
        </form>
    </div>
</section>
@endsection

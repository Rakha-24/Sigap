@extends('layouts.guest')
@section('title', 'Buat Laporan - SIGAP')

@section('content')
<section id="sigap-guest-ticket-form" class="sigap-form-page">
    <h1 class="sigap-form-page__title">Buat Laporan Baru</h1>

    <form method="POST" action="{{ route('guest.ticket.store') }}" enctype="multipart/form-data"
          class="sigap-form" id="sigap-guest-ticket-form__form">
        @csrf

        {{-- Honeypot anti-bot --}}
        <div style="position:absolute; left:-9999px;" aria-hidden="true">
            <input type="text" name="website" tabindex="-1" autocomplete="off">
        </div>

        <div class="sigap-form__group">
            <label for="nama_guest" class="sigap-form__label">Nama Lengkap</label>
            <input type="text" name="nama_guest" id="nama_guest" class="sigap-form__input"
                   value="{{ old('nama_guest') }}" required>
            @error('nama_guest') <span class="sigap-form__error">{{ $message }}</span> @enderror
        </div>

        <div class="sigap-form__group">
            <label for="kontak_guest" class="sigap-form__label">Kontak (Email/No. HP)</label>
            <input type="text" name="kontak_guest" id="kontak_guest" class="sigap-form__input"
                   value="{{ old('kontak_guest') }}" required>
            @error('kontak_guest') <span class="sigap-form__error">{{ $message }}</span> @enderror
        </div>

        <div class="sigap-form__group">
            <label for="departemen_id" class="sigap-form__label">Departemen Tujuan</label>
            <select name="departemen_id" id="departemen_id" class="sigap-form__select" required>
                <option value="">Pilih Departemen</option>
                @foreach($departemens as $dept)
                    <option value="{{ $dept->id }}" {{ old('departemen_id') == $dept->id ? 'selected' : '' }}>
                        {{ $dept->nama }}
                    </option>
                @endforeach
            </select>
            @error('departemen_id') <span class="sigap-form__error">{{ $message }}</span> @enderror
        </div>

        <div class="sigap-form__group">
            <label for="kategori_id" class="sigap-form__label">Kategori Masalah</label>
            <select name="kategori_id" id="kategori_id" class="sigap-form__select" required>
                <option value="">Pilih Kategori</option>
                {{-- Diisi dinamis via JS (fetch berdasarkan departemen_id) atau server-side jika tanpa JS --}}
            </select>
            @error('kategori_id') <span class="sigap-form__error">{{ $message }}</span> @enderror
        </div>

        <div class="sigap-form__group">
            <label for="judul" class="sigap-form__label">Judul Tiket</label>
            <input type="text" name="judul" id="judul" class="sigap-form__input"
                   value="{{ old('judul') }}" required>
        </div>

        <div class="sigap-form__group">
            <label for="deskripsi" class="sigap-form__label">Deskripsi Lengkap</label>
            <textarea name="deskripsi" id="deskripsi" class="sigap-form__textarea" rows="5" required>{{ old('deskripsi') }}</textarea>
        </div>

        <div class="sigap-form__group" id="sigap-guest-ticket-form__priority">
            <span class="sigap-form__label">Tingkat Prioritas</span>
            <div class="sigap-priority-picker">
                @foreach(['rendah' => 'Rendah', 'sedang' => 'Sedang', 'tinggi' => 'Tinggi'] as $val => $label)
                    <label class="sigap-priority-picker__option">
                        <input type="radio" name="prioritas" value="{{ $val }}"
                               {{ old('prioritas', 'sedang') === $val ? 'checked' : '' }}>
                        {{ $label }}
                    </label>
                @endforeach
            </div>
        </div>

        <div class="sigap-form__group">
            <label for="evidence" class="sigap-form__label">Lampiran (Opsional, maks 2MB, JPG/PNG/PDF)</label>
            <input type="file" name="evidence" id="evidence" class="sigap-form__file">
            @error('evidence') <span class="sigap-form__error">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="sigap-form__submit">Kirim Laporan</button>
    </form>
</section>
@endsection
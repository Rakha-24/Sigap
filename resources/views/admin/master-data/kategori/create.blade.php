@extends('layouts.app')
@section('title', 'Tambah Kategori')

@section('content')
<section id="sigap-admin-master-data-kategori-create" class="sigap-page">
    <h1 class="sigap-page__title">Tambah Kategori Masalah</h1>

    <form method="POST" action="{{ route('admin.master-data.kategori.store') }}" class="sigap-form" id="sigap-admin-master-data-kategori-create__form">
        @csrf

        <div class="sigap-form__group">
            <label for="departemen_id" class="sigap-form__label">Departemen</label>
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
            <label for="nama" class="sigap-form__label">Nama Kategori</label>
            <input type="text" name="nama" id="nama" class="sigap-form__input"
                   placeholder="Contoh: Jaringan & Internet" value="{{ old('nama') }}" required>
            @error('nama') <span class="sigap-form__error">{{ $message }}</span> @enderror
        </div>

        <div class="sigap-form__group">
            <label for="default_sla_jam" class="sigap-form__label">Default SLA (jam)</label>
            <input type="number" name="default_sla_jam" id="default_sla_jam" class="sigap-form__input"
                   min="1" max="720" value="{{ old('default_sla_jam', 24) }}" required>
            <p class="sigap-form__hint">Target waktu penyelesaian standar untuk kategori ini, dalam jam.</p>
            @error('default_sla_jam') <span class="sigap-form__error">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="sigap-form__submit">Simpan Kategori</button>
    </form>
</section>
@endsection
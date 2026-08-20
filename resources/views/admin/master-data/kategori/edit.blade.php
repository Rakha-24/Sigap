@extends('layouts.app')
@section('title', 'Edit Kategori')

@section('content')
<section id="sigap-admin-master-data-kategori-edit" class="sigap-page">
    <div class="sigap-page__header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.master-data.kategori.index') }}" class="sigap-icon-btn" title="Kembali">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"/>
                    <polyline points="12 19 5 12 12 5"/>
                </svg>
            </a>
            <div>
                <h1 class="sigap-page__title">Edit Kategori: {{ $kategori->nama }}</h1>
                <p class="sigap-page__subtitle">Perbarui informasi kategori dan target SLA.</p>
            </div>
        </div>
    </div>

    <div class="sigap-card max-w-2xl">
        <form method="POST" action="{{ route('admin.master-data.kategori.update', $kategori) }}" class="sigap-form max-w-none" id="sigap-admin-master-data-kategori-edit__form">
            @csrf
            @method('PUT')

            <div class="grid md:grid-cols-2 gap-5">
                <div class="sigap-form__group">
                    <label for="departemen_id" class="sigap-form__label">Departemen</label>
                    <select name="departemen_id" id="departemen_id" class="sigap-form__select" required>
                        @foreach($departemens as $dept)
                            <option value="{{ $dept->id }}" {{ old('departemen_id', $kategori->departemen_id) == $dept->id ? 'selected' : '' }}>
                                {{ $dept->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('departemen_id') <span class="sigap-form__error">{{ $message }}</span> @enderror
                </div>

                <div class="sigap-form__group">
                    <label for="nama" class="sigap-form__label">Nama Kategori</label>
                    <input type="text" name="nama" id="nama" class="sigap-form__input"
                           value="{{ old('nama', $kategori->nama) }}" required>
                    @error('nama') <span class="sigap-form__error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="sigap-form__group">
                <label for="default_sla_jam" class="sigap-form__label">Default SLA (jam)</label>
                <input type="number" name="default_sla_jam" id="default_sla_jam" class="sigap-form__input"
                       min="1" max="720" value="{{ old('default_sla_jam', $kategori->default_sla_jam) }}" required>
                @error('default_sla_jam') <span class="sigap-form__error">{{ $message }}</span> @enderror
            </div>

            <div class="flex gap-3 flex-wrap">
                <button type="submit" class="sigap-form__submit sm:!w-auto sm:px-8">Perbarui Kategori</button>
                <a href="{{ route('admin.master-data.kategori.index') }}" class="sigap-btn sigap-btn--secondary">Batal</a>
            </div>
        </form>
    </div>
</section>
@endsection
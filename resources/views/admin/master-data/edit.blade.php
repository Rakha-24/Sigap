@extends('layouts.app')
@section('title', 'Edit Departemen')

@section('content')
<section id="sigap-admin-master-data-edit" class="sigap-page">
    <h1 class="sigap-page__title">Edit Departemen: {{ $departemen->nama }}</h1>

    <form method="POST" action="{{ route('admin.master-data.departemen.update', $departemen) }}" class="sigap-form" id="sigap-admin-master-data-edit__form">
        @csrf
        @method('PUT')

        <div class="sigap-form__group">
            <label for="nama" class="sigap-form__label">Nama Departemen</label>
            <input type="text" name="nama" id="nama" class="sigap-form__input"
                   value="{{ old('nama', $departemen->nama) }}" required>
            @error('nama') <span class="sigap-form__error">{{ $message }}</span> @enderror
        </div>

        <div class="sigap-form__group">
            <label for="deskripsi" class="sigap-form__label">Deskripsi (Opsional)</label>
            <textarea name="deskripsi" id="deskripsi" class="sigap-form__textarea" rows="3">{{ old('deskripsi', $departemen->deskripsi) }}</textarea>
            @error('deskripsi') <span class="sigap-form__error">{{ $message }}</span> @enderror
        </div>

        <label class="sigap-form__checkbox-row">
            <input type="checkbox" name="is_active" value="1" {{ $departemen->is_active ? 'checked' : '' }}>
            Departemen Aktif
        </label>

        <button type="submit" class="sigap-form__submit">Perbarui Departemen</button>
    </form>
</section>
@endsection
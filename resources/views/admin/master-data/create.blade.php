@extends('layouts.app')
@section('title', 'Tambah Departemen')

@section('content')
<section id="sigap-admin-master-data-create" class="sigap-page">
    <h1 class="sigap-page__title">Tambah Departemen</h1>

    <form method="POST" action="{{ route('admin.master-data.departemen.store') }}" class="sigap-form" id="sigap-admin-master-data-create__form">
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

        <button type="submit" class="sigap-form__submit">Simpan Departemen</button>
    </form>
</section>
@endsection
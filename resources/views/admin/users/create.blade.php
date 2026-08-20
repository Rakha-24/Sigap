@extends('layouts.app')
@section('title', 'Tambah Pengguna')

@section('content')
<section id="sigap-admin-users-create" class="sigap-page">
    <h1 class="sigap-page__title">Tambah Pengguna Baru</h1>

    <form method="POST" action="{{ route('admin.users.store') }}" id="sigap-admin-users-create__form" class="sigap-form">
        @csrf

        <div class="sigap-form__group">
            <label for="name" class="sigap-form__label">Nama Lengkap</label>
            <input type="text" name="name" id="name" class="sigap-form__input" value="{{ old('name') }}" required>
            @error('name') <span class="sigap-form__error">{{ $message }}</span> @enderror
        </div>

        <div class="sigap-form__group">
            <label for="email" class="sigap-form__label">Email</label>
            <input type="email" name="email" id="email" class="sigap-form__input" value="{{ old('email') }}" required>
            @error('email') <span class="sigap-form__error">{{ $message }}</span> @enderror
        </div>

        <div class="sigap-form__group">
            <label for="password" class="sigap-form__label">Kata Sandi</label>
            <input type="password" name="password" id="password" class="sigap-form__input" required>
            @error('password') <span class="sigap-form__error">{{ $message }}</span> @enderror
        </div>

        <div class="sigap-form__group">
            <label for="password_confirmation" class="sigap-form__label">Konfirmasi Kata Sandi</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="sigap-form__input" required>
        </div>

        <div class="sigap-form__group">
            <label for="role" class="sigap-form__label">Role</label>
            <select name="role" id="role" class="sigap-form__select" required
                    onchange="document.getElementById('sigap-admin-users-create__departemen').style.display = this.value === 'agent' ? 'block' : 'none'">
                <option value="">Pilih Role</option>
                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="agent" {{ old('role') === 'agent' ? 'selected' : '' }}>Agent</option>
                <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>User</option>
            </select>
            @error('role') <span class="sigap-form__error">{{ $message }}</span> @enderror
        </div>

        <div class="sigap-form__group" id="sigap-admin-users-create__departemen"
             style="display: {{ old('role') === 'agent' ? 'block' : 'none' }}">
            <label for="departemen_id" class="sigap-form__label">Departemen (khusus Agent)</label>
            <select name="departemen_id" id="departemen_id" class="sigap-form__select">
                <option value="">Pilih Departemen</option>
                @foreach($departemens as $dept)
                    <option value="{{ $dept->id }}" {{ old('departemen_id') == $dept->id ? 'selected' : '' }}>
                        {{ $dept->nama }}
                    </option>
                @endforeach
            </select>
            @error('departemen_id') <span class="sigap-form__error">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="sigap-form__submit">Simpan Pengguna</button>
    </form>
</section>
@endsection
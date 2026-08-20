@extends('layouts.app')
@section('title', 'Edit Pengguna')

@section('content')
<section id="sigap-admin-users-edit" class="sigap-page">
    <h1 class="sigap-page__title">Edit Pengguna: {{ $user->name }}</h1>

    <form method="POST" action="{{ route('admin.users.update', $user) }}" id="sigap-admin-users-edit__form" class="sigap-form">
        @csrf
        @method('PUT')

        <div class="sigap-form__group">
            <label for="name" class="sigap-form__label">Nama Lengkap</label>
            <input type="text" name="name" id="name" class="sigap-form__input" value="{{ old('name', $user->name) }}" required>
            @error('name') <span class="sigap-form__error">{{ $message }}</span> @enderror
        </div>

        <div class="sigap-form__group">
            <label for="email" class="sigap-form__label">Email</label>
            <input type="email" name="email" id="email" class="sigap-form__input" value="{{ old('email', $user->email) }}" required>
            @error('email') <span class="sigap-form__error">{{ $message }}</span> @enderror
        </div>

        <div class="sigap-form__group">
            <label for="password" class="sigap-form__label">Kata Sandi Baru (opsional)</label>
            <input type="password" name="password" id="password" class="sigap-form__input" placeholder="Kosongkan jika tidak diubah">
            @error('password') <span class="sigap-form__error">{{ $message }}</span> @enderror
        </div>

        <div class="sigap-form__group">
            <label for="password_confirmation" class="sigap-form__label">Konfirmasi Kata Sandi Baru</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="sigap-form__input">
        </div>

        <div class="sigap-form__group">
            <label for="role" class="sigap-form__label">Role</label>
            <select name="role" id="role" class="sigap-form__select" required
                    onchange="document.getElementById('sigap-admin-users-edit__departemen').style.display = this.value === 'agent' ? 'block' : 'none'">
                @foreach(['admin' => 'Admin', 'agent' => 'Agent', 'user' => 'User'] as $val => $label)
                    <option value="{{ $val }}" {{ old('role', $user->role) === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            @error('role') <span class="sigap-form__error">{{ $message }}</span> @enderror
        </div>

        <div class="sigap-form__group" id="sigap-admin-users-edit__departemen"
             style="display: {{ old('role', $user->role) === 'agent' ? 'block' : 'none' }}">
            <label for="departemen_id" class="sigap-form__label">Departemen (khusus Agent)</label>
            <select name="departemen_id" id="departemen_id" class="sigap-form__select">
                <option value="">Pilih Departemen</option>
                @foreach($departemens as $dept)
                    <option value="{{ $dept->id }}" {{ old('departemen_id', $user->departemen_id) == $dept->id ? 'selected' : '' }}>
                        {{ $dept->nama }}
                    </option>
                @endforeach
            </select>
            @error('departemen_id') <span class="sigap-form__error">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="sigap-form__submit">Perbarui Pengguna</button>
    </form>
</section>
@endsection
@extends('layouts.auth')
@section('title', 'Daftar - SIGAP')

@section('content')
<div id="sigap-register" class="sigap-auth-form">
    <h2 class="sigap-auth-form__title">Buat Akun Baru</h2>
    <p class="sigap-auth-form__subtitle">Lengkapi data diri Anda untuk memulai menggunakan SIGAP.</p>

    <form method="POST" action="{{ route('register') }}" id="sigap-register__form" class="sigap-form">
        @csrf

        <div class="sigap-form__group">
            <label for="name" class="sigap-form__label">Nama Lengkap</label>
            <input type="text" name="name" id="name" class="sigap-form__input"
                   placeholder="Masukkan nama lengkap" value="{{ old('name') }}" required autofocus>
            @error('name') <span class="sigap-form__error">{{ $message }}</span> @enderror
        </div>

        <div class="sigap-form__group">
            <label for="email" class="sigap-form__label">Alamat Email</label>
            <input type="email" name="email" id="email" class="sigap-form__input"
                   placeholder="contoh@instansi.go.id" value="{{ old('email') }}" required>
            @error('email') <span class="sigap-form__error">{{ $message }}</span> @enderror
        </div>

        <div class="sigap-form__group">
            <label for="password" class="sigap-form__label">Kata Sandi</label>
            <input type="password" name="password" id="password" class="sigap-form__input"
                   placeholder="Minimal 8 karakter" required>
            @error('password') <span class="sigap-form__error">{{ $message }}</span> @enderror
        </div>

        <div class="sigap-form__group">
            <label for="password_confirmation" class="sigap-form__label">Konfirmasi Kata Sandi</label>
            <input type="password" name="password_confirmation" id="password_confirmation"
                   class="sigap-form__input" placeholder="Ulangi kata sandi" required>
        </div>

        <label class="sigap-form__checkbox-row">
            <input type="checkbox" required> Saya setuju dengan Syarat & Ketentuan serta Kebijakan Privasi
        </label>

        <button type="submit" class="sigap-form__submit" id="sigap-register__submit">Daftar</button>

        <p class="sigap-auth-form__footer">
            Sudah punya akun? <a href="{{ route('login') }}">Masuk</a>
        </p>
    </form>
</div>
@endsection
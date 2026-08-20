@extends('layouts.auth')
@section('title', 'Login - SIGAP')

@section('content')
<div id="sigap-login" class="sigap-auth-form">
    <h2 class="sigap-auth-form__title">Selamat Datang Kembali</h2>
    <p class="sigap-auth-form__subtitle">Silakan masuk ke akun Anda untuk melanjutkan.</p>

    @if (session('status'))
        <div class="sigap-alert sigap-alert--success" id="sigap-login__status">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}" id="sigap-login__form" class="sigap-form">
        @csrf

        <div class="sigap-form__group">
            <label for="email" class="sigap-form__label">Alamat Email</label>
            <input type="email" name="email" id="email" class="sigap-form__input"
                   placeholder="nama@instansi.go.id" value="{{ old('email') }}" required autofocus>
            @error('email') <span class="sigap-form__error">{{ $message }}</span> @enderror
        </div>

        <div class="sigap-form__group">
            <div class="sigap-form__label-row">
                <label for="password" class="sigap-form__label">Kata Sandi</label>
                <a href="{{ route('password.request') }}" id="sigap-login__forgot-link" class="sigap-form__link">
                    Lupa sandi?
                </a>
            </div>
            <input type="password" name="password" id="password" class="sigap-form__input" required>
            @error('password') <span class="sigap-form__error">{{ $message }}</span> @enderror
        </div>

        <div class="sigap-form__checkbox-row">
            <label>
                <input type="checkbox" name="remember"> Ingat saya
            </label>
        </div>

        <button type="submit" class="sigap-form__submit" id="sigap-login__submit">Masuk</button>

        <p class="sigap-auth-form__footer">
            Belum punya akun? <a href="{{ route('register') }}">Daftar</a>
        </p>
    </form>
</div>
@endsection
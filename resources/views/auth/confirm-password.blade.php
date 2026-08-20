@extends('layouts.auth')
@section('title', 'Konfirmasi Sandi - SIGAP')

@section('content')
<div id="sigap-confirm-password" class="sigap-auth-form">
    <h2 class="sigap-auth-form__title">Konfirmasi Kata Sandi</h2>
    <p class="sigap-auth-form__subtitle">
        Ini adalah area aman aplikasi. Konfirmasikan kata sandi Anda sebelum melanjutkan.
    </p>

    <form method="POST" action="{{ route('password.confirm') }}" id="sigap-confirm-password__form" class="sigap-form">
        @csrf

        <div class="sigap-form__group">
            <label for="password" class="sigap-form__label">Kata Sandi</label>
            <input type="password" name="password" id="password" class="sigap-form__input"
                   required autocomplete="current-password">
            @error('password') <span class="sigap-form__error">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="sigap-form__submit">Konfirmasi</button>
    </form>
</div>
@endsection
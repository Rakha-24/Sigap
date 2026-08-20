@extends('layouts.auth')
@section('title', 'Reset Sandi - SIGAP')

@section('content')
<div id="sigap-reset-password" class="sigap-auth-form">
    <h2 class="sigap-auth-form__title">Atur Ulang Kata Sandi</h2>
    <p class="sigap-auth-form__subtitle">Buat kata sandi baru untuk akun Anda.</p>

    <form method="POST" action="{{ route('password.store') }}" id="sigap-reset-password__form" class="sigap-form">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="sigap-form__group">
            <label for="email" class="sigap-form__label">Alamat Email</label>
            <input type="email" name="email" id="email" class="sigap-form__input"
                   value="{{ old('email', $request->email) }}" required autofocus>
            @error('email') <span class="sigap-form__error">{{ $message }}</span> @enderror
        </div>

        <div class="sigap-form__group">
            <label for="password" class="sigap-form__label">Kata Sandi Baru</label>
            <input type="password" name="password" id="password" class="sigap-form__input" required>
            @error('password') <span class="sigap-form__error">{{ $message }}</span> @enderror
        </div>

        <div class="sigap-form__group">
            <label for="password_confirmation" class="sigap-form__label">Konfirmasi Kata Sandi Baru</label>
            <input type="password" name="password_confirmation" id="password_confirmation"
                   class="sigap-form__input" required>
        </div>

        <button type="submit" class="sigap-form__submit" id="sigap-reset-password__submit">
            Reset Kata Sandi
        </button>
    </form>
</div>
@endsection
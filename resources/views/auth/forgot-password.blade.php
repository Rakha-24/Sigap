@extends('layouts.auth')
@section('title', 'Lupa Sandi - SIGAP')

@section('content')
<div id="sigap-forgot-password" class="sigap-auth-form">
    <h2 class="sigap-auth-form__title">Lupa Kata Sandi?</h2>
    <p class="sigap-auth-form__subtitle">
        Masukkan alamat email Anda. Kami akan mengirimkan tautan untuk mereset kata sandi.
    </p>

    @if (session('status'))
        <div class="sigap-alert sigap-alert--success w-full" id="sigap-forgot-password__status">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" id="sigap-forgot-password__form" class="sigap-form">
        @csrf

        <div class="sigap-form__group">
            <label for="email" class="sigap-form__label">Alamat Email</label>
            <input type="email" name="email" id="email" class="sigap-form__input"
                   placeholder="nama@instansi.go.id" value="{{ old('email') }}" required autofocus>
            @error('email') <span class="sigap-form__error">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="sigap-form__submit" id="sigap-forgot-password__submit">
            Kirim Tautan Reset
        </button>

        <p class="sigap-auth-form__footer">
            Ingat kata sandi Anda? <a href="{{ route('login') }}">Kembali ke Login</a>
        </p>
    </form>
</div>
@endsection
@extends('layouts.auth')
@section('title', 'Login - SIGAP')

@section('content')
<div id="sigap-login" class="sigap-auth-form" x-data="{ showPassword: false }">
    <h2 class="sigap-auth-form__title">Selamat Datang Kembali</h2>
    <p class="sigap-auth-form__subtitle">Silakan masuk ke akun Anda untuk melanjutkan.</p>

    @if (session('status'))
        <div class="sigap-alert sigap-alert--success w-full" id="sigap-login__status">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}" id="sigap-login__form" class="sigap-form">
        @csrf

        <div class="sigap-form__group">
            <label for="email" class="sigap-form__label">Alamat Email</label>
            <input type="email" name="email" id="email" class="sigap-form__input"
                   placeholder="nama@instansi.go.id" value="{{ old('email') }}" required autofocus>
            @error('email') <span role="alert" class="sigap-form__error">{{ $message }}</span> @enderror
        </div>

        <div class="sigap-form__group">
            <div class="sigap-form__label-row">
                <label for="password" class="sigap-form__label">Kata Sandi</label>
                <a href="{{ route('password.request') }}" id="sigap-login__forgot-link" class="sigap-form__link">
                    Lupa sandi?
                </a>
            </div>
            <div class="sigap-form__password-wrap">
                <input :type="showPassword ? 'text' : 'password'" name="password" id="password"
                       class="sigap-form__input pr-11" required>
                <button type="button" class="sigap-form__password-toggle" @click="showPassword = !showPassword"
                        :aria-label="showPassword ? 'Sembunyikan sandi' : 'Tampilkan sandi'">
                    <svg x-show="!showPassword" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    <svg x-show="showPassword" x-cloak width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                        <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                        <line x1="1" y1="1" x2="23" y2="23"/>
                    </svg>
                </button>
            </div>
            @error('password') <span role="alert" class="sigap-form__error">{{ $message }}</span> @enderror
        </div>

        <label class="sigap-form__checkbox-row">
            <input type="checkbox" name="remember"> Ingat saya
        </label>

        <button type="submit" class="sigap-form__submit" id="sigap-login__submit">Masuk</button>

        <p class="sigap-auth-form__footer">
            Belum punya akun? <a href="{{ route('register') }}">Daftar</a>
        </p>
    </form>

    <div id="sigap-login__demo" class="sigap-demo">
        <div class="sigap-form__divider" aria-hidden="true">atau</div>
        <p class="sigap-demo__hint">Masuk cepat sebagai akun demo</p>

        <div class="sigap-demo__grid">
            @php
                $demoRoles = [
                    'admin' => [
                        'Admin',
                        '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/></svg>',
                    ],
                    'agent' => [
                        'Agent',
                        '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 14v-3a9 9 0 0 1 18 0v3"/><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3z"/><path d="M3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"/></svg>',
                    ],
                    'user' => [
                        'Pengguna',
                        '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
                    ],
                ];
            @endphp

            @foreach ($demoRoles as $role => [$label, $icon])
                <form method="POST" action="{{ route('login.demo') }}" class="sigap-demo__form">
                    @csrf
                    <input type="hidden" name="role" value="{{ $role }}">
                    <button type="submit" class="sigap-demo__btn" aria-label="Masuk sebagai akun demo {{ $label }}">
                        <span class="sigap-demo__icon">{!! $icon !!}</span>
                        <span class="sigap-demo__label">{{ $label }}</span>
                    </button>
                </form>
            @endforeach
        </div>
    </div>
</div>
@endsection
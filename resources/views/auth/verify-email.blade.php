@extends('layouts.auth')
@section('title', 'Verifikasi Email - SIGAP')

@section('content')
<div id="sigap-verify-email" class="sigap-auth-form">
    <h2 class="sigap-auth-form__title">Verifikasi Email Anda</h2>
    <p class="sigap-auth-form__subtitle">
        Terima kasih telah mendaftar! Sebelum memulai, mohon verifikasi alamat email Anda
        dengan mengklik tautan yang kami kirimkan.
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="sigap-alert sigap-alert--success w-full" id="sigap-verify-email__status">
            Tautan verifikasi baru telah dikirim ke email Anda.
        </div>
    @endif

    <div class="flex flex-col gap-3 w-full">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="sigap-form__submit">Kirim Ulang Email Verifikasi</button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sigap-btn sigap-btn--secondary w-full">Keluar</button>
        </form>
    </div>
</div>
@endsection
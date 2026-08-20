@extends('layouts.guest')
@section('title', 'Lacak Tiket - SIGAP')

@section('content')
<section id="sigap-guest-track" class="sigap-track-page">
    <h1 class="sigap-track-page__title">Lacak Status Tiket Anda</h1>
    <p class="sigap-track-page__subtitle">Masukkan nomor tiket yang Anda terima saat membuat laporan.</p>

    <div class="sigap-track-page__card">
        <form method="GET" action="{{ route('guest.track.form') }}" class="sigap-track-page__form">
            <input type="text" name="nomor_tiket" class="sigap-form__input"
                   placeholder="Contoh: TKT-20260812-A7X9" required>
            <button type="submit" class="sigap-form__submit">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                Lacak
            </button>
        </form>
    </div>
</section>
@endsection
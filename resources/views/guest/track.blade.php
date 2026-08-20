@extends('layouts.guest')
@section('title', 'Lacak Tiket - SIGAP')

@section('content')
<section id="sigap-guest-track" class="sigap-track-page">
    <h1 class="sigap-track-page__title">Lacak Status Tiket Anda</h1>
    <form method="GET" action="{{ route('guest.track.form') }}" class="sigap-track-page__form">
        <input type="text" name="nomor_tiket" class="sigap-form__input"
               placeholder="Contoh: TKT-20260812-A7X9" required>
        <button type="submit" class="sigap-form__submit">Lacak</button>
    </form>
</section>
@endsection
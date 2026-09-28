@extends('layouts.guest')
@section('title', 'Syarat & Ketentuan - SIGAP')

@section('content')
<section id="sigap-terms" class="sigap-form-page">
    <div class="sigap-form-page__card">
        <h1 class="sigap-form-page__title">Syarat & Ketentuan</h1>
        <p class="sigap-form-page__subtitle">Terakhir diperbarui: {{ \Carbon\Carbon::parse('2026-09-28')->translatedFormat('d F Y') }}</p>

        <div class="prose prose-sm max-w-none">
            <h2>1. Penggunaan Layanan</h2>
            <p>
                SIGAP adalah sistem helpdesk internal tempat pengguna melaporkan kendala
                dan memantau penanganannya. Dengan mendaftar, Anda menyatakan bahwa data
                yang Anda isi benar dan akan digunakan secara wajar sesuai keperluan penanganan laporan.
            </p>

            <h2>2. Akun &amp; Keamanan</h2>
            <p>
                Anda bertanggung jawab menjaga kerahasiaan kata sandi akun Anda. Segera hubungi
                administrator bila menemukan aktivitas mencurigakan pada akun Anda.
            </p>

            <h2>3. Data &amp; Laporan</h2>
            <p>
                Laporan yang Anda kirimkan, termasuk lampiran bukti, digunakan hanya untuk
                keperluan investigasi dan penyelesaian kendala oleh tim helpdesk yang berwenang.
            </p>

            <h2>4. Penyalahgunaan</h2>
            <p>
                Dilarang menggunakan sistem untuk konten yang melanggar hukum, memuat data
                pribadi pihak ketiga tanpa izin, maupun melakukan akses tidak sah.
            </p>

            <h2>5. Perubahan Ketentuan</h2>
            <p>
                Ketentuan ini dapat diperbarui sewaktu-waktu. Perubahan akan diumumkan melalui
                halaman ini.
            </p>
        </div>

        <div class="mt-6">
            <a href="{{ route('register') }}" class="sigap-btn sigap-btn--primary">Kembali ke Pendaftaran</a>
        </div>
    </div>
</section>
@endsection
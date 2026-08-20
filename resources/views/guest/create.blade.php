@extends('layouts.guest')
@section('title', 'Buat Laporan - SIGAP')

@section('content')
<section id="sigap-guest-ticket-form" class="sigap-form-page">
    <h1 class="sigap-form-page__title">Buat Laporan Baru</h1>
    <p class="text-sm text-slate-500 -mt-4 mb-6">Lengkapi formulir di bawah ini. Simpan nomor tiket untuk melacak perkembangan penanganan.</p>

    <div class="sigap-form-page__card">
        <form method="POST" action="{{ route('guest.ticket.store') }}" enctype="multipart/form-data"
              class="sigap-form max-w-none" id="sigap-guest-ticket-form__form">
            @csrf

            {{-- Honeypot anti-bot --}}
            <div style="position:absolute; left:-9999px;" aria-hidden="true">
                <input type="text" name="website" tabindex="-1" autocomplete="off">
            </div>

            <div class="grid md:grid-cols-2 gap-5">
                <div class="sigap-form__group">
                    <label for="nama_guest" class="sigap-form__label">Nama Lengkap</label>
                    <input type="text" name="nama_guest" id="nama_guest" class="sigap-form__input"
                           placeholder="Nama Anda" value="{{ old('nama_guest') }}" required>
                    @error('nama_guest') <span class="sigap-form__error">{{ $message }}</span> @enderror
                </div>

                <div class="sigap-form__group">
                    <label for="kontak_guest" class="sigap-form__label">Kontak (Email/No. HP)</label>
                    <input type="text" name="kontak_guest" id="kontak_guest" class="sigap-form__input"
                           placeholder="cara kami menghubungi Anda" value="{{ old('kontak_guest') }}" required>
                    @error('kontak_guest') <span class="sigap-form__error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-5">
                <div class="sigap-form__group">
                    <label for="departemen_id" class="sigap-form__label">Departemen Tujuan</label>
                    <select name="departemen_id" id="departemen_id" class="sigap-form__select" required
                            onchange="filterKategori(this.value); document.getElementById('kategori_id').value = '';">
                        <option value="">Pilih Departemen</option>
                        @foreach($departemens as $dept)
                            <option value="{{ $dept->id }}" {{ old('departemen_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('departemen_id') <span class="sigap-form__error">{{ $message }}</span> @enderror
                </div>

                <div class="sigap-form__group">
                    <label for="kategori_id" class="sigap-form__label">Kategori Masalah</label>
                    <select name="kategori_id" id="kategori_id" class="sigap-form__select" required>
                        <option value="">Pilih Departemen terlebih dahulu</option>
                        @foreach($kategoris as $kategori)
                            <option value="{{ $kategori->id }}" data-dept="{{ $kategori->departemen_id }}"
                                    {{ old('kategori_id') == $kategori->id ? 'selected' : '' }}>
                                {{ $kategori->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('kategori_id') <span class="sigap-form__error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="sigap-form__group">
                <label for="judul" class="sigap-form__label">Judul Tiket</label>
                <input type="text" name="judul" id="judul" class="sigap-form__input"
                       placeholder="Ringkasan singkat masalah" value="{{ old('judul') }}" required>
            </div>

            <div class="sigap-form__group">
                <label for="deskripsi" class="sigap-form__label">Deskripsi Lengkap</label>
                <textarea name="deskripsi" id="deskripsi" class="sigap-form__textarea" rows="5"
                          placeholder="Jelaskan detail kendala yang Anda alami" required>{{ old('deskripsi') }}</textarea>
            </div>

            <div class="sigap-form__group" id="sigap-guest-ticket-form__priority">
                <span class="sigap-form__label">Tingkat Prioritas</span>
                <div class="sigap-priority-picker">
                    @foreach(['rendah' => 'Rendah', 'sedang' => 'Sedang', 'tinggi' => 'Tinggi'] as $val => $label)
                        <label class="sigap-priority-picker__option">
                            <input type="radio" name="prioritas" value="{{ $val }}"
                                   {{ old('prioritas', 'sedang') === $val ? 'checked' : '' }}>
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="sigap-form__group">
                <label for="evidence" class="sigap-form__label">Lampiran (Opsional, maks 2MB, JPG/PNG/PDF)</label>
                <input type="file" name="evidence" id="evidence" class="sigap-form__file">
                @error('evidence') <span class="sigap-form__error">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="sigap-form__submit">Kirim Laporan</button>
        </form>
    </div>
</section>

<script>
    function filterKategori(deptId) {
        var select = document.getElementById('kategori_id');
        var hasVisible = false;
        for (var i = 0; i < select.options.length; i++) {
            var opt = select.options[i];
            if (!opt.dataset.dept) continue;
            var visible = deptId === '' || opt.dataset.dept === deptId;
            opt.hidden = !visible;
            opt.disabled = !visible;
            if (visible) hasVisible = true;
        }
        select.querySelector('option[value=""]').textContent =
            deptId === '' ? 'Pilih Departemen terlebih dahulu'
            : (hasVisible ? 'Pilih Kategori' : 'Tidak ada kategori untuk departemen ini');
    }
    document.addEventListener('DOMContentLoaded', function () {
        var dept = document.getElementById('departemen_id');
        filterKategori(dept.value);
    });
</script>
@endsection
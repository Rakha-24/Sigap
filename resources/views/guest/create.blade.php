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

            <div class="sigap-form__group" x-data="cameraCapture">
                <label for="evidence" class="sigap-form__label">Lampiran (Opsional, maks 2MB, JPG/PNG/PDF)</label>
                <input type="file" name="evidence" id="evidence" class="sigap-form__file" x-ref="field" @change="onPick">
                <button type="button" class="sigap-btn sigap-btn--secondary w-full sm:w-auto mt-2" @click="start()">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                        <circle cx="12" cy="13" r="4"/>
                    </svg>
                    Ambil Foto dengan Kamera
                </button>

                {{-- Pratinjau hasil foto --}}
                <div x-cloak x-show="shotUrl" class="mt-3 flex items-center gap-3">
                    <img :src="shotUrl" alt="Pratinjau foto lampiran"
                         class="w-20 h-20 rounded-lg object-cover border border-slate-200 shadow-sm">
                    <div>
                        <p class="text-sm font-medium text-slate-700">Foto berhasil diambil</p>
                        <button type="button" class="text-sm font-medium text-red-600 hover:underline" @click="clearShot()">Hapus foto</button>
                    </div>
                </div>
                @error('evidence') <span class="sigap-form__error">{{ $message }}</span> @enderror

                {{-- Modal kamera --}}
                <div x-cloak x-show="open" x-transition.opacity
                     class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-slate-900/70 backdrop-blur-sm">
                    <div class="bg-white w-full sm:max-w-xl rounded-t-2xl sm:rounded-xl shadow-xl overflow-hidden"
                         @click.outside="close()" x-transition>
                        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100">
                            <h3 class="text-base font-semibold text-slate-900">Ambil Foto Lampiran</h3>
                            <button type="button" class="sigap-icon-btn" title="Tutup kamera" @click="close()">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                    <line x1="18" y1="6" x2="6" y2="18"/>
                                    <line x1="6" y1="6" x2="18" y2="18"/>
                                </svg>
                            </button>
                        </div>

                        <div class="relative w-full aspect-video bg-slate-900">
                            <video x-ref="video" playsinline muted autoplay
                                   class="absolute inset-0 w-full h-full object-cover"></video>
                            <p x-cloak x-show="error"
                               class="absolute inset-0 flex items-center justify-center text-center text-sm text-slate-200 px-6">
                                <span x-text="error"></span>
                            </p>
                        </div>

                        <div class="flex items-center justify-between gap-2 px-5 py-4 flex-wrap">
                            <button type="button" class="sigap-btn sigap-btn--secondary sigap-btn--sm" @click="flip()">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 4v6h6"/>
                                    <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/>
                                </svg>
                                Ganti Kamera
                            </button>
                            <button type="button" class="sigap-btn sigap-btn--primary flex-1 sm:flex-none justify-center" @click="shoot()">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/>
                                    <circle cx="12" cy="12" r="4"/>
                                </svg>
                                Ambil Foto
                            </button>
                        </div>
                    </div>
                </div>
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
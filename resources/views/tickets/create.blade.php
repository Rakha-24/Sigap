@extends('layouts.app')
@section('title', 'Buat Tiket - SIGAP')

@section('content')
<section id="sigap-tickets-create" class="sigap-page">
    <div class="sigap-page__header">
        <div>
            <h1 class="sigap-page__title">Buat Tiket Baru</h1>
            <p class="sigap-page__subtitle">Jelaskan kendala Anda, tim kami akan segera menindaklanjuti.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data"
          class="sigap-form max-w-none" id="sigap-tickets-create__form">
        @csrf

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
                   placeholder="Ringkasan singkat masalah Anda" value="{{ old('judul') }}" required>
            @error('judul') <span class="sigap-form__error">{{ $message }}</span> @enderror
        </div>

        <div class="sigap-form__group">
            <label for="deskripsi" class="sigap-form__label">Deskripsi Lengkap</label>
            <textarea name="deskripsi" id="deskripsi" class="sigap-form__textarea" rows="5"
                      placeholder="Jelaskan detail kendala, langkah yang sudah dicoba, dll." required>{{ old('deskripsi') }}</textarea>
            @error('deskripsi') <span class="sigap-form__error">{{ $message }}</span> @enderror
        </div>

        <div class="sigap-form__group" id="sigap-tickets-create__priority">
            <span class="sigap-form__label">Tingkat Prioritas</span>
            <div class="sigap-priority-picker">
                @foreach(['tinggi' => 'Tinggi', 'sedang' => 'Sedang', 'rendah' => 'Rendah'] as $val => $label)
                    <label class="sigap-priority-picker__option">
                        <input type="radio" name="prioritas" value="{{ $val }}"
                               {{ old('prioritas', 'sedang') === $val ? 'checked' : '' }}>
                        {{ $label }}
                    </label>
                @endforeach
            </div>
            <p class="sigap-form__hint">Prioritas menentukan target penyelesaian (SLA): Tinggi ×0.5, Sedang ×1.0, Rendah ×1.5 dari SLA default.</p>
            @error('prioritas') <span class="sigap-form__error">{{ $message }}</span> @enderror
        </div>

        <div class="sigap-form__group" x-data="evidenceDropzone">
            <label for="evidence" class="sigap-form__label">Lampiran (Opsional, maks 2MB, JPG/PNG/PDF)</label>

            <div x-show="!fileName"
                 @dragover.prevent="dragOver = true"
                 @dragleave.prevent="dragOver = false"
                 @drop.prevent="onDrop($event)"
                 :class="dragOver ? 'sigap-form__dropzone--active' : 'sigap-form__dropzone'"
                 class="sigap-form__dropzone"
                 role="button" tabindex="0"
                 @keydown.enter.prevent="document.getElementById('evidence').click()"
                 @keydown.space.prevent="document.getElementById('evidence').click()"
                 @click="document.getElementById('evidence').click()">
                <svg class="mx-auto mb-3 h-8 w-8 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="17 8 12 3 7 8"/>
                    <line x1="12" y1="3" x2="12" y2="15"/>
                </svg>
                <p class="text-sm font-medium text-slate-600">Tarik & letakkan berkas di sini</p>
                <p class="text-xs text-slate-400 mt-1">
                    atau <span class="font-semibold text-sigap-600 underline">klik untuk memilih</span> — JPG, PNG, atau PDF
                </p>
            </div>

            <input type="file" name="evidence" id="evidence" class="sr-only" accept=".jpg,.jpeg,.png,.pdf"
                   x-ref="field" @change="onPick" x-on:focus="document.activeElement.focus()">

            <div x-cloak x-show="fileName" class="sigap-form__file-chip">
                <div class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 bg-white w-full sm:w-96">
                    <div class="shrink-0 w-10 h-10 rounded-lg bg-sigap-50 text-sigap-600 flex items-center justify-center">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/>
                            <polyline points="14 2 14 8 20 8"/>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-slate-800 truncate" x-text="fileName"></p>
                        <p class="text-xs text-slate-400" x-text="fileSizeText"></p>
                    </div>
                    <button type="button" class="sigap-icon-btn" title="Hapus berkas" @click="clear()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <line x1="18" y1="6" x2="6" y2="18"/>
                            <line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="mt-3 flex flex-wrap items-center gap-2">
                <button type="button" class="sigap-btn sigap-btn--secondary sigap-btn--sm" @click="start()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                        <circle cx="12" cy="13" r="4"/>
                    </svg>
                    Ambil Foto dengan Kamera
                </button>
            </div>

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

            @include('partials.camera-modal')
        </div>

        <div class="flex gap-3 flex-wrap">
            <button type="submit" class="sigap-form__submit sm:!w-auto sm:px-8">Kirim Tiket</button>
            <a href="{{ route('dashboard') }}" class="sigap-btn sigap-btn--secondary">Batal</a>
        </div>
    </form>
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
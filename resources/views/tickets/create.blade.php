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

        <div class="sigap-form__group">
            <label for="evidence" class="sigap-form__label">Lampiran (Opsional, maks 2MB, JPG/PNG/PDF)</label>
            <input type="file" name="evidence" id="evidence" class="sigap-form__file">
            @error('evidence') <span class="sigap-form__error">{{ $message }}</span> @enderror
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
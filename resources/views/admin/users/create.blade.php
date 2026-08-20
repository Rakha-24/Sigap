@extends('layouts.app')
@section('title', 'Tambah Pengguna')

@section('content')
<section id="sigap-admin-users-create" class="sigap-page">
    <div class="sigap-page__header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.index') }}" class="sigap-icon-btn" title="Kembali">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"/>
                    <polyline points="12 19 5 12 12 5"/>
                </svg>
            </a>
            <div>
                <h1 class="sigap-page__title">Tambah Pengguna Baru</h1>
                <p class="sigap-page__subtitle">Buat akun admin, agent, atau user internal.</p>
            </div>
        </div>
    </div>

    <div class="sigap-card max-w-2xl">
        <form method="POST" action="{{ route('admin.users.store') }}" id="sigap-admin-users-create__form" class="sigap-form max-w-none">
            @csrf

            <div class="grid md:grid-cols-2 gap-5">
                <div class="sigap-form__group">
                    <label for="name" class="sigap-form__label">Nama Lengkap</label>
                    <input type="text" name="name" id="name" class="sigap-form__input" value="{{ old('name') }}" required>
                    @error('name') <span class="sigap-form__error">{{ $message }}</span> @enderror
                </div>

                <div class="sigap-form__group">
                    <label for="email" class="sigap-form__label">Email</label>
                    <input type="email" name="email" id="email" class="sigap-form__input" value="{{ old('email') }}" required>
                    @error('email') <span class="sigap-form__error">{{ $message }}</span> @enderror
                </div>

                <div class="sigap-form__group">
                    <label for="password" class="sigap-form__label">Kata Sandi</label>
                    <input type="password" name="password" id="password" class="sigap-form__input" required>
                    @error('password') <span class="sigap-form__error">{{ $message }}</span> @enderror
                </div>

                <div class="sigap-form__group">
                    <label for="password_confirmation" class="sigap-form__label">Konfirmasi Kata Sandi</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="sigap-form__input" required>
                </div>

                <div class="sigap-form__group">
                    <label for="role" class="sigap-form__label">Role</label>
                    <select name="role" id="role" class="sigap-form__select" required
                            onchange="document.getElementById('sigap-admin-users-create__departemen').style.display = this.value === 'agent' ? 'block' : 'none'">
                        <option value="">Pilih Role</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="agent" {{ old('role') === 'agent' ? 'selected' : '' }}>Agent</option>
                        <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>User</option>
                    </select>
                    @error('role') <span class="sigap-form__error">{{ $message }}</span> @enderror
                </div>

                <div class="sigap-form__group" id="sigap-admin-users-create__departemen"
                     style="display: {{ old('role') === 'agent' ? 'block' : 'none' }}">
                    <label for="departemen_id" class="sigap-form__label">Departemen (khusus Agent)</label>
                    <select name="departemen_id" id="departemen_id" class="sigap-form__select">
                        <option value="">Pilih Departemen</option>
                        @foreach($departemens as $dept)
                            <option value="{{ $dept->id }}" {{ old('departemen_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('departemen_id') <span class="sigap-form__error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex gap-3 flex-wrap">
                <button type="submit" class="sigap-form__submit sm:!w-auto sm:px-8">Simpan Pengguna</button>
                <a href="{{ route('admin.users.index') }}" class="sigap-btn sigap-btn--secondary">Batal</a>
            </div>
        </form>
    </div>
</section>
@endsection
<section class="sigap-card">
    <div class="sigap-card__header">
        <div>
            <h2 class="text-lg font-semibold text-slate-900">Perbarui Kata Sandi</h2>
            <p class="text-sm text-slate-500 mt-0.5">Gunakan kata sandi panjang dan acak agar akun tetap aman.</p>
        </div>
    </div>

    <form method="post" action="{{ route('password.update') }}" class="sigap-form max-w-none">
        @csrf
        @method('put')

        <div class="sigap-form__group">
            <label for="update_password_current_password" class="sigap-form__label">Kata Sandi Saat Ini</label>
            <input id="update_password_current_password" name="current_password" type="password"
                   class="sigap-form__input" autocomplete="current-password">
            @error('current_password', 'updatePassword') <span class="sigap-form__error">{{ $message }}</span> @enderror
        </div>

        <div class="sigap-form__group">
            <label for="update_password_password" class="sigap-form__label">Kata Sandi Baru</label>
            <input id="update_password_password" name="password" type="password"
                   class="sigap-form__input" autocomplete="new-password">
            @error('password', 'updatePassword') <span class="sigap-form__error">{{ $message }}</span> @enderror
        </div>

        <div class="sigap-form__group">
            <label for="update_password_password_confirmation" class="sigap-form__label">Konfirmasi Kata Sandi Baru</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                   class="sigap-form__input" autocomplete="new-password">
            @error('password_confirmation', 'updatePassword') <span class="sigap-form__error">{{ $message }}</span> @enderror
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="sigap-btn sigap-btn--primary">Simpan</button>
            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition
                   x-init="setTimeout(() => show = false, 3000)" class="text-sm text-emerald-600 font-medium">
                    Tersimpan.
                </p>
            @endif
        </div>
    </form>
</section>
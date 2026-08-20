<section class="sigap-card border-red-200">
    <div class="sigap-card__header">
        <div>
            <h2 class="text-lg font-semibold text-red-800">Hapus Akun</h2>
            <p class="text-sm text-slate-500 mt-0.5">
                Setelah akun dihapus, seluruh data terkait akan terhapus permanen dan tidak dapat dipulihkan.
            </p>
        </div>
    </div>

    <div>
        <button type="button" class="sigap-btn sigap-btn--danger"
                x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">
            Hapus Akun
        </button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-slate-900">
                Apakah Anda yakin ingin menghapus akun?
            </h2>

            <p class="mt-2 text-sm text-slate-500">
                Setelah akun dihapus, semua data dan sumber daya akan terhapus permanen.
                Masukkan kata sandi Anda untuk mengonfirmasi penghapusan akun.
            </p>

            <div class="mt-4 sigap-form__group">
                <label for="password" class="sigap-form__label">Kata Sandi</label>
                <input id="password" name="password" type="password" class="sigap-form__input" placeholder="Kata sandi Anda">
                @error('password', 'userDeletion') <span class="sigap-form__error">{{ $message }}</span> @enderror
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" class="sigap-btn sigap-btn--secondary" x-on:click="$dispatch('close')">
                    Batal
                </button>
                <button type="submit" class="sigap-btn sigap-btn--danger">Hapus Akun</button>
            </div>
        </form>
    </x-modal>
</section>
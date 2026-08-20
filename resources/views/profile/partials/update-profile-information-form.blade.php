<section class="sigap-card">
    <div class="sigap-card__header">
        <div>
            <h2 class="text-lg font-semibold text-slate-900">Informasi Profil</h2>
            <p class="text-sm text-slate-500 mt-0.5">Perbarui informasi akun dan alamat email Anda.</p>
        </div>
    </div>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="sigap-form max-w-none">
        @csrf
        @method('patch')

        <div class="sigap-form__group">
            <label for="name" class="sigap-form__label">Nama Lengkap</label>
            <input id="name" name="name" type="text" class="sigap-form__input"
                   value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
            @error('name') <span class="sigap-form__error">{{ $message }}</span> @enderror
        </div>

        <div class="sigap-form__group">
            <label for="email" class="sigap-form__label">Email</label>
            <input id="email" name="email" type="email" class="sigap-form__input"
                   value="{{ old('email', $user->email) }}" required autocomplete="username">
            @error('email') <span class="sigap-form__error">{{ $message }}</span> @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="sigap-alert sigap-alert--error !mx-0 !mb-0 w-full">
                    Alamat email Anda belum diverifikasi.
                    <button form="send-verification" class="underline font-semibold hover:text-red-800 transition-colors">
                        Klik di sini untuk mengirim ulang email verifikasi.
                    </button>
                    @if (session('status') === 'verification-link-sent')
                        <div class="mt-1 font-medium text-emerald-700">Tautan verifikasi baru telah dikirim ke email Anda.</div>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="sigap-btn sigap-btn--primary">Simpan</button>
            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition
                   x-init="setTimeout(() => show = false, 3000)" class="text-sm text-emerald-600 font-medium">
                    Tersimpan.
                </p>
            @endif
        </div>
    </form>
</section>
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

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="sigap-form max-w-none">
        @csrf
        @method('patch')

        <div class="sigap-form__group" x-data="avatarCropper">
            <span class="sigap-form__label">Foto Profil</span>
            <div class="flex items-center gap-4 flex-wrap">
                <div class="relative">
                    <span x-show="!previewUrl" class="contents">
                        @if ($user->avatar_url)
                            <img src="{{ $user->avatar_url }}" alt="Foto profil {{ $user->name }}"
                                 class="sigap-avatar !w-16 !h-16 sigap-avatar__img">
                        @else
                            <span class="sigap-avatar !w-16 !h-16 !text-xl">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        @endif
                    </span>
                    <img x-cloak x-show="previewUrl" :src="previewUrl" alt="Pratinjau foto baru"
                         class="sigap-avatar !w-16 !h-16 sigap-avatar__img">
                </div>
                <div class="flex flex-col gap-1.5 min-w-0">
                    <input type="file" accept="image/jpeg,image/png"
                           class="sigap-form__file" x-ref="picker" @change="pick($event)">
                    {{-- Pembawa file hasil crop yang benar-benar dikirim ke server --}}
                    <input type="file" name="avatar" accept="image/jpeg,image/png" class="hidden" x-ref="avatarField">
                    <p class="text-xs text-slate-400">JPG atau PNG. Foto akan dipotong otomatis menjadi persegi.</p>
                    @if ($user->avatar)
                        <label class="inline-flex items-center gap-2 text-sm font-medium text-red-600 cursor-pointer w-fit">
                            <input type="checkbox" name="remove_avatar" value="1"
                                   class="rounded border-slate-300 text-red-600 focus:ring-red-500">
                            Hapus foto saat ini
                        </label>
                    @endif
                    @error('avatar') <span class="sigap-form__error">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Modal crop --}}
            <div x-cloak x-show="open" x-transition.opacity
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
                <div class="bg-white rounded-xl border border-slate-200 shadow-xl w-full max-w-md p-5"
                     @click.outside="cancel()">
                    <h3 class="text-base font-semibold text-slate-900">Atur Foto Profil</h3>
                    <p class="text-xs text-slate-400 mt-0.5 mb-4">Geser dan perbesar untuk memposisikan wajah di dalam bingkai.</p>
                    <div class="h-72 w-full overflow-hidden rounded-lg mb-4">
                        <img x-ref="cropImage" alt="" class="block max-w-full">
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" class="sigap-icon-btn" title="Putar ke kiri" @click="rotate(-90)">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="1 4 1 10 7 10"/>
                                <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/>
                            </svg>
                        </button>
                        <button type="button" class="sigap-icon-btn" title="Putar ke kanan" @click="rotate(90)">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="23 4 23 10 17 10"/>
                                <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
                            </svg>
                        </button>
                        <div class="ml-auto flex items-center gap-2">
                            <button type="button" class="sigap-btn sigap-btn--secondary sigap-btn--sm" @click="cancel()">Batal</button>
                            <button type="button" class="sigap-btn sigap-btn--primary sigap-btn--sm" @click="apply()">Gunakan Foto</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
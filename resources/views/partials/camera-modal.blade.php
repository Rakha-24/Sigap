{{-- Modal kamera (butuh berada di dalam scope x-data="cameraCapture") --}}
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
            <video x-ref="video" playsinline muted autoplay @loadedmetadata="onReady()"
                   class="absolute inset-0 w-full h-full object-cover"></video>

            {{-- Status: menyalakan kamera --}}
            <p x-cloak x-show="starting && !error"
               class="absolute inset-0 flex items-center justify-center gap-2 text-sm text-slate-200">
                <svg class="animate-spin" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                </svg>
                Menyalakan kamera, mohon izinkan akses bila diminta...
            </p>

            {{-- Pesan error --}}
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
            <button type="button" class="sigap-btn sigap-btn--primary flex-1 sm:flex-none justify-center"
                    :class="!ready ? 'opacity-50 pointer-events-none' : ''"
                    @click="shoot()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <circle cx="12" cy="12" r="4"/>
                </svg>
                Ambil Foto
            </button>
        </div>
    </div>
</div>

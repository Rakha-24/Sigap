import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('avatarCropper', () => ({
    open: false,
    previewUrl: null,
    cropper: null,

    pick(event) {
        const file = event.target.files?.[0];
        if (!file) return;

        if (!file.type.startsWith('image/')) {
            this.cancel();
            return;
        }

        const reader = new FileReader();
        reader.onload = async (e) => {
            this.open = true;
            this.$nextTick(async () => {
                const { default: Cropper } = await import('cropperjs');
                await import('cropperjs/dist/cropper.css');
                const img = this.$refs.cropImage;
                this.cropper?.destroy();
                img.src = e.target.result;
                this.cropper = new Cropper(img, {
                    aspectRatio: 1,
                    viewMode: 1,
                    autoCropArea: 1,
                    background: false,
                });
            });
        };
        reader.readAsDataURL(file);
    },

    rotate(deg) {
        this.cropper?.rotate(deg);
    },

    apply() {
        const canvas = this.cropper?.getCroppedCanvas({
            width: 512,
            height: 512,
            imageSmoothingQuality: 'high',
            fillColor: '#fff',
        });
        if (!canvas) return;

        canvas.toBlob((blob) => {
            if (!blob) return;

            if (this.previewUrl) URL.revokeObjectURL(this.previewUrl);
            this.previewUrl = URL.createObjectURL(blob);

            const cropped = new File([blob], 'avatar.jpg', { type: 'image/jpeg' });
            const transfer = new DataTransfer();
            transfer.items.add(cropped);
            this.$refs.avatarField.files = transfer.files;

            this.shutdown();
        }, 'image/jpeg', 0.9);
    },

    cancel() {
        this.shutdown();
        this.$refs.avatarField.value = '';
        if (this.previewUrl) URL.revokeObjectURL(this.previewUrl);
        this.previewUrl = null;
    },

    shutdown() {
        this.open = false;
        this.cropper?.destroy();
        this.cropper = null;
        this.$refs.picker.value = '';
    },
}));

Alpine.data('cameraCapture', () => ({
    open: false,
    error: null,
    starting: false,
    ready: false,
    facing: 'environment',
    stream: null,
    shotUrl: null,

    async start() {
        this.error = null;
        this.ready = false;
        this.open = true;

        if (!window.isSecureContext) {
            this.error = `Kamera diblokir browser karena halaman ini tidak aman (${location.protocol}//${location.host}). Buka aplikasi melalui localhost atau HTTPS, atau gunakan tombol pilih file.`;
            return;
        }
        if (!navigator.mediaDevices?.getUserMedia) {
            this.error = 'Browser ini tidak mendukung akses kamera. Silakan pilih file dari galeri.';
            return;
        }

        this.starting = true;
        await this.startStream();
    },

    async startStream() {
        this.stopStream();
        this.ready = false;
        try {
            this.stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: this.facing, width: { ideal: 1920 }, height: { ideal: 1080 } },
                audio: false,
            });
        } catch (e) {
            this.starting = false;
            this.error = this.errorMessage(e);
            console.warn('[SIGAP] Kamera gagal:', e?.name, e?.message);
            return;
        }

        this.$nextTick(async () => {
            const video = this.$refs.video;
            if (!video) return;
            video.srcObject = this.stream;
            try {
                await video.play();
            } catch (e) {
                console.warn('[SIGAP] Video belum dapat diputar:', e?.name);
            }
        });
    },

    stopStream() {
        this.stream?.getTracks().forEach((track) => track.stop());
        this.stream = null;
    },

    errorMessage(e) {
        switch (e?.name) {
            case 'NotAllowedError':
            case 'SecurityError':
                return 'Izin kamera diblokir. Klik ikon kamera atau gembok di address bar, pilih "Izinkan", lalu coba lagi.';
            case 'NotFoundError':
                return 'Kamera tidak ditemukan pada perangkat ini.';
            case 'NotReadableError':
                return 'Kamera sedang dipakai aplikasi lain. Tutup aplikasi tersebut (mis. Zoom/Meet) lalu coba lagi.';
            default:
                return 'Gagal mengakses kamera. Silakan pilih file dari galeri sebagai alternatif.';
        }
    },

    onReady() {
        this.starting = false;
        this.ready = true;
    },

    async flip() {
        this.facing = this.facing === 'environment' ? 'user' : 'environment';
        this.starting = true;
        await this.startStream();
    },

    shoot() {
        const video = this.$refs.video;
        if (!this.ready || !video?.videoWidth) return;

        const scale = Math.min(1, 1600 / Math.max(video.videoWidth, video.videoHeight));
        const canvas = document.createElement('canvas');
        canvas.width = Math.round(video.videoWidth * scale);
        canvas.height = Math.round(video.videoHeight * scale);
        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);

        canvas.toBlob((blob) => {
            if (!blob) return;

            if (this.shotUrl) URL.revokeObjectURL(this.shotUrl);
            this.shotUrl = URL.createObjectURL(blob);

            const photo = new File([blob], `foto-${Date.now()}.jpg`, { type: 'image/jpeg' });
            const transfer = new DataTransfer();
            transfer.items.add(photo);
            this.$refs.field.files = transfer.files;

            this.close();
        }, 'image/jpeg', 0.85);
    },

    close() {
        this.open = false;
        this.starting = false;
        this.ready = false;
        this.stopStream();
    },

    onPick() {
        if (this.shotUrl) {
            URL.revokeObjectURL(this.shotUrl);
            this.shotUrl = null;
        }
    },

    clearShot() {
        this.onPick();
        this.$refs.field.value = '';
    },
}));

Alpine.start();

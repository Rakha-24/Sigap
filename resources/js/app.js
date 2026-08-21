import Alpine from 'alpinejs';
import Cropper from 'cropperjs';
import 'cropperjs/dist/cropper.css';

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
        reader.onload = (e) => {
            this.open = true;
            this.$nextTick(() => {
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
    facing: 'environment',
    stream: null,
    shotUrl: null,

    async start() {
        this.error = null;
        this.open = true;

        if (!navigator.mediaDevices?.getUserMedia) {
            this.error = 'Browser ini tidak mendukung akses kamera. Silakan pilih file dari galeri.';
            return;
        }
        await this.startStream();
    },

    async startStream() {
        this.stopStream();
        try {
            this.stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: this.facing, width: { ideal: 1920 }, height: { ideal: 1080 } },
                audio: false,
            });
            this.$nextTick(() => {
                const video = this.$refs.video;
                video.srcObject = this.stream;
                video.play();
            });
        } catch {
            this.error = window.isSecureContext
                ? 'Tidak dapat mengakses kamera. Pastikan izin kamera telah diizinkan untuk situs ini.'
                : 'Kamera hanya dapat diakses melalui HTTPS atau localhost. Silakan pilih file dari galeri.';
        }
    },

    async flip() {
        this.facing = this.facing === 'environment' ? 'user' : 'environment';
        await this.startStream();
    },

    shoot() {
        const video = this.$refs.video;
        if (!video?.videoWidth) return;

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
        this.stream?.getTracks().forEach((track) => track.stop());
        this.stream = null;
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

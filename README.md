# 🚀 SIGAP — Sistem Informasi Gangguan & Antrean Pelayanan

[![Status](https://img.shields.io/badge/status-active-success.svg)]()
[![License](https://img.shields.io/badge/license-MIT-blue.svg)]()

SIGAP adalah sistem *helpdesk* generik yang mendukung pelaporan publik (guest) maupun internal (multi-role: admin, agent, user). Sistem ini dirancang untuk memproses keluhan secara transparan, dilengkapi dengan *audit trail* yang *immutable* dan pelacakan SLA (*Service Level Agreement*).

## ✨ Fitur Utama

*   **Multi-Role Dashboard**: Menyediakan ruang kerja yang terisolasi dan spesifik untuk peran *Admin*, *Agent*, dan *User*.
*   **Guest Ticketing**: Portal pelaporan bagi masyarakat umum tanpa perlu membuat akun, yang dilindungi oleh *rate limiting* untuk mencegah *spam bot*.
*   **Siklus Tiket & Evidence Logic**: Memiliki validasi ketat yang menolak penyelesaian tiket (*status resolved*) jika *agent* tidak menyertakan file bukti (*evidence*) penanganan.
*   **Immutable Audit Log**: Semua riwayat perubahan tiket dan log aktivitas tidak dapat diubah atau dihapus, dilindungi langsung di level *database* menggunakan *trigger*.
*   **Antrean Prioritas Cerdas**: *Agent* mendapatkan antrean tiket yang diurutkan secara otomatis berdasarkan prioritas tertinggi (Tinggi, Sedang, Rendah) dan sisa waktu SLA yang paling dekat.
*   **Laporan & Analitik**: Panel khusus Admin untuk melihat matriks kinerja, kepatuhan SLA per departemen, dan kemampuan *export* data ke dalam bentuk Excel (.xlsx).

## 🛠️ Tech Stack

*   **Framework**: Laravel 13
*   **Backend / Logic**: PHP 8.3
*   **Database**: PostgreSQL
*   **Frontend / UI**: Laravel Breeze (Blade)

## ⚙️ Persyaratan (Prerequisites)

Pastikan sistem Anda sudah memiliki perangkat lunak berikut sebelum memulai:

*   PHP 8.3
*   PostgreSQL
*   Composer
*   Node.js & NPM

## 🚀 Instalasi & Konfigurasi

Ikuti langkah-langkah di bawah ini untuk mengonfigurasi dan menjalankan *project* SIGAP secara lokal:

1.  **Clone repositori ini:**
    ```bash
    git clone [https://github.com/Rakha-24/Sigap.git](https://github.com/Rakha-24/Sigap.git)
    cd Sigap
    ```
2.  **Install Dependency:**
    ```bash
    composer install
    npm install
    npm run build
    ```
3.  **Konfigurasi Environment:**
    *   Salin file `.env.example` menjadi `.env`.
    *   Buka file `.env` dan sesuaikan koneksi ke PostgreSQL Anda:
        ```env
        DB_CONNECTION=pgsql
        DB_HOST=127.0.0.1
        DB_PORT=5432
        DB_DATABASE=nama_database_sigap
        DB_USERNAME=username_postgres
        DB_PASSWORD=password_postgres
        ```
4.  **Generate Application Key:**
    ```bash
    php artisan key:generate
    ```
5.  **Migrasi & Seeding Database:**
    Sistem SIGAP membutuhkan urutan migrasi khusus (seperti pembuatan tipe ENUM *native* PostgreSQL) dan *Master Data*. Jalankan perintah berikut untuk mengeksekusinya sekaligus:
    ```bash
    php artisan migrate:fresh --seed
    ```
    *(Proses ini akan mengonfigurasi database sekaligus memasukkan data referensi departemen, kategori, dan akun pengguna awal)*.
6.  **Jalankan aplikasi:**
    ```bash
    php artisan serve
    ```
    Buka `http://localhost:8000` di *browser* Anda.

## 🧪 Menjalankan Test Suite

Suite pengujian berjalan di atas SQLite *in-memory* (diatur lewat `phpunit.xml`), sehingga tidak membutuhkan PostgreSQL untuk dieksekusi:

```bash
php artisan test
```

Catatan: migrasi bersifat *driver-aware* — fitur native PostgreSQL (tipe ENUM, CHECK constraint, trigger immutable `audit_logs`) hanya aktif saat `DB_CONNECTION=pgsql`; di SQLite, kolom enum dibuat sebagai string agar test ringan dan cepat.

## 👥 Akun Default (Testing)

Proses *seeding* di atas telah menyiapkan beberapa akun untuk keperluan *testing*:

*   **Admin**: `admin@sigap.test` (Sandi: `password`)
*   **Agent (IT Support)**: `agent@sigap.test` (Sandi: `password`)
*   **User Umum**: `user@sigap.test` (Sandi: `password`)

## 🤝 Kontribusi

Kontribusi selalu diterima! Jika Anda ingin berkontribusi, silakan *fork* repositori ini dan buat *pull request* dengan tambahan fitur atau perbaikan Anda.

## 📄 Lisensi

Project ini dilisensikan di bawah [MIT License](LICENSE).
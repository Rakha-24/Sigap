<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Simpan foto profil langsung di database (base64 di kolom TEXT).
     * Vercel serverless memakai filesystem read-only (/var/task), sehingga
     * penyimpanan avatar ke storage/public tidak bertahan dan menyebabkan
     * error 500 saat unggah. Kolom avatar (path disk) dipertahankan sebagai
     * fallback bagi data lama.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('avatar_data')->nullable()->after('avatar');
            $table->string('avatar_mime', 100)->nullable()->after('avatar_data');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar_mime', 'avatar_data']);
        });
    }
};

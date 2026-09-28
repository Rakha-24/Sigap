<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Perbaiki penyimpanan bukti tiket: kolom bytea di PostgreSQL.
     *
     * Latar belakang:
     * - app/Casts/Base64Binary.php selalu MENULIS base64 (ASCII aman) ke kolom bytea.
     * - PDO PHP 8.1+ MEMBACA bytea sebagai string hex ("\x<hex>"), bukan byte mentah,
     *   sehingga base64_decode() gagal → unduhan bukti menggantung (0 byte).
     *
     * Solusi: ubah kolom menjadi TEXT agar base64 tersimpan apa adanya.
     * CATATAN: ALTER bytea→text PG justru menghasilkan representasi hex ("\x..."),
     * jadi konversi nilai lama dilakukan lewat kolom perantara berisi teks base64
     * yang sudah dipulihkan dari bytea.
     */
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            $this->convertLegacyPostgresRows();

            return;
        }

        // SQLite/MySQL: ubah tipe langsung (nilai yang tersimpan memang base64).
        Schema::table('ticket_evidence', function (Blueprint $table) {
            $table->text('data')->change();
        });
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            $this->restorePostgresRows();

            return;
        }

        Schema::table('ticket_evidence', function (Blueprint $table) {
            $table->binary('data')->change();
        });
    }

    private function convertLegacyPostgresRows(): void
    {
        // Kolom perantara ber-tipe text.
        Schema::table('ticket_evidence', function (Blueprint $table) {
            $table->text('data_new')->nullable()->after('data');
        });

        // Nilai lama: bytea = literal teks base64 (ditulis via binding PDO).
        // data::text menghasilkan '\x<hex-dari-karakter-ASCII>' → pulihkan teks aslinya.
        // CATATAN: pada SQL LIKE, backslash adalah karakter escape default, jadi
        // polanya harus E'\\\\x%' (awal literal "\x"), bukan '\x%'.
        DB::statement("
            UPDATE ticket_evidence
            SET data_new = CASE
                WHEN position(E'\\\\x' in data::text) = 1
                THEN convert_from(decode(substring(data::text FROM 3), 'hex'), 'UTF8')
                ELSE data::text
            END
            WHERE data IS NOT NULL
        ");

        Schema::table('ticket_evidence', function (Blueprint $table) {
            $table->dropColumn('data');
        });

        Schema::table('ticket_evidence', function (Blueprint $table) {
            $table->renameColumn('data_new', 'data');
        });
    }

    private function restorePostgresRows(): void
    {
        Schema::table('ticket_evidence', function (Blueprint $table) {
            $table->binary('data_new')->nullable()->after('data');
        });

        // Simpan kembali sebagai bytea: bytes dari teks base64 (parameter PDO text).
        DB::statement("
            UPDATE ticket_evidence
            SET data_new = convert_to(data, 'UTF8')
            WHERE data IS NOT NULL
        ");

        Schema::table('ticket_evidence', function (Blueprint $table) {
            $table->dropColumn('data');
        });

        Schema::table('ticket_evidence', function (Blueprint $table) {
            $table->renameColumn('data_new', 'data');
        });
    }
};

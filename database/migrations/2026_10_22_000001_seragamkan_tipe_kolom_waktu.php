<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Satukan tipe kolom waktu menjadi "timestamp without time zone".
     *
     * Sebelumnya kolom-kolom ini bertipe "timestamp with time zone" sementara
     * mayoritas tabel lain "timestamp without time zone". Dengan session DB
     * Asia/Jakarta dan app.timezone UTC, nilai tersimpan bergeser 7 jam setiap
     * kali dibaca kembali, sehingga perhitungan keterlambatan SLA meleset.
     */
    private array $kolom = [
        'tickets'     => ['sla_target_at', 'resolved_at', 'closed_at'],
        'audit_logs'  => ['created_at'],
    ];

    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        foreach ($this->kolom as $tabel => $koloms) {
            if (! Schema::hasTable($tabel)) {
                continue;
            }

            foreach ($koloms as $kolom) {
                if (! Schema::hasColumn($tabel, $kolom)) {
                    continue;
                }

                // AT TIME ZONE 'Asia/Jakarta' mengonversi nilai ber-offset ke
                // waktu dinding WIB tanpa menggeser maknanya.
                DB::statement("
                    ALTER TABLE {$tabel}
                    ALTER COLUMN {$kolom} TYPE timestamp without time zone
                    USING {$kolom} AT TIME ZONE 'Asia/Jakarta'
                ");
            }
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        foreach ($this->kolom as $tabel => $koloms) {
            if (! Schema::hasTable($tabel)) {
                continue;
            }

            foreach ($koloms as $kolom) {
                if (! Schema::hasColumn($tabel, $kolom)) {
                    continue;
                }

                DB::statement("
                    ALTER TABLE {$tabel}
                    ALTER COLUMN {$kolom} TYPE timestamp with time zone
                    USING {$kolom} AT TIME ZONE 'Asia/Jakarta'
                ");
            }
        }
    }
};
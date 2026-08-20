<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Trigger immutable hanya tersedia di PostgreSQL. Di SQLite (test suite)
        // fitur ini dilewati karena memakai plpgsql yang tidak didukung.
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        // Trigger function yang menolak UPDATE/DELETE apapun terhadap audit_logs,
        // termasuk jika dijalankan oleh user database ber-role superuser aplikasi.
        DB::statement("
            CREATE OR REPLACE FUNCTION prevent_audit_log_mutation()
            RETURNS TRIGGER AS $$
            BEGIN
                RAISE EXCEPTION 'audit_logs bersifat immutable: operasi % tidak diizinkan', TG_OP;
                RETURN NULL;
            END;
            $$ LANGUAGE plpgsql;
        ");

        DB::statement('
            CREATE TRIGGER trg_prevent_audit_update
            BEFORE UPDATE ON audit_logs
            FOR EACH ROW EXECUTE FUNCTION prevent_audit_log_mutation();
        ');

        DB::statement('
            CREATE TRIGGER trg_prevent_audit_delete
            BEFORE DELETE ON audit_logs
            FOR EACH ROW EXECUTE FUNCTION prevent_audit_log_mutation();
        ');
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('DROP TRIGGER IF EXISTS trg_prevent_audit_update ON audit_logs');
        DB::statement('DROP TRIGGER IF EXISTS trg_prevent_audit_delete ON audit_logs');
        DB::statement('DROP FUNCTION IF EXISTS prevent_audit_log_mutation');
    }
};

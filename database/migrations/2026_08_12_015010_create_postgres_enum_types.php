<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Buat tipe ENUM native PostgreSQL yang dipakai kolom users.role,
     * tickets.prioritas, dan tickets.status.
     *
     * Migrasi ini hanya aktif di driver 'pgsql'; di SQLite (test suite) tipe
     * ini tidak diperlukan karena kolom enum dideklarasikan sebagai string
     * biasa di migrasi pemakai (add_columns_to_users_table dan create_tickets_table).
     */
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        $this->ensureType('user_role', "'admin', 'agent', 'user'");
        $this->ensureType('ticket_priority', "'tinggi', 'sedang', 'rendah'");
        $this->ensureType('ticket_status', "'open', 'in_progress', 'resolved', 'closed', 'rejected'");
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('DROP TYPE IF EXISTS ticket_status');
        DB::statement('DROP TYPE IF EXISTS ticket_priority');
        DB::statement('DROP TYPE IF EXISTS user_role');
    }

    private function ensureType(string $name, string $values): void
    {
        $exists = DB::selectOne(
            'SELECT 1 FROM pg_type WHERE typname = ?',
            [$name]
        );

        if (! $exists) {
            DB::statement("CREATE TYPE {$name} AS ENUM ({$values})");
        }
    }
};

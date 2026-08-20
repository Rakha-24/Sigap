<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('departemen_id')->nullable()->after('id')
                ->constrained('departemens')->nullOnDelete();
            $table->string('avatar')->nullable()->after('email');
            $table->boolean('is_active')->default(true)->after('password');
        });

        // Kolom enum native harus ditambah via raw statement (Postgres)
        DB::statement("ALTER TABLE users ADD COLUMN role user_role NOT NULL DEFAULT 'user'");
        DB::statement("CREATE INDEX users_role_index ON users (role)");

        // CHECK CONSTRAINT: role 'agent' WAJIB terikat ke satu departemen,
        // karena antrean tiket agent (Bagian 6.2) difilter berdasarkan departemen_id.
        DB::statement("
            ALTER TABLE users ADD CONSTRAINT chk_agent_wajib_departemen
            CHECK (
                role <> 'agent' OR departemen_id IS NOT NULL
            )
        ");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS chk_agent_wajib_departemen");
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('departemen_id');
            $table->dropColumn(['avatar', 'is_active']);
        });
        DB::statement("DROP INDEX IF EXISTS users_role_index");
        DB::statement("ALTER TABLE users DROP COLUMN IF EXISTS role");
    }
};
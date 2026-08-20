<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_tiket', 30)->unique(); // Tracking ID publik, contoh: TKT-20260812-A7X9

            $table->foreignId('departemen_id')->constrained('departemens');
            $table->foreignId('kategori_id')->constrained('kategoris');

            // Pelapor internal (nullable) vs pelapor guest
            $table->foreignId('id_pelapor')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nama_guest', 100)->nullable();
            $table->string('kontak_guest', 100)->nullable(); // email atau no. HP guest
            $table->string('tracking_token', 64)->nullable(); // token rahasia tambahan untuk verifikasi tracking

            $table->foreignId('assigned_agent_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('judul', 150);
            $table->text('deskripsi');

            $table->string('file_evidence_pelapor')->nullable();
            $table->string('file_evidence_penyelesaian')->nullable();
            $table->text('catatan_penyelesaian')->nullable();

            $table->timestampTz('sla_target_at')->nullable();
            $table->timestampTz('resolved_at')->nullable();
            $table->timestampTz('closed_at')->nullable();

            $table->string('ip_pelapor', 45)->nullable();

            $table->timestamps();

            $table->index('nomor_tiket');
        });

        $this->addStatusAndPrioritas();
    }

    private function addStatusAndPrioritas(): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE tickets ADD COLUMN prioritas ticket_priority NOT NULL DEFAULT 'sedang'");
            DB::statement("ALTER TABLE tickets ADD COLUMN status ticket_status NOT NULL DEFAULT 'open'");

            // Index yang melibatkan kolom enum native (prioritas, status) HARUS dibuat
            // SETELAH kolom tersebut ada — karena itu tidak bisa didefinisikan di dalam
            // Schema::create() di atas (kolomnya baru muncul lewat DB::statement ini).
            DB::statement('CREATE INDEX tickets_departemen_status_index ON tickets (departemen_id, status)');
            DB::statement('CREATE INDEX tickets_prioritas_status_index ON tickets (prioritas, status)');

            // CHECK CONSTRAINT: guest wajib punya nama_guest+kontak_guest JIKA id_pelapor NULL
            DB::statement('
                ALTER TABLE tickets ADD CONSTRAINT chk_pelapor_valid
                CHECK (
                    id_pelapor IS NOT NULL
                    OR (nama_guest IS NOT NULL AND kontak_guest IS NOT NULL)
                )
            ');

            // CHECK CONSTRAINT: status resolved/closed WAJIB ada evidence penyelesaian
            DB::statement("
                ALTER TABLE tickets ADD CONSTRAINT chk_evidence_resolved
                CHECK (
                    status NOT IN ('resolved', 'closed')
                    OR file_evidence_penyelesaian IS NOT NULL
                )
            ");

            return;
        }

        // SQLite (test suite): tidak mendukung ALTER TABLE ADD CONSTRAINT / tipe enum,
        // kolom dideklarasikan sebagai string dengan nama index yang sama.
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('prioritas', 20)->default('sedang');
            $table->string('status', 20)->default('open');
            $table->index(['departemen_id', 'status'], 'tickets_departemen_status_index');
            $table->index(['prioritas', 'status'], 'tickets_prioritas_status_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->nullable()->constrained('tickets')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('aktor_label', 100)->nullable(); // nama guest jika aktor bukan user terdaftar
            $table->string('aksi', 50); // ticket_created, status_changed, comment_added, guest_tracked, dst.
            $table->text('deskripsi');
            $table->jsonb('data_before')->nullable();
            $table->jsonb('data_after')->nullable();
            $table->string('ip_address', 45)->nullable();
            // SENGAJA HANYA created_at, TIDAK ADA updated_at, agar tidak ada jejak "pernah diubah"
            $table->timestampTz('created_at')->useCurrent();

            $table->index(['ticket_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
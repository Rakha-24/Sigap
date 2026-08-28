<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ticket_evidence', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            // 'pelapor' | 'penyelesaian'
            $table->string('jenis', 20);
            $table->string('nama_asli', 255);
            $table->string('mime', 100);
            $table->unsignedBigInteger('ukuran');

            // 'binary' → bytea di PostgreSQL (cocok untuk arsitektur serverless
            // yang filesystem-nya read-only), blob pada SQLite (test suite).
            $table->binary('data');

            $table->timestamps();

            $table->unique(['ticket_id', 'jenis'], 'ticket_evidence_ticket_jenis_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_evidence');
    }
};

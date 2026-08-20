<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategoris', function (Blueprint $table) {
            $table->id();
            $table->foreignId('departemen_id')->constrained('departemens')->cascadeOnDelete();
            $table->string('nama', 100);
            // Default SLA dalam jam, dipakai untuk menghitung sla_target_at otomatis
            $table->unsignedSmallInteger('default_sla_jam')->default(24);
            $table->timestamps();

            $table->unique(['departemen_id', 'nama']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kategoris');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Index untuk query yang paling sering dijalankan: antrean agent (filter
     * departemen + status terbuka), daftar tiket menurut pelapor, dan penyortiran
     * SLA. Berguna khususnya saat data sudah mencapai skala ribuan baris.
     */
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->index('status');
            $table->index('departemen_id');
            $table->index('assigned_agent_id');
            $table->index('id_pelapor');
            $table->index('sla_target_at');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['departemen_id']);
            $table->dropIndex(['assigned_agent_id']);
            $table->dropIndex(['id_pelapor']);
            $table->dropIndex(['sla_target_at']);
        });
    }
};

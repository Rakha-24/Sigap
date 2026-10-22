<?php

namespace App\Services;

use App\Models\Kategori;
use Carbon\Carbon;

class SlaCalculator
{
    /**
     * SLA ditetapkan per kategori dan berlaku sama untuk semua prioritas.
     * Prioritas hanya memengaruhi urutan penanganan di antrean agent,
     * tidak memotong atau memperpanjang batas waktu SLA.
     */
    public function hitung(Kategori $kategori, string $prioritas): Carbon
    {
        return now()->addHours((int) $kategori->default_sla_jam);
    }
}
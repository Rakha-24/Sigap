<?php

namespace App\Services;

use App\Models\Kategori;
use Carbon\Carbon;

class SlaCalculator
{
    /** Faktor pengali SLA berdasarkan prioritas terhadap default_sla_jam kategori. */
    private const FAKTOR_PRIORITAS = [
        'tinggi' => 0.5,
        'sedang' => 1.0,
        'rendah' => 1.5,
    ];

    public function hitung(Kategori $kategori, string $prioritas): Carbon
    {
        $jam = $kategori->default_sla_jam * (self::FAKTOR_PRIORITAS[$prioritas] ?? 1.0);
        return now()->addHours((int) round($jam));
    }
}
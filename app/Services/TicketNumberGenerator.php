<?php

namespace App\Services;

use App\Models\Ticket;
use Illuminate\Support\Str;

class TicketNumberGenerator
{
    /** Format: TKT-YYYYMMDD-XXXXX (5 karakter acak base32, hindari 0/O/1/I). */
    public function generate(): string
    {
        do {
            $random = Str::upper(Str::random(5));
            $nomor  = 'TKT-' . now()->format('Ymd') . '-' . $random;
        } while (Ticket::where('nomor_tiket', $nomor)->exists());

        return $nomor;
    }
}
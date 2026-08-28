<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Menyimpan byte mentah sebagai base64 di database.
 *
 * Alasan: PDO/Postgres mengirim nilai parameter sebagai teks UTF8. Byte biner
 * (mis. 0x89 pada PNG/JPEG) bukan UTF8 valid sehingga insert bytea gagal dengan
 * SQLSTATE[22021]. Dengan menyimpan base64 (ASCII murni), transaksi Postgres
 * aman; PHP tetap melihat byte mentah.
 */
class Base64Binary implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return is_string($value) ? base64_decode($value) : $value;
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return is_string($value) ? base64_encode($value) : $value;
    }
}

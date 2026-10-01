<?php

namespace App\Cache;

use Closure;
use Illuminate\Cache\DatabaseStore;

/**
 * Store cache database yang aman untuk Neon/PgBouncer transaction-mode.
 *
 * DatabaseStore bawaan mengimplementasikan increment/decrement dengan
 * transaction eksplisit + SELECT ... FOR UPDATE. Di Neon, transaksi
 * multi-pernyataan yang dijembatani PgBouncer dapat di-abort sehingga
 * update berikutnya memicu SQLSTATE 25P02 (lihat juga catatan di
 * app/Http/Controllers/Agent/TicketClaimController.php:27).
 *
 * Timpa hanya incrementOrDecrement agar memakai autocommit
 * (SELECT lalu UPDATE terpisah) — tetap berbagi akses antar-instance
 * serverless tanpa transaksi. Operator menulis lainnya (get/put/upsert/
 * forget) sudah aman tanpa transaksi pada DatabaseStore bawaan.
 *
 * Trade-off: tanpa kunci baris, augmentasi hit berkesempatan kecil
 * mengurangi nilai counter saat ada serangan paralel dari banyak instance.
 * Akibatnya hanya batas rate limiting yang sedikit longgar pada kondisi
 * ekstrem, bukan kesalahan total — dapat diterima mengingat batasan Neon.
 */
class NeonSafeDatabaseStore extends DatabaseStore
{
    /**
     * Increment/decrement an item in the cache using autocommit statements.
     *
     * @param  string  $key
     * @param  int|float  $value
     * @return int|false
     */
    protected function incrementOrDecrement($key, $value, Closure $callback)
    {
        $prefixed = $this->prefix.$key;

        $cached = $this->table()->where('key', $prefixed)->value('value');

        // Jika belum ada di cache, kembalikan false (tidak ada nilai bahan).
        if (is_null($cached)) {
            return false;
        }

        $current = $this->unserialize($cached);

        $new = $callback((int) $current, $value);

        if (! is_numeric($current)) {
            return false;
        }

        $this->table()->where('key', $prefixed)->update([
            'value' => $this->serialize($new),
        ]);

        return $new;
    }
}

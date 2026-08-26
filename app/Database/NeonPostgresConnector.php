<?php

namespace App\Database;

use Illuminate\Database\Connectors\PostgresConnector;

class NeonPostgresConnector extends PostgresConnector
{
    /**
     * Override DSN Laravel supaya menyertakan parameter "options=endpoint=..."
     * yang dibutuhkan Neon untuk routing koneksi lewat SNI-workaround.
     * Laravel bawaan tidak pernah meneruskan parameter ini ke PDO.
     */
    protected function getDsn(array $config)
    {
        $dsn = parent::getDsn($config);

        if (! empty($config['neon_endpoint_id'])) {
            $dsn .= ";options=endpoint={$config['neon_endpoint_id']}";
        }

        return $dsn;
    }
}
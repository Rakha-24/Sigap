<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RecalculateSla extends Command
{
    protected $signature = 'sigap:recalculate-sla
                            {--force : Benar-benar simpan perubahan (default hanya simulasi)}
                            {--resolved-only : Hanya tiket yang sudah selesai}';

    protected $description = 'Hitung ulang sla_target_at tiket lama agar sesuai aturan SLA tetap per kategori';

    public function handle(): int
    {
        $force = (bool) $this->option('force');

        // Nilai dibaca sebagai string mentah agar tidak terkena konversi timezone
        // Carbon/PostgreSQL (kolom timestamp with time zone, session Asia/Jakarta,
        // sedangkan app.timezone = UTC).
        $sql = '
            SELECT t.id,
                   t.nomor_tiket,
                   t.prioritas,
                   t.created_at::text  AS created_mentah,
                   t.sla_target_at::text AS sla_mentah,
                   k.default_sla_jam
            FROM tickets t
            JOIN kategoris k ON k.id = t.kategori_id
            WHERE k.default_sla_jam IS NOT NULL
        ';

        if ($this->option('resolved-only')) {
            $sql .= ' AND t.resolved_at IS NOT NULL';
        }

        $sql .= ' ORDER BY t.id';

        $rows = DB::select($sql);

        $total = 0;
        $changed = [];
        $skipped = 0;

        foreach ($rows as $row) {
            $total++;
            $jam = (int) $row->default_sla_jam;

            if (! $row->created_mentah) {
                $skipped++;
                continue;
            }

            $targetBaru = date('Y-m-d H:i:s', strtotime($row->created_mentah) + ($jam * 3600));
            $lamaTanpaTz = $row->sla_mentah ? substr((string) $row->sla_mentah, 0, 19) : null;

            if ($lamaTanpaTz === $targetBaru) {
                continue;
            }

            $changed[] = [
                $row->nomor_tiket,
                $row->prioritas,
                $lamaTanpaTz ?? '-',
                $targetBaru,
            ];

            if ($force) {
                DB::table('tickets')->where('id', $row->id)->update(['sla_target_at' => $targetBaru]);
            }
        }

        if ($changed) {
            $this->table(
                ['Nomor', 'Prioritas', 'SLA Lama', 'SLA Baru (tetap per kategori)'],
                array_slice($changed, 0, 50)
            );
            if (count($changed) > 50) {
                $this->line('... dan ' . (count($changed) - 50) . ' baris lainnya.');
            }
        }

        $this->newLine();
        $this->info("Total tiket                   : {$total}");
        $this->info('Baris perlu diubah            : ' . count($changed));
        $this->info("Dilewati (data tidak lengkap) : {$skipped}");

        if (! $force) {
            $this->warn('MODE SIMULASI - tidak ada yang disimpan. Tambahkan --force untuk menerapkan.');
        } else {
            $this->info('Perubahan tersimpan.');
        }

        return self::SUCCESS;
    }
}
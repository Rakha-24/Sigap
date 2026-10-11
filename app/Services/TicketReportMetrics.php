<?php

namespace App\Services;

use App\Models\Departemen;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Agregasi data tiket untuk laporan Excel.
 *
 * Sengaja ditulis dengan query builder portabel (whereColumn, groupBy)
 * alih-alih SQL khusus Postgres, agar laporan yang sama dapat dihasilkan
 * di produksi (Postgres/Neon) maupun saat pengujian (SQLite in-memory).
 */
class TicketReportMetrics
{
    /** Label tampilan status tiket, konsisten dengan UI. */
    public const STATUS_LABELS = [
        'open' => 'Menunggu',
        'in_progress' => 'Diproses',
        'resolved' => 'Selesai',
        'closed' => 'Ditutup',
    ];

    public const PRIORITAS_LABELS = [
        'tinggi' => 'Tinggi',
        'sedang' => 'Sedang',
        'rendah' => 'Rendah',
    ];

    private ?array $summary = null;

    public function summary(): array
    {
        return $this->summary ??= $this->compute();
    }

    private function compute(): array
    {
        $total = Ticket::count();

        $byStatus = Ticket::query()
            ->selectRaw('status, COUNT(*) AS jumlah')
            ->groupBy('status')
            ->pluck('jumlah', 'status');

        $byPrioritas = Ticket::query()
            ->selectRaw('prioritas, COUNT(*) AS jumlah')
            ->groupBy('prioritas')
            ->pluck('jumlah', 'prioritas');

        $aktif = (int) $byStatus->only(['open', 'in_progress'])->sum();
        $selesai = (int) $byStatus->only(['resolved', 'closed'])->sum();

        $tepatWaktu = Ticket::query()
            ->whereIn('status', ['resolved', 'closed'])
            ->whereNotNull('resolved_at')
            ->whereNotNull('sla_target_at')
            ->whereColumn('resolved_at', '<=', 'sla_target_at')
            ->count();

        $denganTarget = Ticket::query()
            ->whereIn('status', ['resolved', 'closed'])
            ->whereNotNull('resolved_at')
            ->whereNotNull('sla_target_at')
            ->count();

        $melewati = Ticket::query()
            ->whereNotIn('status', ['resolved', 'closed'])
            ->whereNotNull('sla_target_at')
            ->where('sla_target_at', '<', now())
            ->count();

        return [
            'total' => $total,
            'aktif' => $aktif,
            'selesai' => $selesai,
            'rata_resolusi_jam' => $this->rataRataResolusiJam(),
            'kepatuhan_sla' => $denganTarget > 0 ? round($tepatWaktu / $denganTarget * 100, 1) : 0.0,
            'melewati_sla' => $melewati,
            'tingkat_penyelesaian' => $total > 0 ? round($selesai / $total * 100, 1) : 0.0,
            'per_departemen' => $this->perDepartemen(),
            'per_status' => $this->distribusi($byStatus, self::STATUS_LABELS, $total),
            'per_prioritas' => $this->distribusi($byPrioritas, self::PRIORITAS_LABELS, $total),
        ];
    }

    /**
     * Rata-rata durasi penyelesaian (jam) dihitung di PHP agar portabel
     * antar-driver; hanya kolom timestamp yang dibaca lewat cursor agar
     * hemat memori.
     */
    private function rataRataResolusiJam(): float
    {
        $totalMenit = 0;
        $jumlah = 0;

        Ticket::query()
            ->whereNotNull('resolved_at')
            ->select(['created_at', 'resolved_at'])
            ->cursor()
            ->each(function (Ticket $ticket) use (&$totalMenit, &$jumlah): void {
                $totalMenit += (int) $ticket->created_at->diffInMinutes($ticket->resolved_at, true);
                $jumlah++;
            });

        return $jumlah > 0 ? round($totalMenit / $jumlah / 60, 1) : 0.0;
    }

    /**
     * @return array<int, array{nama: string, total: int, selesai: int, tepat_waktu: int, kepatuhan: float, melewati: int}>
     */
    private function perDepartemen(): array
    {
        $nama = Departemen::orderBy('nama')->pluck('nama', 'id');

        if ($nama->isEmpty()) {
            return [];
        }

        $total = $this->hitungPerDepartemen(Ticket::query());
        $selesai = $this->hitungPerDepartemen(Ticket::query()->whereIn('status', ['resolved', 'closed']));

        $denganTarget = $this->hitungPerDepartemen(
            Ticket::query()
                ->whereIn('status', ['resolved', 'closed'])
                ->whereNotNull('resolved_at')
                ->whereNotNull('sla_target_at')
        );

        $tepat = $this->hitungPerDepartemen(
            Ticket::query()
                ->whereIn('status', ['resolved', 'closed'])
                ->whereNotNull('resolved_at')
                ->whereNotNull('sla_target_at')
                ->whereColumn('resolved_at', '<=', 'sla_target_at')
        );

        $melewati = $this->hitungPerDepartemen(
            Ticket::query()
                ->whereNotIn('status', ['resolved', 'closed'])
                ->whereNotNull('sla_target_at')
                ->where('sla_target_at', '<', now())
        );

        $rows = [];

        foreach ($nama as $id => $label) {
            $jumlah = (int) ($total[$id] ?? 0);

            if ($jumlah === 0) {
                continue;
            }

            $base = (int) ($denganTarget[$id] ?? 0);
            $tepatId = (int) ($tepat[$id] ?? 0);

            $rows[] = [
                'nama' => $label,
                'total' => $jumlah,
                'selesai' => (int) ($selesai[$id] ?? 0),
                'tepat_waktu' => $tepatId,
                'kepatuhan' => $base > 0 ? round($tepatId / $base * 100, 1) : 0.0,
                'melewati' => (int) ($melewati[$id] ?? 0),
            ];
        }

        usort($rows, fn (array $a, array $b): int => $b['total'] <=> $a['total']);

        return $rows;
    }

    private function hitungPerDepartemen(Builder $query): Collection
    {
        return $query
            ->selectRaw('departemen_id, COUNT(*) AS jumlah')
            ->groupBy('departemen_id')
            ->pluck('jumlah', 'departemen_id');
    }

    /**
     * @param  array<string, string>  $labels
     * @return array<int, array{key: string, label: string, jumlah: int, persentase: float}>
     */
    private function distribusi(Collection $counts, array $labels, int $total): array
    {
        $rows = [];

        foreach ($labels as $key => $label) {
            $jumlah = (int) ($counts[$key] ?? 0);
            $rows[] = [
                'key' => $key,
                'label' => $label,
                'jumlah' => $jumlah,
                'persentase' => $total > 0 ? round($jumlah / $total * 100, 1) : 0.0,
            ];
        }

        // Nilai di luar daftar resmi tetap ditampilkan agar tidak ada data hilang.
        foreach ($counts as $key => $jumlah) {
            if (array_key_exists($key, $labels)) {
                continue;
            }

            $rows[] = [
                'key' => (string) $key,
                'label' => ucfirst((string) $key),
                'jumlah' => (int) $jumlah,
                'persentase' => $total > 0 ? round((int) $jumlah / $total * 100, 1) : 0.0,
            ];
        }

        return $rows;
    }
}

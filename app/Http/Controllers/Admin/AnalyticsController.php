<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\TicketReportExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class AnalyticsController extends Controller
{
    public function index()
    {
        $metrics = [
            'total_tiket' => DB::table('tickets')->count(),

            // Rata-rata waktu penyelesaian dalam jam, dioptimalkan pakai EXTRACT(EPOCH) khas Postgres
            'rata_rata_resolusi_jam' => round(
                DB::table('tickets')
                    ->whereNotNull('resolved_at')
                    ->selectRaw("AVG(EXTRACT(EPOCH FROM (resolved_at - created_at)) / 3600) as avg_jam")
                    ->value('avg_jam') ?? 0,
                1
            ),

            'melewati_sla' => DB::table('tickets')
                ->whereNotIn('status', ['resolved', 'closed'])
                ->where('sla_target_at', '<', now())
                ->count(),

            // Jumlah tiket per departemen
            'per_departemen' => DB::table('tickets')
                ->join('departemens', 'departemens.id', '=', 'tickets.departemen_id')
                ->select('departemens.nama', DB::raw('count(*) as jumlah'))
                ->groupBy('departemens.nama')
                ->orderByDesc('jumlah')
                ->get(),

            // Kepatuhan SLA per departemen (%)
            'kepatuhan_sla' => DB::table('tickets')
                ->join('departemens', 'departemens.id', '=', 'tickets.departemen_id')
                ->whereIn('tickets.status', ['resolved', 'closed'])
                ->select(
                    'departemens.nama',
                    DB::raw("COUNT(*) FILTER (WHERE resolved_at <= sla_target_at) as tepat_waktu"),
                    DB::raw("COUNT(*) as total")
                )
                ->groupBy('departemens.nama')
                ->get()
                ->map(fn ($row) => [
                    'nama' => $row->nama,
                    'persentase' => $row->total > 0 ? round(($row->tepat_waktu / $row->total) * 100, 1) : 0,
                ]),
        ];

        return view('admin.analytics', compact('metrics'));
    }

    public function export()
    {
        return Excel::download(new TicketReportExport, 'laporan-sigap-'.now()->format('Ymd').'.xlsx');
    }
}
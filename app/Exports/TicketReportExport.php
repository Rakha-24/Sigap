<?php

namespace App\Exports;

use App\Exports\Sheets\DataTiketSheet;
use App\Exports\Sheets\RingkasanSheet;
use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithProperties;
use PhpOffice\PhpSpreadsheet\Style\Style;

/**
 * Laporan Excel SIGAP: sheet "Ringkasan" (KPI + distribusi + grafik)
 * dan "Data Tiket" (tabel detail berwarna).
 */
class TicketReportExport implements Export, WithDefaultStyles, WithMultipleSheets, WithProperties
{
    public function sheets(): array
    {
        return [
            app(RingkasanSheet::class),
            app(DataTiketSheet::class),
        ];
    }

    public function properties(): array
    {
        return [
            'creator' => 'SIGAP Helpdesk',
            'lastModifiedBy' => 'SIGAP Helpdesk',
            'title' => 'Laporan Analitik Tiket SIGAP',
            'subject' => 'Rekapitulasi dan detail tiket',
            'description' => 'Laporan dibuat otomatis oleh SIGAP pada '.now()->format('d/m/Y H:i'),
            'category' => 'Laporan',
            'company' => config('app.name', 'SIGAP'),
        ];
    }

    public function defaultStyles(Style $defaultStyle): ?array
    {
        return [
            'font' => ['name' => 'Calibri', 'size' => 10, 'color' => ['rgb' => '0F172A']],
            'alignment' => ['vertical' => 'center'],
        ];
    }
}

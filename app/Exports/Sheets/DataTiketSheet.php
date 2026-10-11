<?php

namespace App\Exports\Sheets;

use App\Exports\Concerns\SigapReportStyle;
use App\Models\Ticket;
use App\Services\TicketReportMetrics;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithFreezePane;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTabColor;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Sheet detail: satu baris per tiket dengan pewarnaan status/prioritas.
 */
class DataTiketSheet implements FromQuery, WithColumnFormatting, WithColumnWidths, WithEvents, WithFreezePane, WithHeadings, WithMapping, WithStrictNullComparison, WithStyles, WithTabColor, WithTitle
{
    use SigapReportStyle;

    private const LAST_COL = 'N';

    private const DATE_FORMAT = 'DD-MM-YYYY HH:MM';

    private int $rowNumber = 0;

    public function title(): string
    {
        return 'Data Tiket';
    }

    public function tabColor(): string
    {
        return self::COLOR_BRAND_DARK;
    }

    public function query(): Builder
    {
        // Tie-breaker 'id' wajib: chunk() memakai LIMIT/OFFSET, sehingga urutan
        // tanpa kunci unik dapat membuat baris terlewat atau tercetak ganda.
        return Ticket::query()
            ->with(['departemen:id,nama', 'kategori:id,nama', 'pelapor:id,name', 'agent:id,name'])
            ->orderByDesc('created_at')
            ->orderByDesc('id');
    }

    public function headings(): array
    {
        return [
            'No', 'Nomor Tiket', 'Judul', 'Departemen', 'Kategori', 'Prioritas', 'Status',
            'Pelapor', 'Agen', 'Dibuat', 'Target SLA', 'Diselesaikan', 'Durasi (jam)', 'Status SLA',
        ];
    }

    public function map($ticket): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $ticket->nomor_tiket,
            $ticket->judul,
            $ticket->departemen?->nama ?? '-',
            $ticket->kategori?->nama ?? '-',
            TicketReportMetrics::PRIORITAS_LABELS[$ticket->prioritas] ?? ucfirst((string) $ticket->prioritas),
            TicketReportMetrics::STATUS_LABELS[$ticket->status] ?? ucfirst((string) $ticket->status),
            $ticket->nama_guest ?: ($ticket->pelapor?->name ?? '-'),
            $ticket->agent?->name ?? '-',
            $ticket->created_at,
            $ticket->sla_target_at ?? '-',
            $ticket->resolved_at ?? '-',
            $this->durasiJam($ticket),
            $this->statusSla($ticket),
        ];
    }

    private function durasiJam(Ticket $ticket): float|string
    {
        if (! $ticket->resolved_at) {
            return '-';
        }

        return round($ticket->created_at->diffInMinutes($ticket->resolved_at, true) / 60, 1);
    }

    private function statusSla(Ticket $ticket): string
    {
        if ($ticket->resolved_at && $ticket->sla_target_at) {
            return $ticket->resolved_at->lessThanOrEqualTo($ticket->sla_target_at) ? 'Tepat Waktu' : 'Terlambat';
        }

        if (in_array($ticket->status, ['resolved', 'closed'], true)) {
            return 'Selesai';
        }

        if ($ticket->sla_target_at?->isPast()) {
            return 'Melewati SLA';
        }

        return 'Dalam Proses';
    }

    public function columnFormats(): array
    {
        return [
            'J' => self::DATE_FORMAT,
            'K' => self::DATE_FORMAT,
            'L' => self::DATE_FORMAT,
            'M' => '0.0',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 18,
            'C' => 42,
            'D' => 20,
            'E' => 20,
            'F' => 11,
            'G' => 13,
            'H' => 22,
            'I' => 20,
            'J' => 17,
            'K' => 17,
            'L' => 17,
            'M' => 11,
            'N' => 15,
        ];
    }

    public function freezePane(): string
    {
        return 'C2';
    }

    public function styles(Worksheet $sheet): ?array
    {
        $last = self::LAST_COL;
        $lastRow = max($sheet->getHighestRow(), 1);

        $styles = ["A1:{$last}1" => $this->tableHeaderStyle()];

        if ($lastRow < 2) {
            return $styles;
        }

        for ($row = 2; $row <= $lastRow; $row++) {
            $styles["A{$row}:{$last}{$row}"] = [
                'fill' => $this->zebraFill($row),
                'borders' => $this->thinBorder(),
                'alignment' => ['vertical' => 'top'],
            ];

            foreach (['A', 'F', 'G', 'M', 'N'] as $col) {
                $styles["{$col}{$row}"] = ['alignment' => ['horizontal' => 'center', 'vertical' => 'top']];
            }

            $styles["C{$row}"] = ['alignment' => ['vertical' => 'top', 'wrapText' => true]];

            $styles["F{$row}"] = $this->cellTone($sheet, "F{$row}", 'priority');
            $styles["G{$row}"] = $this->cellTone($sheet, "G{$row}", 'status');
            $styles["N{$row}"] = $this->cellTone($sheet, "N{$row}", 'sla');
        }

        return $styles;
    }

    /**
     * Tentukan gaya sel berdasarkan nilainya, sekaligus mempertahankan border
     * dan perataan dari gaya baris.
     */
    private function cellTone(Worksheet $sheet, string $coordinate, string $jenis): array
    {
        $value = trim((string) $sheet->getCell($coordinate)->getValue());

        $tone = match ($jenis) {
            'priority' => $this->priorityTone($value),
            'status' => $this->statusTone($value),
            default => $this->slaTone($value),
        };

        return $tone + [
            'borders' => $this->thinBorder(),
            'alignment' => ['horizontal' => 'center', 'vertical' => 'top'],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $sheet = $event->sheet->getDelegate();
                $lastRow = max($sheet->getHighestRow(), 1);
                $last = self::LAST_COL;

                $sheet->setAutoFilter("A1:{$last}{$lastRow}");

                $sheet->getRowDimension(1)->setRowHeight(28);

                $pageSetup = $sheet->getPageSetup();
                $pageSetup->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
                $pageSetup->setPaperSize(PageSetup::PAPERSIZE_A4);
                $pageSetup->setFitToWidth(1);
                $pageSetup->setFitToHeight(0);
                $pageSetup->setRowsToRepeatAtTopByStartAndEnd(1, 1);
                $sheet->getPageMargins()->setTop(0.5)->setBottom(0.5)->setLeft(0.4)->setRight(0.4);
                $sheet->getPageSetup()->setPrintArea("A1:{$last}{$lastRow}");

                $sheet->getHeaderFooter()->setOddFooter('&LSIGAP · Data Tiket&C&P/&N&R'.now()->format('d/m/Y H:i'));
            },
        ];
    }
}

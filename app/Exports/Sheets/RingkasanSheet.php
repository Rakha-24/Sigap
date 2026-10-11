<?php

namespace App\Exports\Sheets;

use App\Exports\Concerns\SigapReportStyle;
use App\Services\TicketReportMetrics;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithCharts;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTabColor;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Sheet ringkasan: identitas laporan, kartu KPI, dan tabel distribusi.
 */
class RingkasanSheet implements FromArray, WithCharts, WithColumnWidths, WithEvents, WithStrictNullComparison, WithStyles, WithTabColor, WithTitle
{
    use SigapReportStyle;

    private const LAST_COL = 'F';

    private array $rows = [];

    private int $kpiSectionRow = 0;

    private int $kpiLabelRow = 0;

    private int $kpiValueRow = 0;

    private int $departemenSectionRow = 0;

    private int $departemenHeaderRow = 0;

    private int $departemenFirstRow = 0;

    private int $departemenLastRow = 0;

    private bool $departemenEmpty = false;

    private int $prioritasSectionRow = 0;

    private int $prioritasHeaderRow = 0;

    private int $prioritasFirstRow = 0;

    private int $prioritasLastRow = 0;

    private int $statusSectionRow = 0;

    private int $statusHeaderRow = 0;

    private int $statusFirstRow = 0;

    private int $statusLastRow = 0;

    private int $lastRow = 1;

    /** @var int[] */
    private array $sectionRows = [];

    /** @var int[] */
    private array $tableHeaderRows = [];

    public function __construct(private TicketReportMetrics $metrics)
    {
        $this->build();
    }

    public function title(): string
    {
        return 'Ringkasan';
    }

    public function tabColor(): string
    {
        return self::COLOR_BRAND;
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 24,
            'B' => 14,
            'C' => 14,
            'D' => 18,
            'E' => 15,
            'F' => 15,
            'G' => 3,
        ];
    }

    private function push(array $cells): int
    {
        $this->rows[] = $cells;

        return $this->lastRow = count($this->rows);
    }

    private function build(): void
    {
        $m = $this->metrics->summary();
        $now = now();

        $blank = ['', '', '', '', '', ''];

        // ── Identitas laporan ────────────────────────────────────────────
        $this->push(['SIGAP', '', '', '', '', '']);
        $this->push(['Laporan Analitik & Rekapitulasi Tiket', '', '', '', '', '']);
        $this->push([
            'Dibuat '.$now->format('d/m/Y H:i').' '.config('app.timezone').' · '.number_format($m['total'], 0, ',', '.').' tiket',
            '', '', '', '', '',
        ]);
        $this->push($blank);

        // ── Kartu KPI ────────────────────────────────────────────────────
        $this->kpiSectionRow = $this->push(['RINGKASAN UTAMA', '', '', '', '', '']);
        $this->sectionRows[] = $this->kpiSectionRow;
        $this->kpiLabelRow = $this->push([
            'TOTAL TIKET', 'TIKET AKTIF', 'SELESAI', 'RATA-RATA RESOLUSI', 'KEPATUHAN SLA', 'MELEWATI SLA',
        ]);
        $this->kpiValueRow = $this->push([
            $m['total'], $m['aktif'], $m['selesai'], $m['rata_resolusi_jam'], $m['kepatuhan_sla'], $m['melewati_sla'],
        ]);
        $this->push($blank);

        // ── Distribusi per departemen ────────────────────────────────────
        $this->departemenSectionRow = $this->push(['DISTRIBUSI PER DEPARTEMEN', '', '', '', '', '']);
        $this->sectionRows[] = $this->departemenSectionRow;
        $this->departemenHeaderRow = $this->push([
            'Departemen', 'Total', 'Selesai', 'Tepat Waktu', 'Kepatuhan SLA', 'Melewati SLA',
        ]);
        $this->tableHeaderRows[] = $this->departemenHeaderRow;
        $this->departemenFirstRow = count($this->rows) + 1;

        if ($m['per_departemen'] === []) {
            $this->departemenEmpty = true;
            $this->push(['Belum ada data tiket.', '', '', '', '', '']);
        } else {
            foreach ($m['per_departemen'] as $d) {
                $this->push([$d['nama'], $d['total'], $d['selesai'], $d['tepat_waktu'], $d['kepatuhan'], $d['melewati']]);
            }
        }

        $this->departemenLastRow = count($this->rows);
        $this->push($blank);

        // ── Distribusi prioritas ─────────────────────────────────────────
        $this->prioritasSectionRow = $this->push(['DISTRIBUSI PRIORITAS', '', '', '', '', '']);
        $this->sectionRows[] = $this->prioritasSectionRow;
        $this->prioritasHeaderRow = $this->push(['Prioritas', 'Jumlah', 'Persentase', '', '', '']);
        $this->tableHeaderRows[] = $this->prioritasHeaderRow;
        $this->prioritasFirstRow = count($this->rows) + 1;

        foreach ($m['per_prioritas'] as $p) {
            $this->push([$p['label'], $p['jumlah'], $p['persentase'], '', '', '']);
        }

        $this->prioritasLastRow = count($this->rows);
        $this->push($blank);

        // ── Distribusi status ────────────────────────────────────────────
        $this->statusSectionRow = $this->push(['DISTRIBUSI STATUS', '', '', '', '', '']);
        $this->sectionRows[] = $this->statusSectionRow;
        $this->statusHeaderRow = $this->push(['Status', 'Jumlah', 'Persentase', '', '', '']);
        $this->tableHeaderRows[] = $this->statusHeaderRow;
        $this->statusFirstRow = count($this->rows) + 1;

        foreach ($m['per_status'] as $s) {
            $this->push([$s['label'], $s['jumlah'], $s['persentase'], '', '', '']);
        }

        $this->statusLastRow = count($this->rows);
    }

    public function charts(): Chart|array
    {
        if ($this->departemenEmpty) {
            return [];
        }

        $count = $this->departemenLastRow - $this->departemenFirstRow + 1;

        $categories = new DataSeriesValues(
            DataSeriesValues::DATASERIES_TYPE_STRING,
            "'Ringkasan'!\$A\${$this->departemenFirstRow}:\$A\${$this->departemenLastRow}",
            null,
            $count
        );

        $values = new DataSeriesValues(
            DataSeriesValues::DATASERIES_TYPE_NUMBER,
            "'Ringkasan'!\$B\${$this->departemenFirstRow}:\$B\${$this->departemenLastRow}",
            null,
            $count
        );
        $values->setFillColor(self::COLOR_BRAND);

        $series = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_CLUSTERED,
            [0],
            [],
            [$categories],
            [$values]
        );
        $series->setPlotDirection(DataSeries::DIRECTION_COL);

        $chart = new Chart(
            'total_tiket_per_departemen',
            new Title('Total Tiket per Departemen'),
            null,
            new PlotArea(null, [$series])
        );
        $chart->setTopLeftPosition('H'.$this->departemenSectionRow);
        $chart->setBottomRightPosition('O'.($this->departemenSectionRow + 15));

        return $chart;
    }

    public function styles(Worksheet $sheet): ?array
    {
        $last = self::LAST_COL;
        $styles = [];

        $styles["A1:{$last}1"] = $this->brandTitleStyle();
        $styles["A2:{$last}2"] = $this->subtitleStyle();
        $styles["A3:{$last}3"] = $this->metaStyle();

        foreach ($this->sectionRows as $row) {
            $styles["A{$row}:{$last}{$row}"] = $this->sectionHeaderStyle();
        }

        foreach ($this->tableHeaderRows as $row) {
            $styles["A{$row}:{$last}{$row}"] = $this->tableHeaderStyle();
        }

        // Kartu KPI
        $styles["A{$this->kpiLabelRow}:{$last}{$this->kpiLabelRow}"] = $this->kpiLabelStyle();
        $styles["A{$this->kpiValueRow}:{$last}{$this->kpiValueRow}"] = $this->kpiValueStyle();
        $styles["D{$this->kpiValueRow}"] = ['numberFormat' => ['formatCode' => '0.0" jam"']];
        $styles["E{$this->kpiValueRow}"] = ['numberFormat' => ['formatCode' => '0.0"%"']];
        $styles["F{$this->kpiValueRow}"] = ['numberFormat' => ['formatCode' => '0']];

        // Tabel departemen
        $this->styleDataRows($styles, $this->departemenFirstRow, $this->departemenLastRow, $this->departemenEmpty);
        for ($row = $this->departemenFirstRow; $row <= $this->departemenLastRow; $row++) {
            $styles["E{$row}"] = ['numberFormat' => ['formatCode' => '0.0"%"']];
        }

        // Tabel prioritas
        $this->styleDistributionRows($styles, $this->prioritasFirstRow, $this->prioritasLastRow, true);
        // Tabel status
        $this->styleDistributionRows($styles, $this->statusFirstRow, $this->statusLastRow, false);

        return $styles;
    }

    private function styleDataRows(array &$styles, int $first, int $last, bool $empty): void
    {
        for ($row = $first; $row <= $last; $row++) {
            $style = [
                'fill' => $empty ? $this->solidFill(self::COLOR_WHITE) : $this->zebraFill($row),
                'borders' => $this->thinBorder(),
                'alignment' => ['vertical' => 'center'],
            ];

            if ($empty) {
                $style['font'] = ['italic' => true, 'color' => ['rgb' => self::COLOR_MUTED]];
                $style['alignment']['horizontal'] = 'center';
            }

            $styles["A{$row}:F{$row}"] = $style;

            if (! $empty) {
                foreach (['B', 'C', 'D', 'E', 'F'] as $col) {
                    $styles["{$col}{$row}"] = ['alignment' => ['horizontal' => 'center', 'vertical' => 'center']];
                }
            }
        }
    }

    private function styleDistributionRows(array &$styles, int $first, int $last, bool $isPrioritas): void
    {
        for ($row = $first; $row <= $last; $row++) {
            $label = (string) ($this->rows[$row - 1][0] ?? '');
            $tone = $isPrioritas ? $this->priorityTone($label) : $this->statusTone($label);

            $styles["A{$row}:C{$row}"] = [
                'fill' => $this->zebraFill($row),
                'borders' => $this->thinBorder(),
                'alignment' => ['vertical' => 'center'],
            ];
            $styles["A{$row}"] = $tone + [
                'borders' => $this->thinBorder(),
                'alignment' => ['horizontal' => 'left', 'vertical' => 'center'],
            ];
            $styles["B{$row}"] = ['alignment' => ['horizontal' => 'center', 'vertical' => 'center']];
            $styles["C{$row}"] = [
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                'numberFormat' => ['formatCode' => '0.0"%"'],
            ];
        }
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $sheet = $event->sheet->getDelegate();
                $last = self::LAST_COL;

                foreach ([1, 2, 3] as $row) {
                    if ($row <= $this->lastRow) {
                        $sheet->mergeCells("A{$row}:{$last}{$row}");
                    }
                }

                foreach ($this->sectionRows as $row) {
                    $sheet->mergeCells("A{$row}:{$last}{$row}");
                }

                if ($this->departemenEmpty && $this->departemenFirstRow <= $this->departemenLastRow) {
                    $sheet->mergeCells("A{$this->departemenFirstRow}:{$last}{$this->departemenFirstRow}");
                }

                $sheet->getRowDimension(1)->setRowHeight(36);
                $sheet->getRowDimension(2)->setRowHeight(22);
                $sheet->getRowDimension(3)->setRowHeight(18);
                $sheet->getRowDimension($this->kpiLabelRow)->setRowHeight(30);
                $sheet->getRowDimension($this->kpiValueRow)->setRowHeight(30);

                foreach ($this->sectionRows as $row) {
                    $sheet->getRowDimension($row)->setRowHeight(22);
                }

                foreach ($this->tableHeaderRows as $row) {
                    $sheet->getRowDimension($row)->setRowHeight(26);
                }

                $pageSetup = $sheet->getPageSetup();
                $pageSetup->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
                $pageSetup->setPaperSize(PageSetup::PAPERSIZE_A4);
                $pageSetup->setFitToWidth(1);
                $pageSetup->setFitToHeight(0);
                $sheet->getPageMargins()->setTop(0.5)->setBottom(0.5)->setLeft(0.4)->setRight(0.4);
                $sheet->getPageSetup()->setPrintArea("A1:{$last}{$this->lastRow}");

                $sheet->getHeaderFooter()->setOddFooter('&LSIGAP · Laporan Tiket&C&P/&N&RSIGAP');
            },
        ];
    }
}

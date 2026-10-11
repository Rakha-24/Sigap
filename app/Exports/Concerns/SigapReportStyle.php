<?php

namespace App\Exports\Concerns;

use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

/**
 * Palet dan gaya bersama untuk seluruh sheet laporan Excel SIGAP.
 *
 * Warna diselaraskan dengan tema aplikasi: brand sigap-600 (#0369A1),
 * dan warna badge status/prioritas di resources/css/app.css sehingga
 * laporan yang dicetak terlihat konsisten dengan tampilan web.
 */
trait SigapReportStyle
{
    private const COLOR_BRAND_DARK = '0C3E5C';

    private const COLOR_BRAND = '0369A1';

    private const COLOR_BRAND_SOFT = 'EFF8FC';

    private const COLOR_BRAND_TINT = 'DCEEF9';

    private const COLOR_BORDER = 'E2E8F0';

    private const COLOR_TEXT = '0F172A';

    private const COLOR_MUTED = '64748B';

    private const COLOR_SURFACE = 'F8FAFC';

    private const COLOR_WHITE = 'FFFFFF';

    private const COLOR_GREEN = '047857';

    private const COLOR_GREEN_SOFT = 'ECFDF5';

    private const COLOR_AMBER = 'B45309';

    private const COLOR_AMBER_SOFT = 'FFFBEB';

    private const COLOR_RED = 'B91C1C';

    private const COLOR_RED_SOFT = 'FEF2F2';

    private const COLOR_SLATE = '475569';

    private const COLOR_SLATE_SOFT = 'F1F5F9';

    protected function solidFill(string $rgb): array
    {
        return [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => $rgb],
        ];
    }

    /**
     * Border tipis di keempat sisi, dipakai untuk garis tabel.
     */
    protected function thinBorder(string $rgb = self::COLOR_BORDER): array
    {
        $side = ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => $rgb]];

        return ['top' => $side, 'bottom' => $side, 'left' => $side, 'right' => $side];
    }

    protected function brandTitleStyle(): array
    {
        return [
            'font' => ['bold' => true, 'size' => 20, 'color' => ['rgb' => self::COLOR_WHITE]],
            'fill' => $this->solidFill(self::COLOR_BRAND_DARK),
            'alignment' => ['horizontal' => 'left', 'vertical' => 'center', 'indent' => 2],
        ];
    }

    protected function subtitleStyle(): array
    {
        return [
            'font' => ['size' => 12, 'bold' => true, 'color' => ['rgb' => self::COLOR_WHITE]],
            'fill' => $this->solidFill(self::COLOR_BRAND),
            'alignment' => ['horizontal' => 'left', 'vertical' => 'center', 'indent' => 2],
        ];
    }

    protected function metaStyle(): array
    {
        return [
            'font' => ['size' => 9, 'italic' => true, 'color' => ['rgb' => self::COLOR_MUTED]],
            'alignment' => ['horizontal' => 'left', 'vertical' => 'center', 'indent' => 2],
        ];
    }

    protected function sectionHeaderStyle(): array
    {
        return [
            'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => self::COLOR_BRAND_DARK]],
            'fill' => $this->solidFill(self::COLOR_BRAND_TINT),
            'alignment' => ['horizontal' => 'left', 'vertical' => 'center', 'indent' => 2],
            'borders' => [
                'bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::COLOR_BRAND]],
            ],
        ];
    }

    protected function tableHeaderStyle(): array
    {
        return [
            'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => self::COLOR_WHITE]],
            'fill' => $this->solidFill(self::COLOR_BRAND),
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true],
            'borders' => $this->thinBorder(self::COLOR_BRAND_DARK),
        ];
    }

    protected function kpiLabelStyle(): array
    {
        return [
            'font' => ['bold' => true, 'size' => 8, 'color' => ['rgb' => self::COLOR_MUTED]],
            'fill' => $this->solidFill(self::COLOR_BRAND_SOFT),
            'alignment' => ['horizontal' => 'left', 'vertical' => 'bottom', 'wrapText' => true, 'indent' => 1],
            'borders' => [
                'top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::COLOR_BRAND_TINT]],
                'left' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::COLOR_BRAND_TINT]],
                'right' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::COLOR_BRAND_TINT]],
            ],
        ];
    }

    protected function kpiValueStyle(): array
    {
        return [
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => self::COLOR_BRAND_DARK]],
            'fill' => $this->solidFill(self::COLOR_WHITE),
            'alignment' => ['horizontal' => 'left', 'vertical' => 'top', 'indent' => 1],
            'borders' => [
                'bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::COLOR_BRAND_TINT]],
                'left' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::COLOR_BRAND_TINT]],
                'right' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::COLOR_BRAND_TINT]],
            ],
        ];
    }

    protected function zebraFill(int $row): array
    {
        return $this->solidFill($row % 2 === 0 ? self::COLOR_SURFACE : self::COLOR_WHITE);
    }

    /**
     * Warna font + latar untuk sebuah pasangan nada (tone).
     */
    protected function tone(string $font, string $fill): array
    {
        return [
            'font' => ['bold' => true, 'color' => ['rgb' => $font]],
            'fill' => $this->solidFill($fill),
        ];
    }

    protected function neutralTone(): array
    {
        return $this->tone(self::COLOR_MUTED, self::COLOR_SURFACE);
    }

    /**
     * Nada warna status tiket. Menerima key (open) maupun label (Menunggu).
     */
    protected function statusTone(string $value): array
    {
        $key = match (strtolower(trim($value))) {
            'menunggu', 'open' => 'open',
            'diproses', 'in_progress', 'in progress' => 'in_progress',
            'selesai', 'resolved' => 'resolved',
            'ditutup', 'closed' => 'closed',
            default => 'closed',
        };

        return match ($key) {
            'open' => $this->tone(self::COLOR_BRAND, self::COLOR_BRAND_SOFT),
            'in_progress' => $this->tone(self::COLOR_AMBER, self::COLOR_AMBER_SOFT),
            'resolved' => $this->tone(self::COLOR_GREEN, self::COLOR_GREEN_SOFT),
            default => $this->tone(self::COLOR_SLATE, self::COLOR_SLATE_SOFT),
        };
    }

    protected function priorityTone(string $value): array
    {
        return match (strtolower(trim($value))) {
            'tinggi' => $this->tone(self::COLOR_RED, self::COLOR_RED_SOFT),
            'sedang' => $this->tone(self::COLOR_AMBER, self::COLOR_AMBER_SOFT),
            'rendah' => $this->tone(self::COLOR_GREEN, self::COLOR_GREEN_SOFT),
            default => $this->neutralTone(),
        };
    }

    protected function slaTone(string $value): array
    {
        $v = strtolower(trim($value));

        return match (true) {
            str_contains($v, 'tepat') => $this->tone(self::COLOR_GREEN, self::COLOR_GREEN_SOFT),
            str_contains($v, 'terlambat'), str_contains($v, 'melewati') => $this->tone(self::COLOR_RED, self::COLOR_RED_SOFT),
            str_contains($v, 'proses'), str_contains($v, 'berjalan') => $this->tone(self::COLOR_AMBER, self::COLOR_AMBER_SOFT),
            default => $this->neutralTone(),
        };
    }
}

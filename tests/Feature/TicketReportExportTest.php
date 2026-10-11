<?php

namespace Tests\Feature;

use App\Exports\TicketReportExport;
use App\Models\Departemen;
use App\Models\Kategori;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Excel as ExcelWriter;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Tests\TestCase;

class TicketReportExportTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Departemen $departemen;

    private Kategori $kategori;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->departemen = Departemen::create(['kode' => 'DEPT-EXP', 'nama' => 'Dinas Operasional']);
        $this->kategori = Kategori::create([
            'departemen_id' => $this->departemen->id,
            'nama' => 'Jaringan',
            'default_sla_jam' => 24,
        ]);
    }

    private function tiket(string $status, array $atribut = []): Ticket
    {
        // created_at bukan kolom mass-assignable, set setelah create().
        $createdAt = $atribut['created_at'] ?? null;
        unset($atribut['created_at']);

        $ticket = Ticket::create(array_merge([
            'nomor_tiket' => 'TKT-'.strtoupper(substr(sha1(uniqid('', true)), 0, 6)),
            'departemen_id' => $this->departemen->id,
            'kategori_id' => $this->kategori->id,
            'nama_guest' => 'Pelapor Uji',
            'kontak_guest' => 'pelapor@mail.com',
            'judul' => 'Judul '.$status,
            'deskripsi' => 'Deskripsi tiket uji.',
            'prioritas' => 'sedang',
            'status' => $status,
        ], $atribut));

        if ($createdAt !== null) {
            $ticket->created_at = $createdAt;
            $ticket->save();
        }

        return $ticket->refresh();
    }

    /**
     * Data uji dengan angka yang deterministik:
     * total 6, aktif 3, selesai 3, tepat waktu 2/3, melewati SLA 1,
     * rata-rata resolusi (10 + 20 + 30) / 3 = 20 jam.
     */
    private function seedTiket(): void
    {
        $this->tiket('open', ['sla_target_at' => now()->addHours(24)]);
        $this->tiket('in_progress', ['sla_target_at' => now()->addHours(24)]);

        $this->tiket('resolved', [
            'created_at' => now()->subHours(10),
            'resolved_at' => now()->subHours(0),
            'sla_target_at' => now()->addHours(2),
        ]);
        $this->tiket('resolved', [
            'created_at' => now()->subHours(30),
            'resolved_at' => now()->subHours(10),
            'sla_target_at' => now()->subHours(20),
        ]);
        $this->tiket('closed', [
            'created_at' => now()->subHours(40),
            'resolved_at' => now()->subHours(10),
            'sla_target_at' => now()->subHours(5),
        ]);

        $this->tiket('open', ['sla_target_at' => now()->subHours(3)]);
    }

    private function barisDenganLabel(Worksheet $sheet, string $label): ?int
    {
        foreach ($sheet->getRowIterator() as $row) {
            if ($sheet->getCell('A'.$row->getRowIndex())->getValue() === $label) {
                return $row->getRowIndex();
            }
        }

        return null;
    }

    private function workbook(): Spreadsheet
    {
        $raw = Excel::raw(new TicketReportExport, ExcelWriter::XLSX);

        $path = tempnam(sys_get_temp_dir(), 'sigap-report').'.xlsx';
        file_put_contents($path, $raw);

        try {
            $reader = IOFactory::createReader('Xlsx');
            $reader->setIncludeCharts(true);

            return $reader->load($path);
        } finally {
            @unlink($path);
        }
    }

    public function test_laporan_menghasilkan_sheet_ringkasan_dan_data_tiket(): void
    {
        $this->seedTiket();

        $spreadsheet = $this->workbook();

        $this->assertSame(2, $spreadsheet->getSheetCount());
        $this->assertSame('Ringkasan', $spreadsheet->getSheet(0)->getTitle());
        $this->assertSame('Data Tiket', $spreadsheet->getSheet(1)->getTitle());
    }

    public function test_sheet_ringkasan_memuat_metrik_utama(): void
    {
        $this->seedTiket();

        $sheet = $this->workbook()->getSheet(0);

        $this->assertEquals(6, $sheet->getCell('A7')->getValue());
        $this->assertEquals(3, $sheet->getCell('B7')->getValue());
        $this->assertEquals(3, $sheet->getCell('C7')->getValue());
        $this->assertEqualsWithDelta(20.0, $sheet->getCell('D7')->getValue(), 0.05);
        $this->assertEqualsWithDelta(66.7, $sheet->getCell('E7')->getValue(), 0.05);
        $this->assertEquals(1, $sheet->getCell('F7')->getValue());
    }

    public function test_sheet_ringkasan_memuat_tabel_distribusi_dan_grafik(): void
    {
        $this->seedTiket();

        $sheet = $this->workbook()->getSheet(0);

        $this->assertSame('DISTRIBUSI PER DEPARTEMEN', $sheet->getCell('A9')->getValue());
        $this->assertSame('Dinas Operasional', $sheet->getCell('A11')->getValue());
        $this->assertEquals(6, $sheet->getCell('B11')->getValue());

        $this->assertGreaterThanOrEqual(1, $sheet->getChartCollection()->count());
    }

    public function test_sheet_data_tiket_memuat_header_dan_seluruh_baris(): void
    {
        $this->seedTiket();

        $sheet = $this->workbook()->getSheet(1);

        $this->assertSame('Nomor Tiket', $sheet->getCell('B1')->getValue());
        $this->assertSame('Status SLA', $sheet->getCell('N1')->getValue());

        // 1 baris header + 6 baris tiket
        $this->assertSame(7, $sheet->getHighestRow());
        $this->assertNotNull($sheet->getAutoFilter()->getRange());
        $this->assertSame('C2', $sheet->getFreezePane());
    }

    public function test_admin_dapat_mengunduh_laporan_excel(): void
    {
        $this->seedTiket();

        $this->actingAs($this->admin)
            ->get(route('admin.analytics.export'))
            ->assertOk()
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->assertDownload();
    }

    public function test_non_admin_tidak_dapat_mengunduh_laporan(): void
    {
        $agent = User::factory()->create([
            'role' => 'agent',
            'departemen_id' => $this->departemen->id,
        ]);

        $this->actingAs($agent)
            ->get(route('admin.analytics.export'))
            ->assertForbidden();
    }

    public function test_laporan_tetap_dihasilkan_saat_belum_ada_tiket(): void
    {
        $spreadsheet = $this->workbook();

        $sheet = $spreadsheet->getSheet(0);

        $this->assertSame(0, $sheet->getCell('A7')->getValue());
        $this->assertSame('Belum ada data tiket.', $sheet->getCell('A11')->getValue());
        $this->assertSame(1, $spreadsheet->getSheet(1)->getHighestRow());
    }

    /**
     * Regresi: nilai 0 pada tabel distribusi tidak boleh hilang.
     *
     * maatwebsite memakai perbandingan longgar (0 == null), sehingga angka 0
     * sempat terlewat saat menulis baris dan sel menjadi kosong.
     */
    public function test_tabel_distribusi_tetap_menulis_nilai_nol(): void
    {
        $this->tiket('open', ['sla_target_at' => now()->addHours(24)]);

        $sheet = $this->workbook()->getSheet(0);

        $harapan = ['Menunggu' => 1, 'Diproses' => 0, 'Selesai' => 0, 'Ditutup' => 0];

        foreach ($harapan as $label => $jumlah) {
            $baris = $this->barisDenganLabel($sheet, $label);

            $this->assertNotNull($baris, "Baris distribusi status '{$label}' tidak ditemukan.");
            $this->assertSame(
                $jumlah,
                $sheet->getCell('B'.$baris)->getValue(),
                "Jumlah status '{$label}' seharusnya tertulis {$jumlah}, bukan sel kosong."
            );
        }
    }
}

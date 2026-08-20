<?php

namespace Tests\Feature;

use App\Models\Departemen;
use App\Models\Kategori;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTicketListTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Departemen $departemen;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->departemen = Departemen::create([
            'kode' => 'DEPT-LIST',
            'nama' => 'Departemen List',
        ]);
        $this->kategori = Kategori::create([
            'departemen_id' => $this->departemen->id,
            'nama' => 'Kategori List',
            'default_sla_jam' => 24,
        ]);
    }

    private function buatTiket(string $status): Ticket
    {
        return Ticket::create([
            'nomor_tiket' => 'TKT-'.strtoupper(substr(sha1(uniqid()), 0, 5)),
            'departemen_id' => $this->departemen->id,
            'kategori_id' => $this->kategori->id,
            'nama_guest' => 'Pelapor Uji',
            'kontak_guest' => 'pelapor@mail.com',
            'judul' => 'Tiket status '.$status,
            'deskripsi' => 'Deskripsi tiket uji.',
            'prioritas' => 'sedang',
            'status' => $status,
            'sla_target_at' => now()->addHours(24),
        ]);
    }

    public function test_admin_melihat_tiket_belum_selesai_secara_default(): void
    {
        $ini = $this->buatTiket('open');
        $proses = $this->buatTiket('in_progress');
        $selesai = $this->buatTiket('resolved');

        $response = $this->actingAs($this->admin)
            ->get(route('admin.tickets.index'))
            ->assertOk();

        $response->assertSee($ini->nomor_tiket);
        $response->assertSee($proses->nomor_tiket);
        $response->assertDontSee($selesai->nomor_tiket);
    }

    public function test_admin_melihat_tiket_selesai_saat_filter_selesai(): void
    {
        $selesai = $this->buatTiket('resolved');
        $ditutup = $this->buatTiket('closed');
        $belum = $this->buatTiket('open');

        $response = $this->actingAs($this->admin)
            ->get(route('admin.tickets.index', ['status' => 'selesai']))
            ->assertOk();

        $response->assertSee($selesai->nomor_tiket);
        $response->assertSee($ditutup->nomor_tiket);
        $response->assertDontSee($belum->nomor_tiket);
    }

    public function test_admin_dapat_mencari_tiket_berdasarkan_nomor(): void
    {
        $tiket = $this->buatTiket('open');

        $this->actingAs($this->admin)
            ->get(route('admin.tickets.index', ['status' => 'belum', 'cari' => $tiket->nomor_tiket]))
            ->assertOk()
            ->assertSee($tiket->nomor_tiket);
    }

    public function test_non_admin_tidak_bisa_mengakses_daftar_tiket_admin(): void
    {
        $agent = User::factory()->create([
            'role' => 'agent',
            'departemen_id' => $this->departemen->id,
        ]);

        $this->actingAs($agent)
            ->get(route('admin.tickets.index'))
            ->assertForbidden();
    }
}

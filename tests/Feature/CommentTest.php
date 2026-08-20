<?php

namespace Tests\Feature;

use App\Models\Departemen;
use App\Models\Kategori;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    private Departemen $departemen;

    private Kategori $kategori;

    protected function setUp(): void
    {
        parent::setUp();

        $this->departemen = Departemen::create([
            'kode' => 'DEPT-COMENT',
            'nama' => 'Departemen Komentar',
        ]);

        $this->kategori = Kategori::create([
            'departemen_id' => $this->departemen->id,
            'nama' => 'Kategori Komentar',
            'default_sla_jam' => 24,
        ]);
    }

    private function buatTiket(?User $pelapor = null): Ticket
    {
        return Ticket::create([
            'nomor_tiket' => 'TKT-CMT-'.strtoupper(substr(sha1(uniqid()), 0, 5)),
            'departemen_id' => $this->departemen->id,
            'kategori_id' => $this->kategori->id,
            'id_pelapor' => $pelapor?->id,
            'nama_guest' => $pelapor ? null : 'Pelapor Publik',
            'kontak_guest' => $pelapor ? null : 'publik@mail.com',
            'judul' => 'Tiket uji komentar',
            'deskripsi' => 'Deskripsi tiket uji komentar.',
            'prioritas' => 'sedang',
            'status' => 'open',
            'sla_target_at' => now()->addHours(24),
        ]);
    }

    public function test_pelapor_dapat_berkomentar_publik_pada_tiketnya(): void
    {
        $pelapor = User::factory()->create(['role' => 'user']);
        $tiket = $this->buatTiket($pelapor);

        $response = $this->actingAs($pelapor)
            ->post(route('tickets.comments.store', $tiket), [
                'pesan' => 'Mohon info perkembangannya.',
            ]);

        $response->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('comments', [
            'ticket_id' => $tiket->id,
            'user_id' => $pelapor->id,
            'is_internal' => false,
            'pesan' => 'Mohon info perkembangannya.',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'ticket_id' => $tiket->id,
            'aksi' => 'comment_added',
        ]);
    }

    public function test_komentar_agent_terhadap_tiket_internal_bersifat_internal(): void
    {
        $pelapor = User::factory()->create(['role' => 'user']);
        $agent = User::factory()->create([
            'role' => 'agent',
            'departemen_id' => $this->departemen->id,
        ]);

        $tiket = $this->buatTiket($pelapor);

        $this->actingAs($agent)
            ->post(route('tickets.comments.store', $tiket), [
                'pesan' => 'Kami sedang menindaklanjuti.',
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('comments', [
            'ticket_id' => $tiket->id,
            'is_internal' => true,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'ticket_id' => $tiket->id,
            'aksi' => 'internal_note_added',
        ]);
    }

    public function test_balasan_agent_terhadap_tiket_guest_bersifat_publik(): void
    {
        $agent = User::factory()->create([
            'role' => 'agent',
            'departemen_id' => $this->departemen->id,
        ]);

        $tiket = $this->buatTiket(); // tiket guest (id_pelapor null)

        $this->actingAs($agent)
            ->post(route('tickets.comments.store', $tiket), [
                'pesan' => 'Terima kasih atas laporannya, akan segera kami proses.',
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('comments', [
            'ticket_id' => $tiket->id,
            'is_internal' => false,
        ]);

        // Harus muncul di halaman pelacakan publik
        $this->get(route('guest.track.show', $tiket->nomor_tiket))
            ->assertOk()
            ->assertSee('Terima kasih atas laporannya');
    }

    public function test_user_lain_tidak_bisa_berkomentar_pada_tiket_bukan_miliknya(): void
    {
        $pelapor = User::factory()->create(['role' => 'user']);
        $orangLain = User::factory()->create(['role' => 'user']);
        $tiket = $this->buatTiket($pelapor);

        $this->actingAs($orangLain)
            ->post(route('tickets.comments.store', $tiket), [
                'pesan' => 'Saya coba iseng.',
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('comments', 0);
    }

    public function test_pesan_kosong_ditolak(): void
    {
        $pelapor = User::factory()->create(['role' => 'user']);
        $tiket = $this->buatTiket($pelapor);

        $this->actingAs($pelapor)
            ->post(route('tickets.comments.store', $tiket), ['pesan' => ''])
            ->assertSessionHasErrors('pesan');

        $this->assertDatabaseCount('comments', 0);
    }
}

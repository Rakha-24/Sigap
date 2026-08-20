<?php

namespace Tests\Feature;

use App\Models\Departemen;
use App\Models\Kategori;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketCreationTest extends TestCase
{
    use RefreshDatabase;

    private Departemen $departemen;

    private Kategori $kategori;

    protected function setUp(): void
    {
        parent::setUp();

        $this->departemen = Departemen::create([
            'kode' => 'DEPT-TEST',
            'nama' => 'Departemen Uji',
        ]);

        $this->kategori = Kategori::create([
            'departemen_id' => $this->departemen->id,
            'nama' => 'Kategori Uji',
            'default_sla_jam' => 24,
        ]);
    }

    public function test_guest_dapat_membuat_tiket_dan_diredirect_ke_lacak(): void
    {
        $response = $this->post('/lapor', [
            'nama_guest' => 'Pengunjung',
            'kontak_guest' => 'pengunjung@mail.com',
            'departemen_id' => $this->departemen->id,
            'kategori_id' => $this->kategori->id,
            'judul' => 'Masalah uji',
            'deskripsi' => 'Deskripsi masalah uji.',
            'prioritas' => 'sedang',
        ])->assertSessionHasNoErrors();

        $this->assertSame(302, $response->getStatusCode());

        $tiket = Ticket::first();
        $this->assertNotNull($tiket);
        $this->assertTrue(str_starts_with($tiket->nomor_tiket, 'TKT-'));
        $this->assertSame('open', $tiket->status);
        $this->assertSame('Pengunjung', $tiket->nama_guest);

        $this->assertStringEndsWith(
            route('guest.track.show', $tiket->nomor_tiket),
            $response->headers->get('Location')
        );
    }

    public function test_guest_ditolak_saat_kategori_tidak_cocok_dengan_departemen(): void
    {
        $departemenLain = Departemen::create([
            'kode' => 'DEPT-LAIN',
            'nama' => 'Departemen Lain',
        ]);

        $response = $this->post('/lapor', [
            'nama_guest' => 'Pengunjung',
            'kontak_guest' => 'pengunjung@mail.com',
            'departemen_id' => $departemenLain->id,
            'kategori_id' => $this->kategori->id,
            'judul' => 'Masalah uji',
            'deskripsi' => 'Deskripsi masalah uji.',
            'prioritas' => 'sedang',
        ]);

        $response->assertSessionHasErrors('kategori_id');
        $this->assertDatabaseCount('tickets', 0);
    }

    public function test_guest_track_dengan_nomor_valid_mengarah_ke_halaman_hasil(): void
    {
        $this->post('/lapor', [
            'nama_guest' => 'Pengunjung',
            'kontak_guest' => 'pengunjung@mail.com',
            'departemen_id' => $this->departemen->id,
            'kategori_id' => $this->kategori->id,
            'judul' => 'Masalah uji',
            'deskripsi' => 'Deskripsi masalah uji.',
            'prioritas' => 'sedang',
        ]);

        $nomor = Ticket::first()->nomor_tiket;

        $this->get(route('guest.track.form', ['nomor_tiket' => $nomor]))
            ->assertRedirect(route('guest.track.show', $nomor));

        $this->get(route('guest.track.show', $nomor))
            ->assertOk()
            ->assertSee($nomor);
    }

    public function test_guest_track_dengan_nomor_tidak_dikenal_dikembalikan_ke_form(): void
    {
        $this->get(route('guest.track.form', ['nomor_tiket' => 'TKT-TIDAKADA']))
            ->assertRedirect(route('guest.track.form'))
            ->assertSessionHas('error');
    }

    public function test_guest_ditolak_saat_honeypot_terisi(): void
    {
        $response = $this->post('/lapor', [
            'nama_guest' => 'Pengunjung',
            'kontak_guest' => 'pengunjung@mail.com',
            'departemen_id' => $this->departemen->id,
            'kategori_id' => $this->kategori->id,
            'judul' => 'Masalah uji',
            'deskripsi' => 'Deskripsi masalah uji.',
            'prioritas' => 'sedang',
            'website' => 'http://spam.example.com',
        ]);

        $response->assertSessionHasErrors('website');
        $this->assertDatabaseCount('tickets', 0);
    }

    public function test_user_internal_tidak_bisa_memakai_kategori_departemen_lain(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $departemenLain = Departemen::create([
            'kode' => 'DEPT-LAIN',
            'nama' => 'Departemen Lain',
        ]);

        $kategoriLain = Kategori::create([
            'departemen_id' => $departemenLain->id,
            'nama' => 'Kategori Lain',
            'default_sla_jam' => 12,
        ]);

        $response = $this->actingAs($user)->post('/tickets', [
            'departemen_id' => $this->departemen->id,
            'kategori_id' => $kategoriLain->id,
            'judul' => 'Masalah uji',
            'deskripsi' => 'Deskripsi masalah uji.',
            'prioritas' => 'sedang',
        ]);

        $response->assertSessionHasErrors('kategori_id');
        $this->assertDatabaseCount('tickets', 0);
    }

    public function test_user_internal_dengan_kategori_cocok_berhasil_membuat_tiket(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->post('/tickets', [
            'departemen_id' => $this->departemen->id,
            'kategori_id' => $this->kategori->id,
            'judul' => 'Masalah uji',
            'deskripsi' => 'Deskripsi masalah uji.',
            'prioritas' => 'sedang',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('tickets', [
            'id_pelapor' => $user->id,
            'departemen_id' => $this->departemen->id,
            'kategori_id' => $this->kategori->id,
            'status' => 'open',
        ]);
    }
}

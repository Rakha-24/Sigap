<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Departemen;
use App\Models\Kategori;
use App\Models\Ticket;
use App\Models\TicketEvidence;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AgentWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private Departemen $deptUsat;

    private Departemen $deptLain;

    private Kategori $kategori;

    private User $agent;

    protected function setUp(): void
    {
        parent::setUp();

        $this->deptUsat = Departemen::create([
            'kode' => 'DEPT-ATK-USAT',
            'nama' => 'Unit Saudara',
        ]);

        $this->deptLain = Departemen::create([
            'kode' => 'DEPT-LAIN',
            'nama' => 'Unit Lain',
        ]);

        $this->kategori = Kategori::create([
            'departemen_id' => $this->deptUsat->id,
            'nama' => 'Kategori Unit Saudara',
            'default_sla_jam' => 24,
        ]);

        $this->agent = User::factory()->create([
            'role' => 'agent',
            'departemen_id' => $this->deptUsat->id,
        ]);
    }

    private function buatTiket(string $status = 'open'): Ticket
    {
        return Ticket::create([
            'nomor_tiket' => 'TKT-TEST-'.strtoupper(substr(sha1(uniqid()), 0, 5)),
            'departemen_id' => $this->deptUsat->id,
            'kategori_id' => $this->kategori->id,
            'nama_guest' => 'Penguji',
            'kontak_guest' => 'penguji@mail.com',
            'judul' => 'Masalah uji agent',
            'deskripsi' => 'Deskripsi masalah uji agent.',
            'prioritas' => 'tinggi',
            'status' => $status,
            'sla_target_at' => now()->addHours(24),
        ]);
    }

    public function test_agent_dapat_mengambil_tiket_dari_departemennya(): void
    {
        $tiket = $this->buatTiket();

        $response = $this->actingAs($this->agent)
            ->patch(route('agent.tickets.claim', $tiket));

        $response->assertRedirect(route('tickets.show', $tiket));

        $this->assertDatabaseHas('tickets', [
            'id' => $tiket->id,
            'assigned_agent_id' => $this->agent->id,
            'status' => 'in_progress',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'ticket_id' => $tiket->id,
            'aksi' => 'status_changed',
        ]);
    }

    public function test_agent_tidak_bisa_mengambil_tiket_departemen_lain(): void
    {
        $agentLain = User::factory()->create([
            'role' => 'agent',
            'departemen_id' => $this->deptLain->id,
        ]);

        $tiket = $this->buatTiket();

        $this->actingAs($agentLain)
            ->patch(route('agent.tickets.claim', $tiket))
            ->assertForbidden();

        $this->assertNull($tiket->fresh()->assigned_agent_id);
    }

    public function test_agent_tidak_bisa_menyelesaikan_tanpa_evidence(): void
    {
        $tiket = $this->buatTiket('in_progress');
        $tiket->forceFill(['assigned_agent_id' => $this->agent->id])->save();

        $response = $this->actingAs($this->agent)
            ->patch(route('agent.tickets.resolve', $tiket), [
                'catatan_penyelesaian' => 'Sudah diperbaiki',
            ]);

        $response->assertSessionHasErrors('evidence_penyelesaian');
        $this->assertSame('in_progress', $tiket->fresh()->status);
    }

    public function test_agent_menyelesaikan_dengan_evidence(): void
    {
        $tiket = $this->buatTiket('in_progress');
        $tiket->forceFill(['assigned_agent_id' => $this->agent->id])->save();

        $response = $this
            ->actingAs($this->agent)
            ->patch(route('agent.tickets.resolve', $tiket), [
                'catatan_penyelesaian' => 'Sudah diperbaiki',
                'evidence_penyelesaian' => UploadedFile::fake()->image('bukti.jpg'),
            ]);

        $response->assertSessionHasNoErrors();

        $tiket->refresh();
        $this->assertSame('resolved', $tiket->status);
        $this->assertNotNull($tiket->resolved_at);
        $this->assertSame('bukti.jpg', $tiket->file_evidence_penyelesaian);
        $this->assertDatabaseHas('ticket_evidence', [
            'ticket_id' => $tiket->id,
            'jenis' => 'penyelesaian',
            'nama_asli' => 'bukti.jpg',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'ticket_id' => $tiket->id,
            'aksi' => 'status_changed',
        ]);
    }

    public function test_agent_tidak_bisa_menyelesaikan_tiket_orang_lain(): void
    {
        $agentLain = User::factory()->create([
            'role' => 'agent',
            'departemen_id' => $this->deptUsat->id,
        ]);

        $tiket = $this->buatTiket('in_progress');
        $tiket->forceFill(['assigned_agent_id' => $agentLain->id])->save();

        $this->actingAs($this->agent)
            ->patch(route('agent.tickets.resolve', $tiket), [
                'catatan_penyelesaian' => 'Saya coba selesaikan',
                'evidence_penyelesaian' => UploadedFile::fake()->image('bukti.jpg'),
            ])
            ->assertForbidden();

        $this->assertSame('in_progress', $tiket->fresh()->status);
    }

    public function test_evidence_tersimpan_dan_bisa_diunduh_sebagai_bytea(): void
    {
        $tiket = $this->buatTiket();
        $tiket->forceFill([
            'assigned_agent_id' => $this->agent->id,
            'file_evidence_penyelesaian' => 'bukti.jpg',
        ])->save();

        $file = UploadedFile::fake()->image('bukti.jpg', 200, 200);
        TicketEvidence::create([
            'ticket_id' => $tiket->id,
            'jenis' => 'penyelesaian',
            'nama_asli' => 'bukti.jpg',
            'mime' => 'image/jpeg',
            'ukuran' => $file->getSize(),
            'data' => $file->get(),
        ]);

        $response = $this->actingAs($this->agent)
            ->get(route('tickets.evidence', ['ticket' => $tiket, 'jenis' => 'penyelesaian']));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'image/jpeg');
        $this->assertSame($file->get(), $response->streamedContent());
    }
}
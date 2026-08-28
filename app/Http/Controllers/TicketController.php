<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResolveTicketRequest;
use App\Http\Requests\StoreTicketRequest;
use App\Models\Departemen;
use App\Models\Kategori;
use App\Models\Ticket;
use App\Models\TicketEvidence;
use App\Services\SlaCalculator;
use App\Services\TicketEvidenceService;
use App\Services\TicketNumberGenerator;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function __construct(
        private readonly TicketNumberGenerator $numberGenerator,
        private readonly SlaCalculator $slaCalculator,
        private readonly TicketEvidenceService $evidenceService,
    ) {}

    public function index()
    {
        $tickets = Ticket::with(['departemen', 'kategori'])
            ->where('id_pelapor', auth()->id())
            ->latest()
            ->paginate(15);

        return view('tickets.index', compact('tickets'));
    }

    public function create()
    {
        return view('tickets.create', [
            'departemens' => Departemen::where('is_active', true)->get(),
            'kategoris' => Kategori::with('departemen')->get(),
        ]);
    }

    public function store(StoreTicketRequest $request)
    {
        $kategori = Kategori::findOrFail($request->kategori_id);

        // Catatan: bukti disimpan sebagai bytea di tabel ticket_evidence (bukan
        // filesystem) agar aman pada deployment serverless yang read-only.

        $ticket = Ticket::create([
            'nomor_tiket' => $this->numberGenerator->generate(),
            'departemen_id' => $request->departemen_id,
            'kategori_id' => $request->kategori_id,
            'id_pelapor' => auth()->id(),
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'prioritas' => $request->prioritas,
            'status' => 'open',
            'sla_target_at' => $this->slaCalculator->hitung($kategori, $request->prioritas),
            'ip_pelapor' => $request->ip(),
        ]);

        // Simpan bukti pelapor (jika ada) setelah tiket tercipta (butuh ticket_id).
        if ($request->hasFile('evidence')) {
            $ticket->file_evidence_pelapor = $this->evidenceService->store(
                $ticket,
                $request->file('evidence'),
                'pelapor',
            );
            $ticket->save();
        }

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', "Tiket {$ticket->nomor_tiket} berhasil dibuat.");
    }

    public function show(Ticket $ticket)
    {
        $this->authorize('view', $ticket);
        $ticket->load(['comments.user', 'auditLogs.user', 'agent', 'departemen', 'kategori']);

        return view('tickets.show', compact('ticket'));
    }

    /**
     * Endpoint khusus Agent: menandai tiket Resolved.
     * Aturan "wajib evidence" ditegakkan sepenuhnya oleh ResolveTicketRequest.
     */
    public function resolve(ResolveTicketRequest $request, Ticket $ticket)
    {
        // Simpan bukti penanganan sebagai bytea (ticket_evidence), bukan filesystem.
        if ($request->hasFile('evidence_penyelesaian')) {
            $ticket->file_evidence_penyelesaian = $this->evidenceService->store(
                $ticket,
                $request->file('evidence_penyelesaian'),
                'penyelesaian',
            );
        }

        $ticket->catatan_penyelesaian = $request->catatan_penyelesaian;
        $ticket->status = 'resolved';
        $ticket->resolved_at = now();
        $ticket->save(); // AuditLogObserver mencatat perubahan status otomatis

        return back()->with('success', "Tiket {$ticket->nomor_tiket} ditandai selesai.");
    }

    /**
     * Unduh/lihat bukti (bytea) dari database.
     * Akses mengikuti kebijakan 'view' tiket (pelapor, agent departemen, admin).
     */
    public function evidence(Ticket $ticket, string $jenis, Request $request)
    {
        $jenis = in_array($jenis, ['pelapor', 'penyelesaian'], true) ? $jenis : abort(404);

        $evidence = $ticket->evidence()
            ->where('jenis', $jenis)
            ->firstOrFail();

        $this->authorize('view', $ticket);

        return $this->evidenceService->download(
            $evidence,
            $request->boolean('inline'),
        );
    }
}

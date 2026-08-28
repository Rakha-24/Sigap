<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResolveTicketRequest;
use App\Http\Requests\StoreTicketRequest;
use App\Models\Departemen;
use App\Models\Kategori;
use App\Models\Ticket;
use App\Services\SlaCalculator;
use App\Services\TicketNumberGenerator;

class TicketController extends Controller
{
    public function __construct(
        private readonly TicketNumberGenerator $numberGenerator,
        private readonly SlaCalculator $slaCalculator,
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

        // Simpan berkas bukti SEBELUM transaksi DB. I/O penyimpanan file tidak
        // perlu dibungkus transaksi database.
        $path = $request->hasFile('evidence')
            ? $request->file('evidence')->store('evidence/pelapor', 'private')
            : null;

        // WAJIB: Hindari blok transaksi eksplisit (DB::transaction) multi-pernyataan.
        // Neon dengan koneksi pooler (PgBouncer transaction mode) meng-abort
        // transaksi yang sedang berjalan secara deterministik → "current transaction
        // is aborted" (SQLSTATE 25P02) pada INSERT.
        // Karena di sini hanya ada SATU operasi tulis (INSERT), kita eksekusi memakai
        // auto-commit per-pernyataan sehingga tidak ada transaksi panjang yang di-abort.
        $ticket = Ticket::create([
            'nomor_tiket' => $this->numberGenerator->generate(),
            'departemen_id' => $request->departemen_id,
            'kategori_id' => $request->kategori_id,
            'id_pelapor' => auth()->id(),
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'prioritas' => $request->prioritas,
            'status' => 'open',
            'file_evidence_pelapor' => $path,
            'sla_target_at' => $this->slaCalculator->hitung($kategori, $request->prioritas),
            'ip_pelapor' => $request->ip(),
        ]);

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
        // Simpan berkas bukti DI LUAR transaksi database (sama seperti store()),
        // untuk menghindari transaksi Postgres yang di-abort oleh connection pooler.
        if ($request->hasFile('evidence_penyelesaian')) {
            $path = $request->file('evidence_penyelesaian')->store('evidence/penyelesaian', 'private');
            $ticket->file_evidence_penyelesaian = $path;
        }

        $ticket->catatan_penyelesaian = $request->catatan_penyelesaian;
        $ticket->status = 'resolved';
        $ticket->resolved_at = now();
        $ticket->save(); // AuditLogObserver mencatat perubahan status otomatis

        return back()->with('success', "Tiket {$ticket->nomor_tiket} ditandai selesai.");
    }
}

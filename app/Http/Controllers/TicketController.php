<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResolveTicketRequest;
use App\Http\Requests\StoreTicketRequest;
use App\Models\Departemen;
use App\Models\Kategori;
use App\Models\Ticket;
use App\Services\SlaCalculator;
use App\Services\TicketNumberGenerator;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Throwable;

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

        // Simpan berkas bukti DI LUAR transaksi database. I/O penyimpanan file
        // tidak perlu dibungkus transaksi dan mengurangi risiko transaksi Postgres
        // dibatalkan (terutama pada connection pooler seperti Neon/PgBouncer,
        // di mana transaksi yang sedang berjalan bisa di-abort oleh server).
        $path = $request->hasFile('evidence')
            ? $request->file('evidence')->store('evidence/pelapor', 'private')
            : null;

        // Neon/infra mungkin meng-abort transaksi pada koneksi pooler saat beban
        // tinggi. Lakukan retry hanya untuk kegagalan transien/sementara.
        $ticket = retry(
            3,
            function () use ($request, $kategori, $path) {
                return DB::transaction(function () use ($request, $kategori, $path) {
                    return Ticket::create([
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
                });
            },
            250,
            function (Throwable $e) {
                return $e instanceof QueryException
                    && preg_match('/(25P02|connection|closed|aborted|deadlock|timeout)/i', (string) $e->getMessage());
            },
        );

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

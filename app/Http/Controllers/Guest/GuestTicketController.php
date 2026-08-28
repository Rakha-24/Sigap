<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGuestTicketRequest;
use App\Models\Departemen;
use App\Models\Kategori;
use App\Models\Ticket;
use App\Services\SlaCalculator;
use App\Services\TicketNumberGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GuestTicketController extends Controller
{
    public function __construct(
        private readonly TicketNumberGenerator $numberGenerator,
        private readonly SlaCalculator $slaCalculator,
    ) {}

    public function create()
    {
        return view('guest.create', [
            'departemens' => Departemen::where('is_active', true)->get(),
            'kategoris' => Kategori::with('departemen')->get(),
        ]);
    }

    public function store(StoreGuestTicketRequest $request)
    {
        $kategori = Kategori::findOrFail($request->kategori_id);

        // Simpan berkas bukti SEBELUM INSERT. I/O file tidak perlu dibungkus transaksi.
        $path = $request->hasFile('evidence')
            ? $request->file('evidence')->store('evidence/pelapor', 'private')
            : null;

        // WAJIB: Hindari blok transaksi eksplisit (DB::transaction) multi-pernyataan.
        // Neon dengan koneksi pooler (PgBouncer transaction mode) meng-abort transaksi
        // yang sedang berjalan → SQLSTATE 25P02 "current transaction is aborted".
        // Karena hanya ada SATU operasi tulis (INSERT), pakai auto-commit per-pernyataan.
        $ticket = Ticket::create([
            'nomor_tiket' => $this->numberGenerator->generate(),
            'departemen_id' => $request->departemen_id,
            'kategori_id' => $request->kategori_id,
            'nama_guest' => $request->nama_guest,
            'kontak_guest' => $request->kontak_guest,
            'tracking_token' => Str::random(40),
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'prioritas' => $request->prioritas,
            'status' => 'open',
            'file_evidence_pelapor' => $path,
            'sla_target_at' => $this->slaCalculator->hitung($kategori, $request->prioritas),
            'ip_pelapor' => $request->ip(),
        ]);

        return redirect()
            ->route('guest.track.show', $ticket->nomor_tiket)
            ->with('success', "Tiket berhasil dibuat. Simpan nomor tiket Anda: {$ticket->nomor_tiket}");
    }

    /** Form pelacakan (input nomor tiket). */
    public function trackForm(Request $request)
    {
        $nomorTiket = $request->query('nomor_tiket');

        if ($nomorTiket === null) {
            return view('guest.track');
        }

        $tiket = Ticket::where('nomor_tiket', $nomorTiket)->first();

        if (! $tiket) {
            return redirect()
                ->route('guest.track.form')
                ->with('error', "Nomor tiket '{$nomorTiket}' tidak ditemukan. Periksa kembali nomor tiket Anda.");
        }

        return redirect()->route('guest.track.show', $tiket->nomor_tiket);
    }

    /** Hasil pelacakan tiket berdasarkan nomor tiket. */
    public function trackShow(string $nomorTiket)
    {
        $ticket = Ticket::with(['auditLogs', 'comments' => fn ($q) => $q->where('is_internal', false)])
            ->where('nomor_tiket', $nomorTiket)
            ->firstOrFail();

        return view('guest.track-result', compact('ticket'));
    }
}

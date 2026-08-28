<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGuestTicketRequest;
use App\Models\Departemen;
use App\Models\Kategori;
use App\Models\Ticket;
use App\Services\SlaCalculator;
use App\Services\TicketEvidenceService;
use App\Services\TicketNumberGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GuestTicketController extends Controller
{
    public function __construct(
        private readonly TicketNumberGenerator $numberGenerator,
        private readonly SlaCalculator $slaCalculator,
        private readonly TicketEvidenceService $evidenceService,
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

        // Catatan: bukti disimpan sebagai bytea di tabel ticket_evidence (bukan
        // filesystem) agar aman pada deployment serverless yang read-only.

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

<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Ticket;

class TicketClaimController extends Controller
{
    public function __invoke(Ticket $ticket)
    {
        $agent = auth()->user();

        // Proteksi lintas-departemen: agent hanya boleh mengambil tiket
        // dari departemennya sendiri, atau tiket yang sudah ditugaskan ke dirinya.
        abort_unless(
            $ticket->departemen_id === $agent->departemen_id || $ticket->assigned_agent_id === $agent->id,
            403,
            'Tiket ini bukan bagian dari departemen Anda.'
        );

        // Cegah race condition: dua agent menekan "Kerjakan" bersamaan pada tiket yang sama.
        if ($ticket->assigned_agent_id !== null && $ticket->assigned_agent_id !== $agent->id) {
            return back()->with('error', 'Tiket ini baru saja diambil oleh agent lain.');
        }

        // Hindari blok transaksi eksplisit (DB::transaction) multi-pernyataan karena
        // Neon/PgBouncer transaction-mode meng-abort transaksi panjang → SQLSTATE 25P02.
        // Operasi ini hanya satu UPDATE; observer audit-log menulis statement sendiri.
        $ticket->assigned_agent_id = $agent->id;
        if ($ticket->status === 'open') {
            $ticket->status = 'in_progress';
        }
        $ticket->save(); // TicketObserver mencatat perubahan status ke audit_logs otomatis

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', "Tiket {$ticket->nomor_tiket} berhasil diambil dan ditugaskan ke Anda.");
    }
}
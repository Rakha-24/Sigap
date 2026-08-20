<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Ticket;

class TicketObserver
{
    public function created(Ticket $ticket): void
    {
        AuditLog::create([
            'ticket_id'   => $ticket->id,
            'user_id'     => $ticket->id_pelapor,
            'aktor_label' => $ticket->isGuestTicket() ? $ticket->nama_guest : null,
            'aksi'        => 'ticket_created',
            'deskripsi'   => "Tiket {$ticket->nomor_tiket} dibuat.",
            'data_after'  => $ticket->only(['status', 'prioritas', 'departemen_id', 'kategori_id']),
            'ip_address'  => $ticket->ip_pelapor,
        ]);
    }

    public function updated(Ticket $ticket): void
    {
        if (! $ticket->isDirty('status')) {
            return;
        }

        AuditLog::create([
            'ticket_id'   => $ticket->id,
            'user_id'     => auth()->id(),
            'aktor_label' => auth()->user()?->name,
            'aksi'        => 'status_changed',
            'deskripsi'   => sprintf(
                "Status berubah dari '%s' menjadi '%s'.",
                $ticket->getOriginal('status'),
                $ticket->status
            ),
            'data_before' => ['status' => $ticket->getOriginal('status')],
            'data_after'  => ['status' => $ticket->status],
            'ip_address'  => request()?->ip(),
        ]);
    }
}
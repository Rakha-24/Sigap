<?php

namespace App\Exports;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TicketReportExport implements FromQuery, WithHeadings, WithMapping
{

    public function query(): Builder
    {
        return Ticket::with(['departemen', 'kategori'])->orderByDesc('created_at');
    }

    public function headings(): array
    {
        return ['Nomor Tiket', 'Departemen', 'Kategori', 'Prioritas', 'Status', 'Dibuat', 'Diselesaikan'];
    }

    public function map($ticket): array
    {
        return [
            $ticket->nomor_tiket,
            $ticket->departemen->nama,
            $ticket->kategori->nama,
            ucfirst($ticket->prioritas),
            ucfirst($ticket->status),
            $ticket->created_at->format('d-m-Y H:i'),
            $ticket->resolved_at?->format('d-m-Y H:i') ?? '-',
        ];
    }
}
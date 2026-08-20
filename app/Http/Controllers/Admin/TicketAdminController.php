<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketAdminController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'belum');
        $cari = trim((string) $request->query('cari', ''));

        $query = Ticket::with(['departemen', 'kategori', 'agent', 'pelapor'])
            ->latest();

        if ($status === 'belum') {
            $query->whereIn('status', ['open', 'in_progress']);
        } elseif ($status === 'selesai') {
            $query->whereIn('status', ['resolved', 'closed']);
        }

        if ($cari !== '') {
            // 'ilike' hanya didukung PostgreSQL; SQLite memakai 'like' (case-insensitive default)
            $op = DB::connection()->getDriverName() === 'pgsql' ? 'ilike' : 'like';

            $query->where(function ($q) use ($cari, $op) {
                $q->where('nomor_tiket', $op, "%{$cari}%")
                    ->orWhere('judul', $op, "%{$cari}%")
                    ->orWhere('nama_guest', $op, "%{$cari}%");
            });
        }

        $tickets = $query->paginate(15)->withQueryString();

        return view('admin.tickets.index', [
            'tickets' => $tickets,
            'status' => $status,
            'counts' => [
                'semua' => Ticket::count(),
                'belum' => Ticket::whereIn('status', ['open', 'in_progress'])->count(),
                'selesai' => Ticket::whereIn('status', ['resolved', 'closed'])->count(),
            ],
        ]);
    }
}

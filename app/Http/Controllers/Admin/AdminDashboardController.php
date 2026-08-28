<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total'   => Ticket::count(),
            'open'    => Ticket::where('status', 'open')->count(),
            'resolved'=> Ticket::where('status', 'resolved')->count(),
            'closed'      => Ticket::where('status', 'closed')->count(),
        ];

        $recentTickets = Ticket::with(['departemen', 'kategori', 'agent'])
            ->latest()
            ->take(10)
            ->get();

        $slaWarning = Ticket::whereNotIn('status', ['resolved', 'closed'])
            ->where('sla_target_at', '<=', now()->copy()->addHours(2))
            ->where('sla_target_at', '>', now())
            ->with(['departemen', 'agent'])
            ->orderBy('sla_target_at')
            ->take(5)
            ->get();

        $perDepartemen = Ticket::select('departemen_id', DB::raw('count(*) as jumlah'))
            ->groupBy('departemen_id')
            ->with('departemen')
            ->orderByDesc('jumlah')
            ->get();

        return view('admin.dashboard', compact('stats', 'recentTickets', 'slaWarning', 'perDepartemen'));
    }
}

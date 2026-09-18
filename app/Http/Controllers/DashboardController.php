<?php

namespace App\Http\Controllers;

use App\Models\Ticket;

class DashboardController extends Controller
{
    public function index()
    {
        return match (auth()->user()->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'agent' => $this->agentDashboard(),
            default => view('user.dashboard', [
                'tickets' => auth()->user()->ticketsSebagaiPelapor()->latest()->take(10)->get(),
                'stats'   => [
                    'total'       => auth()->user()->ticketsSebagaiPelapor()->count(),
                    'in_progress' => auth()->user()->ticketsSebagaiPelapor()->where('status', 'in_progress')->count(),
                    'selesai'     => auth()->user()->ticketsSebagaiPelapor()->where('status', 'resolved')->count(),
                ],
            ]),
        };
    }

    /**
     * Dashboard khusus agent: ringkasan tiket yang ditugaskan kepadanya.
     */
    protected function agentDashboard()
    {
        $agentId = auth()->id();

        $ditugaskan = Ticket::where('assigned_agent_id', $agentId);

        $stats = [
            'total'            => (clone $ditugaskan)->count(),
            'in_progress'      => (clone $ditugaskan)->where('status', 'in_progress')->count(),
            'selesai_hari_ini' => (clone $ditugaskan)
                ->where('status', 'resolved')
                ->whereDate('resolved_at', today())
                ->count(),
            'sla_terlampaui'   => (clone $ditugaskan)
                ->whereNotIn('status', ['resolved', 'closed'])
                ->where('sla_target_at', '<', now())
                ->count(),
        ];

        $tiketPrioritas = (clone $ditugaskan)
            ->whereNotIn('status', ['resolved', 'closed'])
            ->with(['kategori', 'departemen'])
            // Urutan custom: tinggi > sedang > rendah, lalu SLA paling dekat duluan
            ->orderByRaw("CASE prioritas WHEN 'tinggi' THEN 1 WHEN 'sedang' THEN 2 ELSE 3 END")
            ->orderBy('sla_target_at', 'asc')
            ->take(5)
            ->get();

        return view('agent.dashboard', compact('stats', 'tiketPrioritas'));
    }
}
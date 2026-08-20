<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        return match (auth()->user()->role) {
            'admin' => redirect()->route('admin.analytics'),
            'agent' => redirect()->route('agent.queue'),
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
}
<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Ticket;

class QueueController extends Controller
{
    public function index()
    {
        $agentDepartemenId = auth()->user()->departemen_id;

        $tickets = Ticket::with('departemen')
            ->whereIn('status', ['open', 'in_progress'])
            // BATASAN PENTING: agent hanya boleh melihat tiket dari departemennya sendiri
            // (untuk tiket yang belum diambil), ATAU tiket yang sudah ditugaskan ke dirinya.
            // Tanpa filter departemen_id ini, agent Departemen A akan ikut melihat
            // tiket milik Departemen B — sistem ini generik untuk banyak departemen,
            // bukan cuma IT, jadi isolasi per-departemen wajib ditegakkan di sini.
            ->where(function ($q) use ($agentDepartemenId) {
                $q->where(function ($sub) use ($agentDepartemenId) {
                    $sub->whereNull('assigned_agent_id')
                        ->where('departemen_id', $agentDepartemenId);
                })->orWhere('assigned_agent_id', auth()->id());
            })
            // Urutan custom: tinggi > sedang > rendah, lalu SLA paling dekat duluan
            ->orderByRaw("CASE prioritas WHEN 'tinggi' THEN 1 WHEN 'sedang' THEN 2 ELSE 3 END")
            ->orderBy('sla_target_at', 'asc')
            ->get();

        return view('agent.queue', compact('tickets'));
    }
}
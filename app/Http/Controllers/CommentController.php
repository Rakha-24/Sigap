<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Ticket;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request, Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        $user = $request->user();

        Comment::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'pesan' => $request->pesan,
            // Komentar agent/admian terhadap tiket internal bersifat internal (tidak tampil
            // di pelacakan publik). Untuk tiket guest, balasan agent/admian justru publik
            // agar pengguna dapat melihatnya di halaman lacak.
            'is_internal' => ! $ticket->isGuestTicket() && $user->role !== 'user',
        ]);

        return back()->with('success', 'Komentar berhasil ditambahkan.');
    }
}

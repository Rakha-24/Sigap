<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Comment;

class CommentObserver
{
    public function created(Comment $comment): void
    {
        AuditLog::create([
            'ticket_id' => $comment->ticket_id,
            'user_id' => $comment->user_id,
            'aktor_label' => $comment->user?->name,
            'aksi' => $comment->is_internal ? 'internal_note_added' : 'comment_added',
            'deskripsi' => 'Menambahkan tanggapan pada tiket.',
            'ip_address' => request()?->ip(),
        ]);
    }
}

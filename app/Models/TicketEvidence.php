<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketEvidence extends Model
{
    protected $table = 'ticket_evidence';

    public $timestamps = true;

    protected $fillable = [
        'ticket_id', 'jenis', 'nama_asli', 'mime', 'ukuran', 'data',
    ];

    protected $casts = [
        'ukuran' => 'integer',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }
}

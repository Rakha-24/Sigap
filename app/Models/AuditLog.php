<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    const UPDATED_AT = null; // tidak ada kolom updated_at

    protected $fillable = [
        'ticket_id', 'user_id', 'aktor_label', 'aksi',
        'deskripsi', 'data_before', 'data_after', 'ip_address',
    ];

    protected $casts = [
        'data_before' => 'array',
        'data_after'  => 'array',
    ];

    /** Guard tambahan di level Eloquent: tolak update/delete meski trigger DB gagal terpasang. */
    protected static function booted(): void
    {
        static::updating(fn () => throw new \RuntimeException('audit_logs bersifat immutable.'));
        static::deleting(fn () => throw new \RuntimeException('audit_logs bersifat immutable.'));
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
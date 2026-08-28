<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_tiket', 'departemen_id', 'kategori_id', 'id_pelapor',
        'nama_guest', 'kontak_guest', 'tracking_token', 'assigned_agent_id',
        'judul', 'deskripsi', 'file_evidence_pelapor', 'file_evidence_penyelesaian',
        'catatan_penyelesaian', 'prioritas', 'status', 'sla_target_at',
        'resolved_at', 'closed_at', 'ip_pelapor',
    ];

    protected $casts = [
        'sla_target_at' => 'datetime',
        'resolved_at'   => 'datetime',
        'closed_at'     => 'datetime',
    ];

    public function departemen(): BelongsTo
    {
        return $this->belongsTo(Departemen::class);
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    public function pelapor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_pelapor');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_agent_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->orderBy('created_at');
    }

    public function evidencePelapor(): HasOne
    {
        return $this->hasOne(TicketEvidence::class)->where('jenis', 'pelapor');
    }

    public function evidencePenyelesaian(): HasOne
    {
        return $this->hasOne(TicketEvidence::class)->where('jenis', 'penyelesaian');
    }

    public function evidence(): HasMany
    {
        return $this->hasMany(TicketEvidence::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class)->orderByDesc('created_at');
    }

    public function isGuestTicket(): bool
    {
        return is_null($this->id_pelapor);
    }

    public function isSlaBreached(): bool
    {
        return $this->sla_target_at
            && $this->sla_target_at->isPast()
            && ! in_array($this->status, ['resolved', 'closed']);
    }
}
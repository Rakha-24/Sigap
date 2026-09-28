<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'avatar',
        'avatar_data',
        'avatar_mime',
        'password',
        'role',
        'departemen_id',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /** URL foto profil yang bisa diakses (null bila belum mengunggah). */
    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar_data && $this->avatar_mime) {
            return 'data:'.$this->avatar_mime.';base64,'.$this->avatar_data;
        }

        return $this->avatar ? asset('storage/'.$this->avatar) : null;
    }

    /** Hapus foto profil lama (data di database ataupun berkas disk bila ada). */
    public function deleteAvatarFile(): void
    {
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            Storage::disk('public')->delete($this->avatar);
        }
    }

    /** Departemen tempat agent bertugas (khusus role agent). */
    public function departemen(): BelongsTo
    {
        return $this->belongsTo(Departemen::class);
    }

    /** Tiket yang dibuat oleh user ini sebagai pelapor internal. */
    public function ticketsSebagaiPelapor(): HasMany
    {
        return $this->hasMany(Ticket::class, 'id_pelapor');
    }

    /** Tiket yang ditugaskan ke user ini sebagai agent. */
    public function ticketsSebagaiAgent(): HasMany
    {
        return $this->hasMany(Ticket::class, 'assigned_agent_id');
    }

    /**
     * Override notifikasi reset password bawaan Laravel agar memakai
     * template email kustom SIGAP (lihat app/Notifications/ResetPasswordNotification.php).
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}

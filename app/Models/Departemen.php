<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Departemen extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'nama',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /** Daftar kategori masalah di bawah departemen ini. */
    public function kategoris(): HasMany
    {
        return $this->hasMany(Kategori::class);
    }

    /** Semua tiket yang masuk ke departemen ini. */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /** Agent yang ditugaskan di departemen ini. */
    public function agents(): HasMany
    {
        return $this->hasMany(User::class)->where('role', 'agent');
    }
}
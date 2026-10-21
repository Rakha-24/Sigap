<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegisteredEmail extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'nama',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    /** Apakah email ini terdaftar dan aktif (boleh membuat laporan). */
    public static function bolehLapor(string $email): bool
    {
        return static::aktif()->where('email', strtolower(trim($email)))->exists();
    }
}

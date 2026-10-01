<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class DemoLoginController extends Controller
{
    /**
     * Akun demo yang boleh diakses lewat tombol "masuk cepat" di halaman login.
     * Kunci = role, nilai = email akun demo baku (lihat database/seeders/UserSeeder.php).
     */
    protected const DEMO_ACCOUNTS = [
        'admin' => 'admin@sigap.test',
        'agent' => 'agent@sigap.test',
        'user' => 'user@sigap.test',
    ];

    /**
     * Masuk langsung ke akun demo sesuai role yang dipilih.
     */
    public function store(Request $request): RedirectResponse
    {
        $role = $request->input('role');

        if (! array_key_exists($role, self::DEMO_ACCOUNTS)) {
            throw ValidationException::withMessages([
                'role' => 'Peran akun demo tidak dikenali.',
            ]);
        }

        $user = User::where('email', self::DEMO_ACCOUNTS[$role])->first();

        if (! $user || ! $user->is_active) {
            throw ValidationException::withMessages([
                'email' => 'Akun demo sedang tidak tersedia.',
            ]);
        }

        Auth::login($user);

        $request->session()->regenerate();

        // Arahkan sesuai role AKTUAL akun (dari DB), bukan dari input request,
        // supaya tidak ada kenaikan hak akses lewat parameter role.
        $home = match ($user->role) {
            'admin' => '/admin/dashboard',
            'agent' => '/agent/antrean',
            default => route('dashboard', absolute: false),
        };

        return redirect($home);
    }
}

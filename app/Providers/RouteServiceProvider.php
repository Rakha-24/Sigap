<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\RateLimiter;

class RouteServiceProvider extends ServiceProvider
{
    /** Halaman tujuan setelah login berhasil (dipakai Breeze & redirect default). */
    public const HOME = '/dashboard';

    public function boot(): void
    {
        // Maksimal 3 tiket per 10 menit per alamat IP, untuk mencegah spam bot pada form guest
        RateLimiter::for('guest-ticket', function (Request $request) {
            return Limit::perMinutes(10, 3)->by($request->ip());
        });

        // Maksimal 15 percobaan lacak tiket per menit per IP
        RateLimiter::for('guest-track', function (Request $request) {
            return Limit::perMinute(15)->by($request->ip());
        });

        // Rate limit standar Breeze untuk login (opsional, kalau belum ada)
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->input('email').'|'.$request->ip());
        });

        $this->routes(function () {
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
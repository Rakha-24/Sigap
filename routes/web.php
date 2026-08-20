<?php

use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\MasterDataController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Agent\QueueController;
use App\Http\Controllers\Agent\TicketClaimController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Guest\GuestTicketController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| GUEST / PUBLIC ROUTES — tanpa login, dilindungi rate limiting
|--------------------------------------------------------------------------
*/
Route::view('/', 'guest.landing')->name('guest.landing');

Route::prefix('lapor')->name('guest.')->group(function () {
    Route::get('/', [GuestTicketController::class, 'create'])->name('ticket.create');
    Route::post('/', [GuestTicketController::class, 'store'])
        ->middleware('throttle:guest-ticket')
        ->name('ticket.store');
});

Route::prefix('lacak')->name('guest.track.')->middleware('throttle:guest-track')->group(function () {
    Route::get('/', [GuestTicketController::class, 'trackForm'])->name('form');
    Route::get('/{nomorTiket}', [GuestTicketController::class, 'trackShow'])->name('show');
});

/*
|--------------------------------------------------------------------------
| AUTH SCAFFOLDING (Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| INTERNAL ROUTES — wajib login, dipisah per role
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Tiket bisa diakses semua role internal (user membuat, agent/admin melihat)
    Route::resource('tickets', TicketController::class)->only(['index', 'create', 'store', 'show']);

    // AGENT — semua route di bawah ini otomatis dibatasi per-departemen di level
    // controller (QueueController & TicketClaimController), bukan cuma di middleware,
    // supaya agent satu departemen tidak pernah bisa melihat/mengambil tiket departemen lain.
    Route::middleware('role:agent')->prefix('agent')->name('agent.')->group(function () {
        Route::get('/antrean', [QueueController::class, 'index'])->name('queue');
        Route::patch('/tickets/{ticket}/claim', TicketClaimController::class)->name('tickets.claim');
        Route::patch('/tickets/{ticket}/resolve', [TicketController::class, 'resolve'])->name('tickets.resolve');
    });

    // ADMIN
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/analitik', [AnalyticsController::class, 'index'])->name('analytics');
        Route::get('/analitik/export', [AnalyticsController::class, 'export'])->name('analytics.export');

        Route::resource('master-data/departemen', MasterDataController::class)
    ->names('master-data.departemen');

        Route::resource('master-data/kategori', KategoriController::class)
            ->except(['show'])
            ->names('master-data.kategori');
        Route::get('master-data/sla', [KategoriController::class, 'slaOverview'])
            ->name('master-data.sla');

        Route::resource('users', UserManagementController::class)
            ->except(['show', 'destroy'])
            ->names('users');
        Route::patch('users/{user}/toggle-active', [UserManagementController::class, 'toggleActive'])
            ->name('users.toggle-active');
    });
});
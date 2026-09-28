<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Maksimal percobaan masuk yang gagal sebelum akun dikunci sementara.
     */
    protected const MAX_ATTEMPTS = 5;

    /**
     * Jendela waktu (detik) counter percobaan diingat.
     */
    protected const DECAY_SECONDS = 60;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $user = User::where('email', $this->input('email'))->first();

        // Cek status aktif SEBELUM mencoba autentikasi kredensial,
        // supaya pesan error tegas: akun nonaktif, bukan "kredensial salah".
        if ($user && ! $user->is_active) {
            $this->hitThrottle();

            throw ValidationException::withMessages([
                'email' => 'Akun Anda telah dinonaktifkan. Silakan hubungi administrator SIGAP.',
            ]);
        }

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            $this->hitThrottle();

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        $this->clearThrottle();
    }

    /**
     * Blokir percobaan masuk ketika batas maksimal sudah tercapai.
     */
    public function ensureIsNotRateLimited(): void
    {
        $row = $this->throttleRow();

        if ($this->effectiveAttempts($row) < static::MAX_ATTEMPTS) {
            return;
        }

        event(new Lockout($this));

        $seconds = $this->availableIn($row);

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }

    /**
     * Mencatat satu percobaan masuk yang gagal. Counter di-reset bila
     * jendela waktu sudah lewat.
     *
     * Sengaja TANPA blok transaksi eksplisit (DB::transaction/lockForUpdate):
     * Neon/PgBouncer transaction-mode meng-abort transaksi multi-pernyataan
     * → SQLSTATE 25P02. Kedua statement berjalan autocommit; bila terjadi race
     * antar-instance, dampak terparah hanya menghitung satu percobaan kurang
     * (masih tetap terbatas), bukan kerusakan transaksi.
     */
    protected function hitThrottle(): void
    {
        $key = $this->throttleKey();
        $now = now();
        $cutoff = now()->subSeconds(static::DECAY_SECONDS)->format('Y-m-d H:i:s');

        $updated = DB::table('login_throttle')
            ->where('throttle_key', $key)
            ->where('updated_at', '>', $cutoff)
            ->update([
                'attempts' => DB::raw('attempts + 1'),
                'updated_at' => $now,
            ]);

        // Baris belum ada, atau jendela sudah lewat → reset hitungan ke 1.
        if ($updated === 0) {
            DB::table('login_throttle')->updateOrInsert(
                ['throttle_key' => $key],
                [
                    'attempts' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }

    protected function attempts(): int
    {
        return $this->effectiveAttempts($this->throttleRow());
    }

    protected function throttleRow(): ?object
    {
        return DB::table('login_throttle')
            ->where('throttle_key', $this->throttleKey())
            ->first();
    }

    /**
     * Jumlah percobaan aktif; counter yang sudah lewat jendela dihitung nol,
     * sehingga akun otomatis bisa mencoba lagi setelah jendela berlalu.
     */
    protected function effectiveAttempts(?object $row): int
    {
        if ($row === null) {
            return 0;
        }

        if ($row->updated_at !== null
            && Carbon::parse($row->updated_at)->lt(now()->subSeconds(static::DECAY_SECONDS))) {
            return 0;
        }

        return (int) $row->attempts;
    }

    /**
     * Sisa waktu (detik) sebelum jendela percobaan terbuka kembali.
     */
    protected function availableIn(?object $row = null): int
    {
        $row ??= $this->throttleRow();

        if ($row === null || $row->updated_at === null) {
            return static::DECAY_SECONDS;
        }

        $age = max(0, (int) now()->diffInSeconds(Carbon::parse($row->updated_at)));

        return max(1, static::DECAY_SECONDS - $age);
    }

    protected function clearThrottle(): void
    {
        DB::table('login_throttle')
            ->where('throttle_key', $this->throttleKey())
            ->delete();
    }
}

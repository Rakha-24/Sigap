<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginThrottleTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_locked_after_five_failed_attempts(): void
    {
        $user = User::factory()->create();

        for ($i = 1; $i <= 5; $i++) {
            $this->post('/login', [
                'email' => $user->email,
                'password' => 'wrong-password',
            ]);
        }

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString('Silakan coba lagi', session('errors')->get('email')[0]);
        $this->assertGuest();

        $this->assertDatabaseHas('login_throttle', [
            'throttle_key' => $user->email.'|127.0.0.1',
            'attempts' => 5,
        ]);
    }

    public function test_counter_reset_after_decay_window(): void
    {
        $user = User::factory()->create();

        for ($i = 1; $i <= 5; $i++) {
            $this->post('/login', [
                'email' => $user->email,
                'password' => 'wrong-password',
            ]);
        }

        // Lewati jendela 60 detik, reset counter, lalu coba lagi.
        $this->travel(61)->seconds();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();

        $this->assertDatabaseHas('login_throttle', [
            'throttle_key' => $user->email.'|127.0.0.1',
            'attempts' => 1,
        ]);
    }

    public function test_throttle_cleared_after_successful_login(): void
    {
        $user = User::factory()->create();

        for ($i = 1; $i <= 3; $i++) {
            $this->post('/login', [
                'email' => $user->email,
                'password' => 'wrong-password',
            ]);
        }

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $this->assertDatabaseMissing('login_throttle', [
            'throttle_key' => $user->email.'|127.0.0.1',
        ]);
    }
}

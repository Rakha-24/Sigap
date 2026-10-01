<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class DemoLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(UserSeeder::class);
    }

    public function test_login_page_menampilkan_tombol_akun_demo(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
        $response->assertSee('Masuk cepat sebagai akun demo');
        $response->assertSee('Admin');
        $response->assertSee('Agent');
        $response->assertSee('Pengguna');
    }

    public function test_demo_admin_masuk_dan_diarahkan_ke_dashboard_admin(): void
    {
        $response = $this->post(route('login.demo'), ['role' => 'admin']);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs(User::where('email', 'admin@sigap.test')->first());
        $this->assertSame('admin', Auth::user()->role);
    }

    public function test_demo_agent_masuk_dan_diarahkan_ke_antrean_agent(): void
    {
        $response = $this->post(route('login.demo'), ['role' => 'agent']);

        $response->assertRedirect('/agent/antrean');
        $this->assertAuthenticatedAs(User::where('email', 'agent@sigap.test')->first());
    }

    public function test_demo_user_masuk_dan_diarahkan_ke_dashboard(): void
    {
        $response = $this->post(route('login.demo'), ['role' => 'user']);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs(User::where('email', 'user@sigap.test')->first());
    }

    public function test_role_demo_tidak_dikenali_ditolak(): void
    {
        $response = $this->post(route('login.demo'), ['role' => 'superadmin']);

        $response->assertSessionHasErrors('role');
        $this->assertGuest();
    }
}

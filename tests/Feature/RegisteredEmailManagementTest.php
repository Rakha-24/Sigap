<?php

namespace Tests\Feature;

use App\Models\RegisteredEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisteredEmailManagementTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_dapat_melihat_daftar_email(): void
    {
        RegisteredEmail::create(['email' => 'a@mail.com', 'nama' => 'A']);

        $this->actingAs($this->admin())
            ->get(route('admin.master-data.email.index'))
            ->assertOk()
            ->assertSee('a@mail.com');
    }

    public function test_admin_dapat_menambah_email(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.master-data.email.store'), [
                'email' => 'BARU@Mail.com',
                'nama' => 'Karyawan Baru',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.master-data.email.index'));

        $this->assertDatabaseHas('registered_emails', [
            'email' => 'baru@mail.com',
            'nama' => 'Karyawan Baru',
            'is_active' => true,
        ]);
    }

    public function test_email_duplikat_ditolak(): void
    {
        RegisteredEmail::create(['email' => 'a@mail.com']);

        $this->actingAs($this->admin())
            ->post(route('admin.master-data.email.store'), ['email' => 'A@mail.com'])
            ->assertSessionHasErrors('email');

        $this->assertDatabaseCount('registered_emails', 1);
    }

    public function test_email_nonaktif_tidak_bisa_dipakai_lapor(): void
    {
        RegisteredEmail::create(['email' => 'dl@mail.com', 'is_active' => false]);

        $this->assertFalse(RegisteredEmail::bolehLapor('dl@mail.com'));
    }

    public function test_user_biasa_tidak_boleh_mengakses_master_data_email(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)
            ->get(route('admin.master-data.email.index'))
            ->assertForbidden();
    }

    public function test_admin_dapat_menghapus_email(): void
    {
        $email = RegisteredEmail::create(['email' => 'hapus@mail.com']);

        $this->actingAs($this->admin())
            ->delete(route('admin.master-data.email.destroy', $email))
            ->assertRedirect();

        $this->assertDatabaseMissing('registered_emails', ['id' => $email->id]);
    }
}

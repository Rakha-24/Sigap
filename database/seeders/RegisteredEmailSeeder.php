<?php

namespace Database\Seeders;

use App\Models\RegisteredEmail;
use Illuminate\Database\Seeder;

class RegisteredEmailSeeder extends Seeder
{
    public function run(): void
    {
        $emails = [
            ['email' => 'admin@sigap.test', 'nama' => 'Admin SIGAP'],
            ['email' => 'agent@sigap.test', 'nama' => 'Agent IT'],
            ['email' => 'user@sigap.test', 'nama' => 'Pengguna Umum'],
            ['email' => 'rakha24ls@gmail.com', 'nama' => 'Rakha'],
        ];

        foreach ($emails as $email) {
            RegisteredEmail::updateOrCreate(['email' => $email['email']], $email + ['is_active' => true]);
        }
    }
}

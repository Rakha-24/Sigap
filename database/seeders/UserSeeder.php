<?php

namespace Database\Seeders;

use App\Models\Departemen;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $itSupport = Departemen::where('nama', 'IT Support')->first();

        User::create([
            'name' => 'Admin SIGAP', 'email' => 'admin@sigap.test',
            'password' => Hash::make('password'), 'role' => 'admin',
        ]);

        User::create([
            'name' => 'Agent IT', 'email' => 'agent@sigap.test',
            'password' => Hash::make('password'), 'role' => 'agent',
            'departemen_id' => $itSupport?->id,
        ]);

        User::create([
            'name' => 'Pengguna Umum', 'email' => 'user@sigap.test',
            'password' => Hash::make('password'), 'role' => 'user',
        ]);
    }
}
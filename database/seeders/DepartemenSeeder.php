<?php

namespace Database\Seeders;

use App\Models\Departemen;
use App\Models\Kategori;
use Illuminate\Database\Seeder;

class DepartemenSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'IT Support' => [
                ['nama' => 'Jaringan & Internet', 'sla' => 4],
                ['nama' => 'Hardware/Perangkat', 'sla' => 8],
                ['nama' => 'Software/Aplikasi', 'sla' => 6],
                ['nama' => 'Akun & Akses', 'sla' => 2],
            ],
            'Fasilitas' => [
                ['nama' => 'Kebersihan Ruangan', 'sla' => 24],
                ['nama' => 'Perbaikan AC/Listrik', 'sla' => 12],
                ['nama' => 'Perlengkapan Kantor', 'sla' => 48],
            ],
            'Sumber Daya Manusia' => [
                ['nama' => 'Administrasi Karyawan', 'sla' => 24],
                ['nama' => 'Payroll & Tunjangan', 'sla' => 48],
            ],
        ];

        foreach ($data as $namaDept => $kategoriList) {
            $dept = Departemen::create([
                'kode' => strtoupper(str_replace(' ', '', substr($namaDept, 0, 6))),
                'nama' => $namaDept,
            ]);

            foreach ($kategoriList as $kategori) {
                Kategori::create([
                    'departemen_id'   => $dept->id,
                    'nama'            => $kategori['nama'],
                    'default_sla_jam' => $kategori['sla'],
                ]);
            }
        }
    }
}
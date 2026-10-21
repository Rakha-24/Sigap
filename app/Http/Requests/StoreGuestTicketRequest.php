<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesKategoriDepartemen;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGuestTicketRequest extends FormRequest
{
    use ValidatesKategoriDepartemen;

    public function authorize(): bool
    {
        return true; // publik, tanpa login
    }

    public function rules(): array
    {
        return [
            'nama_guest' => ['required', 'string', 'max:100'],
            'kontak_guest' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:100',
                // Hanya email yang sudah terdaftar sebagai pengguna aktif
                // (anggota perusahaan) yang boleh membuat laporan guest.
                Rule::exists('users', 'email')->where(fn ($q) => $q->where('is_active', true)),
            ],
            'departemen_id' => ['required', 'exists:departemens,id'],
            'kategori_id' => ['required', 'exists:kategoris,id'],
            'judul' => ['required', 'string', 'max:150'],
            'deskripsi' => ['required', 'string', 'max:2000'],
            'prioritas' => ['required', 'in:rendah,sedang,tinggi'],
            'evidence' => ['nullable', 'file', 'max:2048', 'mimes:jpg,jpeg,png,pdf'],
            // Honeypot anti-bot sederhana: field tersembunyi di form, harus tetap kosong
            'website' => ['prohibited'],
        ];
    }

    public function messages(): array
    {
        return [
            'kontak_guest.email' => 'Kontak harus berupa alamat email yang valid.',
            'kontak_guest.exists' => 'Email ini belum terdaftar di perusahaan. Hanya email terdaftar yang dapat membuat laporan.',
        ];
    }
}

<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesKategoriDepartemen;
use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
{
    use ValidatesKategoriDepartemen;

    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'departemen_id' => ['required', 'exists:departemens,id'],
            'kategori_id' => ['required', 'exists:kategoris,id'],
            'judul' => ['required', 'string', 'max:150'],
            'deskripsi' => ['required', 'string', 'max:2000'],
            'prioritas' => ['required', 'in:rendah,sedang,tinggi'],
            'evidence' => ['nullable', 'file', 'max:2048', 'mimes:jpg,jpeg,png,pdf'],
        ];
    }

    public function messages(): array
    {
        return [
            'evidence.max' => 'Berkas bukti maksimal berukuran 2MB.',
            'evidence.mimes' => 'Berkas bukti hanya boleh berformat JPG, PNG, atau PDF.',
        ];
    }
}

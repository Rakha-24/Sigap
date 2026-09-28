<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepartemenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'kode' => ['nullable', 'alpha_num', 'max:6', 'unique:departemens,kode'],
            'nama' => ['required', 'string', 'max:100', 'unique:departemens,nama'],
            'deskripsi' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'kode.alpha_num' => 'Kode hanya boleh berisi huruf dan angka.',
            'kode.max' => 'Kode maksimal 6 karakter.',
            'kode.unique' => 'Kode sudah digunakan departemen lain.',
        ];
    }
}

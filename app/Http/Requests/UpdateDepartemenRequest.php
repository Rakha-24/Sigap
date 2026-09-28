<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDepartemenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        $departemenId = $this->route('departemen')->id;

        return [
            'kode' => ['nullable', 'alpha_num', 'max:6', Rule::unique('departemens', 'kode')->ignore($departemenId)],
            'nama' => ['required', 'string', 'max:100', Rule::unique('departemens', 'nama')->ignore($departemenId)],
            'deskripsi' => ['nullable', 'string', 'max:500'],
            'is_active' => ['sometimes', 'boolean'],
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

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
            'nama'      => ['required', 'string', 'max:100', Rule::unique('departemens', 'nama')->ignore($departemenId)],
            'deskripsi' => ['nullable', 'string', 'max:500'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
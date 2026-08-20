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
            'nama'      => ['required', 'string', 'max:100', 'unique:departemens,nama'],
            'deskripsi' => ['nullable', 'string', 'max:500'],
        ];
    }
}
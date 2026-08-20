<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreKategoriRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'departemen_id'   => ['required', 'exists:departemens,id'],
            'nama'            => [
                'required', 'string', 'max:100',
                Rule::unique('kategoris', 'nama')->where(fn ($q) => $q->where('departemen_id', $this->departemen_id)),
            ],
            'default_sla_jam' => ['required', 'integer', 'min:1', 'max:720'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.unique' => 'Kategori dengan nama ini sudah ada di departemen tersebut.',
        ];
    }
}
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'pesan' => ['required', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'pesan.required' => 'Pesan komentar tidak boleh kosong.',
            'pesan.max' => 'Komentar maksimal 2000 karakter.',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRegisteredEmailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower(trim((string) $this->email)),
        ]);
    }

    public function rules(): array
    {
        $registeredEmail = $this->route('registered_email');

        return [
            'email' => ['required', 'string', 'email', 'max:150', Rule::unique('registered_emails', 'email')->ignore($registeredEmail?->id)],
            'nama' => ['nullable', 'string', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Email ini sudah ada di daftar.',
        ];
    }
}

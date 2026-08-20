<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'name'           => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'       => ['required', 'string', 'min:8', 'confirmed'],
            'role'           => ['required', Rule::in(['admin', 'agent', 'user'])],
            'departemen_id'  => ['required_if:role,agent', 'nullable', 'exists:departemens,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'departemen_id.required_if' => 'Agent wajib ditugaskan ke satu departemen.',
        ];
    }
}
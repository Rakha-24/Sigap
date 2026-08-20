<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ResolveTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        $ticket = $this->route('ticket');
        return $this->user()?->role === 'agent'
            && $ticket->assigned_agent_id === $this->user()->id;
    }

    public function rules(): array
    {
        $ticket = $this->route('ticket');

        return [
            'catatan_penyelesaian' => ['required', 'string', 'max:2000'],
            // Wajib file BARU jika tiket belum pernah punya evidence penyelesaian
            'evidence_penyelesaian' => [
                $ticket->file_evidence_penyelesaian ? 'nullable' : 'required',
                'file', 'max:2048', 'mimes:jpg,jpeg,png,pdf',
            ],
        ];
    }

    /**
     * Validasi tambahan lintas-field: jaring pengaman terakhir sebelum
     * status benar-benar diubah menjadi 'resolved'.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $ticket = $this->route('ticket');
            $akanPunyaEvidence = $ticket->file_evidence_penyelesaian
                || $this->hasFile('evidence_penyelesaian');

            if (! $akanPunyaEvidence) {
                $validator->errors()->add(
                    'evidence_penyelesaian',
                    'Tiket tidak dapat diselesaikan tanpa melampirkan bukti penanganan.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'evidence_penyelesaian.required' => 'Bukti penyelesaian wajib dilampirkan sebelum tiket ditandai Resolved.',
        ];
    }
}
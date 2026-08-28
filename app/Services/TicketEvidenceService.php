<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\TicketEvidence;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Menyimpan lampiran bukti tiket langsung sebagai bytea di database (Neon/Postgres).
 *
 * Tidak memakai filesystem sama sekali, sehingga aman pada deployment serverless
 * (Vercel) yang filesystem-nya read-only.
 */
class TicketEvidenceService
{
    /**
     * Simpan (atau timpa jika sudah ada) bukti untuk satu jenis pada satu tiket,
     * lalu kembalikan nilai marker untuk kolom file_evidence_* di tabel tickets.
     */
    public function store(Ticket $ticket, UploadedFile $file, string $jenis): string
    {
        TicketEvidence::updateOrCreate(
            ['ticket_id' => $ticket->id, 'jenis' => $jenis],
            [
                'nama_asli' => $file->getClientOriginalName(),
                'mime'      => $file->getClientMimeType() ?: 'application/octet-stream',
                'ukuran'    => $file->getSize(),
                'data'      => $file->get(),
            ],
        );

        return $file->getClientOriginalName();
    }

    /**
     * Sajikan isi bukti (bytea) sebagai respons unduh dengan MIME yang sesuai.
     */
    public function download(TicketEvidence $evidence, bool $inline = false): StreamedResponse
    {
        return response()->streamDownload(
            function () use ($evidence) {
                echo $evidence->data;
            },
            $evidence->nama_asli ?: 'lampiran',
            [
                'Content-Type'   => $evidence->mime ?: 'application/octet-stream',
                'Content-Length' => (string) $evidence->ukuran,
            ],
            $inline ? 'inline' : 'attachment',
        );
    }
}

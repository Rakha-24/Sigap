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
                'mime' => $file->getClientMimeType() ?: 'application/octet-stream',
                'ukuran' => $file->getSize(),
                // data disimpan sebagai bytea (PG) / blob (SQLite). Model
                // meng-cast kolom ini sebagai 'base64' agar transaksi PDO/Postgres
                // tidak gagal pada byte biner (mis. 0x89) yang bukan UTF8 valid.
                'data' => $file->get(),
            ],
        );

        return $file->getClientOriginalName();
    }

    /**
     * Sajikan isi bukti (bytea) sebagai respons unduh dengan MIME yang sesuai.
     */
    public function download(TicketEvidence $evidence, bool $inline = false): StreamedResponse
    {
        $data = $evidence->data;

        // Jaga-jaga bila data biner tidak bisa didecode (mis. baris lama yang gagal
        // tersimpan): hentikan dengan 404 alih-alih mengalirkan 0 byte dengan
        // header Content-Length yang besar (yang membuat peramban menggantung).
        if (! is_string($data) || $data === '') {
            abort(404, 'Bukti tidak ditemukan.');
        }

        return response()->streamDownload(
            function () use ($data) {
                echo $data;
            },
            $evidence->nama_asli ?: 'lampiran',
            [
                'Content-Type' => $evidence->mime ?: 'application/octet-stream',
                'Content-Length' => (string) strlen($data),
            ],
            $inline ? 'inline' : 'attachment',
        );
    }
}

<?php

namespace App\Http\Requests\Concerns;

use App\Models\Kategori;
use Illuminate\Validation\Validator;

trait ValidatesKategoriDepartemen
{
    /**
     * Tegakkan relasi cascade departemen -> kategori di sisi server.
     * Filter di form (JS) hanya memengaruhi UX; validasi ini mencegah POST
     * manual memakai kategori yang bukan milik departemen yang dipilih.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $kategoriId = $validator->getData()['kategori_id'] ?? null;
            $departemenId = $validator->getData()['departemen_id'] ?? null;

            if (! $kategoriId || ! $departemenId) {
                return;
            }

            $cocok = Kategori::where('id', $kategoriId)
                ->where('departemen_id', $departemenId)
                ->exists();

            if (! $cocok) {
                $validator->errors()->add(
                    'kategori_id',
                    'Kategori yang dipilih tidak tersedia untuk departemen tujuan.'
                );
            }
        });
    }
}

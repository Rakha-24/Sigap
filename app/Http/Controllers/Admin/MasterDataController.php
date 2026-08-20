<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDepartemenRequest;
use App\Http\Requests\UpdateDepartemenRequest;
use App\Models\Departemen;
use Illuminate\Support\Str;

class MasterDataController extends Controller
{
    public function index()
    {
        $departemens = Departemen::withCount('kategoris')
            ->when(request('cari'), fn ($q) => $q->where('nama', 'ilike', '%'.request('cari').'%'))
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        return view('admin.master-data.index', compact('departemens'));
    }

    public function create()
    {
        return view('admin.master-data.create');
    }

    public function store(StoreDepartemenRequest $request)
    {
        Departemen::create([
            'kode'      => $this->generateUniqueKode($request->nama),
            'nama'      => $request->nama,
            'deskripsi' => $request->deskripsi,
            'is_active' => true,
        ]);

        return redirect()
            ->route('admin.master-data.departemen.index')
            ->with('success', 'Departemen baru berhasil ditambahkan.');
    }

    public function edit(Departemen $departemen)
    {
        return view('admin.master-data.edit', compact('departemen'));
    }

    public function update(UpdateDepartemenRequest $request, Departemen $departemen)
    {
        $departemen->update([
            'nama'      => $request->nama,
            'deskripsi' => $request->deskripsi,
            'is_active' => $request->boolean('is_active', $departemen->is_active),
        ]);

        return redirect()
            ->route('admin.master-data.departemen.index')
            ->with('success', "Departemen {$departemen->nama} berhasil diperbarui.");
    }

    public function destroy(Departemen $departemen)
    {
        // Cegah hapus departemen yang masih punya tiket, kategori, atau agent aktif —
        // supaya tidak ada data yatim (tiket/agent kehilangan referensi departemennya).
        if ($departemen->tickets()->exists() || $departemen->kategoris()->exists() || $departemen->agents()->exists()) {
            return back()->with('error',
                "Departemen {$departemen->nama} tidak dapat dihapus karena masih memiliki tiket, kategori, atau agent terkait. Nonaktifkan saja jika sudah tidak dipakai."
            );
        }

        $departemen->delete();

        return back()->with('success', "Departemen {$departemen->nama} berhasil dihapus.");
    }

    /** Kode unik 5-6 huruf dari nama departemen, dengan fallback jika terjadi tabrakan. */
    private function generateUniqueKode(string $nama): string
    {
        $base = Str::upper(Str::of($nama)->replace(' ', '')->substr(0, 6));
        $kode = $base;
        $counter = 1;

        while (Departemen::where('kode', $kode)->exists()) {
            $kode = $base.$counter;
            $counter++;
        }

        return $kode;
    }
}
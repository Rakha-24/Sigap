<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreKategoriRequest;
use App\Http\Requests\UpdateKategoriRequest;
use App\Models\Departemen;
use App\Models\Kategori;

class KategoriController extends Controller
{
    public function index()
    {
        $kategoris = Kategori::with('departemen')
            ->when(request('departemen_id'), fn ($q) => $q->where('departemen_id', request('departemen_id')))
            ->when(request('cari'), fn ($q) => $q->where('nama', 'ilike', '%'.request('cari').'%'))
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        return view('admin.master-data.kategori.index', [
            'kategoris'   => $kategoris,
            'departemens' => Departemen::orderBy('nama')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.master-data.kategori.create', [
            'departemens' => Departemen::where('is_active', true)->orderBy('nama')->get(),
        ]);
    }

    public function store(StoreKategoriRequest $request)
    {
        Kategori::create($request->only('departemen_id', 'nama', 'default_sla_jam'));

        return redirect()
            ->route('admin.master-data.kategori.index')
            ->with('success', 'Kategori baru berhasil ditambahkan.');
    }

    public function edit(Kategori $kategori)
    {
        return view('admin.master-data.kategori.edit', [
            'kategori'    => $kategori,
            'departemens' => Departemen::where('is_active', true)->orderBy('nama')->get(),
        ]);
    }

    public function update(UpdateKategoriRequest $request, Kategori $kategori)
    {
        $kategori->update($request->only('departemen_id', 'nama', 'default_sla_jam'));

        return redirect()
            ->route('admin.master-data.kategori.index')
            ->with('success', "Kategori {$kategori->nama} berhasil diperbarui.");
    }

    public function destroy(Kategori $kategori)
    {
        // Cegah hapus kategori yang masih dipakai tiket, supaya tiket lama tidak kehilangan referensi.
        if ($kategori->tickets()->exists()) {
            return back()->with('error',
                "Kategori {$kategori->nama} tidak dapat dihapus karena masih dipakai oleh tiket yang ada."
            );
        }

        $kategori->delete();

        return back()->with('success', "Kategori {$kategori->nama} berhasil dihapus.");
    }

    /** Tab "SLA & Prioritas": ringkasan SLA (jam) tiap kategori, dikelompokkan per departemen — read-only. */
    public function slaOverview()
    {
        $departemens = Departemen::with(['kategoris' => fn ($q) => $q->orderBy('nama')])
            ->where('is_active', true)
            ->orderBy('nama')
            ->get();

        return view('admin.master-data.sla-overview', compact('departemens'));
    }
}
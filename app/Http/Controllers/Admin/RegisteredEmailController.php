<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRegisteredEmailRequest;
use App\Http\Requests\UpdateRegisteredEmailRequest;
use App\Models\RegisteredEmail;

class RegisteredEmailController extends Controller
{
    public function index()
    {
        $emails = RegisteredEmail::when(request('cari'), fn ($q) => $q->where('email', 'ilike', '%'.request('cari').'%'))
            ->when(request('status') === 'aktif', fn ($q) => $q->where('is_active', true))
            ->when(request('status') === 'nonaktif', fn ($q) => $q->where('is_active', false))
            ->orderBy('email')
            ->paginate(10)
            ->withQueryString();

        return view('admin.master-data.email.index', compact('emails'));
    }

    public function create()
    {
        return view('admin.master-data.email.create');
    }

    public function store(StoreRegisteredEmailRequest $request)
    {
        RegisteredEmail::create([
            'email' => $request->email,
            'nama' => $request->nama,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('admin.master-data.email.index')
            ->with('success', 'Email berhasil ditambahkan ke daftar.');
    }

    public function edit(RegisteredEmail $registeredEmail)
    {
        return view('admin.master-data.email.edit', ['email' => $registeredEmail]);
    }

    public function update(UpdateRegisteredEmailRequest $request, RegisteredEmail $registeredEmail)
    {
        $registeredEmail->update([
            'email' => $request->email,
            'nama' => $request->nama,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.master-data.email.index')
            ->with('success', "Email {$registeredEmail->email} berhasil diperbarui.");
    }

    public function destroy(RegisteredEmail $registeredEmail)
    {
        $registeredEmail->delete();

        return back()->with('success', "Email {$registeredEmail->email} berhasil dihapus dari daftar.");
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Departemen;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::with('departemen')
            ->when(request('role'), fn ($q) => $q->where('role', request('role')))
            ->when(request('cari'), fn ($q) => $q->where('name', 'ilike', '%'.request('cari').'%')
                ->orWhere('email', 'ilike', '%'.request('cari').'%'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create', [
            'departemens' => Departemen::where('is_active', true)->get(),
        ]);
    }

    public function store(StoreUserRequest $request)
    {
        User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'role'          => $request->role,
            'departemen_id' => $request->role === 'agent' ? $request->departemen_id : null,
            'is_active'     => true,
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', [
            'user'        => $user,
            'departemens' => Departemen::where('is_active', true)->get(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $user->name          = $request->name;
        $user->email         = $request->email;
        $user->role          = $request->role;
        $user->departemen_id = $request->role === 'agent' ? $request->departemen_id : null;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Data {$user->name} berhasil diperbarui.");
    }

    /** Nonaktifkan/aktifkan akun — dipakai alih-alih hapus permanen agar riwayat tiket & audit log tetap utuh. */
    public function toggleActive(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('success', $user->is_active
            ? "{$user->name} diaktifkan kembali."
            : "{$user->name} dinonaktifkan."
        );
    }
}
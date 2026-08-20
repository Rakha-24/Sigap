@extends('layouts.app')
@section('title', 'Manajemen Pengguna')

@section('content')
<section id="sigap-admin-users" class="sigap-page">
    <div class="sigap-page__header">
        <div>
            <h1 class="sigap-page__title">Manajemen Pengguna</h1>
            <p class="sigap-page__subtitle">Kelola akun admin, agent, dan user internal.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="sigap-btn sigap-btn--primary" id="sigap-admin-users__add">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Tambah Pengguna
        </a>
    </div>

    <form method="GET" action="{{ route('admin.users.index') }}" class="sigap-toolbar" id="sigap-admin-users__filter">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" name="cari" class="sigap-form__input !pl-9 !w-64" placeholder="Cari nama atau email..."
                   value="{{ request('cari') }}">
        </div>
        <select name="role" class="sigap-form__select !w-auto" onchange="this.form.submit()">
            <option value="">Semua Role</option>
            @foreach(['admin' => 'Admin', 'agent' => 'Agent', 'user' => 'User'] as $val => $label)
                <option value="{{ $val }}" {{ request('role') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="sigap-btn sigap-btn--secondary">Filter</button>
    </form>

    <div class="sigap-table-wrapper" id="sigap-admin-users__table">
        <table class="sigap-table">
            <thead>
                <tr>
                    <th>Pengguna</th><th>Email</th><th>Role</th><th>Departemen</th><th>Status</th><th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <span class="sigap-avatar sigap-avatar--sm">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                <span class="font-medium text-slate-800">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="text-slate-500">{{ $user->email }}</td>
                        <td><span class="sigap-badge sigap-badge--role-{{ $user->role }}">{{ ucfirst($user->role) }}</span></td>
                        <td class="text-slate-500">{{ $user->departemen->nama ?? '-' }}</td>
                        <td>
                            <span class="sigap-badge sigap-badge--{{ $user->is_active ? 'active' : 'inactive' }}">
                                {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="sigap-table__actions">
                            <a href="{{ route('admin.users.edit', $user) }}" class="sigap-icon-btn" title="Edit">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5Z"/>
                                </svg>
                            </a>
                            <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="sigap-icon-btn" title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    @if($user->is_active)
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                                            <line x1="12" y1="9" x2="12" y2="13"/>
                                            <line x1="12" y1="17" x2="12.01" y2="17"/>
                                        </svg>
                                    @else
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                            <polyline points="22 4 12 14.01 9 11.01"/>
                                        </svg>
                                    @endif
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="sigap-empty">
                                <svg class="sigap-empty__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                </svg>
                                <p class="sigap-empty__text">Tidak ada pengguna ditemukan.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $users->links() }}</div>
</section>
@endsection
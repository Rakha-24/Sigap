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
            + Tambah Pengguna
        </a>
    </div>

    @if (session('success'))
        <div class="sigap-alert sigap-alert--success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="sigap-alert sigap-alert--error">{{ session('error') }}</div>
    @endif

    <form method="GET" action="{{ route('admin.users.index') }}" class="sigap-toolbar" id="sigap-admin-users__filter">
        <input type="text" name="cari" class="sigap-form__input" placeholder="Cari nama atau email..."
               value="{{ request('cari') }}">
        <select name="role" class="sigap-form__select" onchange="this.form.submit()">
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
                    <th>Nama</th><th>Email</th><th>Role</th><th>Departemen</th><th>Status</th><th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td><span class="sigap-badge sigap-badge--role-{{ $user->role }}">{{ ucfirst($user->role) }}</span></td>
                        <td>{{ $user->departemen->nama ?? '-' }}</td>
                        <td>
                            <span class="sigap-badge sigap-badge--{{ $user->is_active ? 'active' : 'inactive' }}">
                                {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="sigap-table__actions">
                            <a href="{{ route('admin.users.edit', $user) }}" title="Edit">✏️</a>
                            <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}" style="display:inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    {{ $user->is_active ? '🚫' : '✅' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{ $users->links() }}
</section>
@endsection
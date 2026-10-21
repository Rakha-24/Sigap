@extends('layouts.app')
@section('title', 'Email Terdaftar')

@section('content')
<section id="sigap-admin-master-data-email" class="sigap-page">
    <div class="sigap-page__header">
        <div>
            <h1 class="sigap-page__title">Manajemen Master Data</h1>
            <p class="sigap-page__subtitle">Kelola data referensi utama sistem.</p>
        </div>
        <a href="{{ route('admin.master-data.email.create') }}" class="sigap-btn sigap-btn--primary" id="sigap-admin-master-data-email__add">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Tambah Email
        </a>
    </div>

    @include('admin.master-data._tabs')

    <div class="sigap-card" id="sigap-admin-master-data-email__list">
        <div class="sigap-card__header">
            <h2 class="text-lg font-semibold text-slate-900">Daftar Email yang Boleh Melapor</h2>
            <form method="GET" action="{{ route('admin.master-data.email.index') }}" class="sigap-toolbar">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input type="text" name="cari" class="sigap-form__input !pl-9 !w-56" placeholder="Cari email..."
                           value="{{ request('cari') }}">
                </div>
                <select name="status" class="sigap-form__select !w-auto" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </form>
        </div>

        <div class="sigap-table-wrapper !border-0 !shadow-none !rounded-none">
            <table class="sigap-table">
                <thead>
                    <tr><th>Email</th><th>Nama</th><th>Status</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse($emails as $email)
                        <tr>
                            <td class="font-medium text-slate-800">{{ $email->email }}</td>
                            <td class="text-slate-500">{{ $email->nama ?? '-' }}</td>
                            <td>
                                @if($email->is_active)
                                    <span class="sigap-badge sigap-badge--role-agent">Aktif</span>
                                @else
                                    <span class="sigap-badge sigap-badge--role-user">Nonaktif</span>
                                @endif
                            </td>
                            <td class="sigap-table__actions">
                                <a href="{{ route('admin.master-data.email.edit', $email) }}" class="sigap-icon-btn" title="Edit">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5Z"/>
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('admin.master-data.email.destroy', $email) }}"
                                      onsubmit="return confirm('Hapus email {{ $email->email }} dari daftar?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="sigap-icon-btn hover:!text-red-600 hover:!bg-red-50" title="Hapus">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="3 6 5 6 21 6"/>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                            <line x1="10" y1="11" x2="10" y2="17"/>
                                            <line x1="14" y1="11" x2="14" y2="17"/>
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="sigap-empty">
                                    <p class="sigap-empty__text">Belum ada email terdaftar. Guest tidak akan bisa melapor sampai email ditambahkan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>{{ $emails->links() }}</div>
    </div>
</section>
@endsection

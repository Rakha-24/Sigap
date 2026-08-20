@extends('layouts.app')
@section('title', 'Daftar Tiket')

@section('content')
<section id="sigap-admin-tickets" class="sigap-page">
    <div class="sigap-page__header">
        <div>
            <h1 class="sigap-page__title">Daftar Tiket</h1>
            <p class="sigap-page__subtitle">Pantau seluruh tiket masuk, yang masih berjalan maupun yang sudah selesai.</p>
        </div>
    </div>

    <div class="sigap-tabbar" id="sigap-admin-tickets__tabs">
        @foreach(['semua' => 'Semua', 'belum' => 'Belum Selesai', 'selesai' => 'Selesai'] as $val => $label)
            <a href="{{ route('admin.tickets.index', ['status' => $val, 'cari' => request('cari')]) }}"
               class="sigap-tabbar__tab {{ $status === $val ? 'sigap-tabbar__tab--active' : '' }}">
                {{ $label }}
                <span class="sigap-badge sigap-badge--{{ $val === 'semua' ? 'closed' : ($val === 'belum' ? 'in_progress' : 'resolved') }}">
                    {{ $counts[$val] }}
                </span>
            </a>
        @endforeach
    </div>

    <form method="GET" action="{{ route('admin.tickets.index') }}" class="sigap-toolbar" id="sigap-admin-tickets__filter">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" name="cari" class="sigap-form__input !pl-9 !w-64" placeholder="Cari nomor, judul, atau pelapor..."
                   value="{{ request('cari') }}">
        </div>
        <input type="hidden" name="status" value="{{ $status }}">
        <button type="submit" class="sigap-btn sigap-btn--secondary">Filter</button>
    </form>

    <div class="sigap-table-wrapper" id="sigap-admin-tickets__table">
        <table class="sigap-table">
            <thead>
                <tr>
                    <th>Nomor Tiket</th>
                    <th>Judul</th>
                    <th>Departemen</th>
                    <th>Prioritas</th>
                    <th>Status</th>
                    <th>Pelapor</th>
                    <th>Tenggat SLA</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $ticket)
                    <tr>
                        <td class="font-mono text-xs font-semibold text-slate-700">{{ $ticket->nomor_tiket }}</td>
                        <td>
                            <span class="font-medium text-slate-800">{{ $ticket->judul }}</span>
                        </td>
                        <td class="text-slate-500">{{ $ticket->departemen->nama ?? '-' }}</td>
                        <td>
                            <span class="sigap-badge sigap-badge--priority-{{ $ticket->prioritas }}">Prioritas {{ ucfirst($ticket->prioritas) }}</span>
                        </td>
                        <td>
                            <span class="sigap-badge sigap-badge--{{ $ticket->status }}">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span>
                        </td>
                        <td class="text-slate-500">
                            {{ $ticket->nama_guest ?? $ticket->pelapor?->name ?? '-' }}
                        </td>
                        <td class="text-slate-500">
                            @if($ticket->sla_target_at)
                                <span class="{{ $ticket->isSlaBreached() ? 'text-red-600 font-medium' : '' }}">
                                    {{ $ticket->sla_target_at->format('d M Y, H:i') }}
                                </span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="sigap-table__actions">
                            <a href="{{ route('tickets.show', $ticket) }}" class="sigap-icon-btn" title="Lihat Detail">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <div class="sigap-empty">
                                <svg class="sigap-empty__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                    <path d="M9 13h6"/>
                                    <path d="M9 17h6"/>
                                </svg>
                                <p class="sigap-empty__text">Tidak ada tiket ditemukan.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $tickets->links() }}</div>
</section>
@endsection
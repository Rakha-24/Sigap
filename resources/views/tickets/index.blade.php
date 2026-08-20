@extends('layouts.app')
@section('title', 'Tiket Saya')

@section('content')
<section id="sigap-tickets-index" class="sigap-page">
    <div class="sigap-page__header">
        <div>
            <h1 class="sigap-page__title">Tiket Saya</h1>
            <p class="sigap-page__subtitle">Seluruh laporan yang pernah Anda buat.</p>
        </div>
        <a href="{{ route('tickets.create') }}" class="sigap-btn sigap-btn--primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Buat Tiket Baru
        </a>
    </div>

    <div class="sigap-table-wrapper" id="sigap-tickets-index__table">
        <table class="sigap-table">
            <thead>
                <tr>
                    <th>Nomor Tiket</th>
                    <th>Judul</th>
                    <th>Departemen</th>
                    <th>Kategori</th>
                    <th>Prioritas</th>
                    <th>Status</th>
                    <th>Dibuat</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $ticket)
                    <tr>
                        <td class="font-mono text-slate-500 text-xs">{{ $ticket->nomor_tiket }}</td>
                        <td>
                            <a href="{{ route('tickets.show', $ticket) }}" class="font-medium text-slate-800 hover:text-sigap-700 transition-colors">
                                {{ $ticket->judul }}
                            </a>
                        </td>
                        <td class="text-slate-500">{{ $ticket->departemen->nama ?? '-' }}</td>
                        <td class="text-slate-500">{{ $ticket->kategori->nama ?? '-' }}</td>
                        <td><span class="sigap-badge sigap-badge--priority-{{ $ticket->prioritas }}">{{ ucfirst($ticket->prioritas) }}</span></td>
                        <td><span class="sigap-badge sigap-badge--{{ $ticket->status }}">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span></td>
                        <td class="text-slate-500 text-sm whitespace-nowrap">{{ $ticket->created_at->format('d M Y') }}</td>
                        <td class="sigap-table__actions">
                            <a href="{{ route('tickets.show', $ticket) }}" class="sigap-btn sigap-btn--sm sigap-btn--secondary">
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <div class="sigap-empty">
                                <svg class="sigap-empty__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/>
                                    <path d="M14 2v4a1 1 0 0 0 1 1h3"/>
                                    <line x1="9" y1="15" x2="15" y2="15"/>
                                </svg>
                                <p class="sigap-empty__text">Belum ada tiket.</p>
                                <p class="sigap-empty__hint">Buat tiket pertama Anda untuk memulai.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($tickets->hasPages())
        <div>{{ $tickets->links() }}</div>
    @endif
</section>
@endsection
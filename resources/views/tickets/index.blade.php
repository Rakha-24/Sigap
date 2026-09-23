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

    <div class="sigap-tabbar" id="sigap-tickets-index__tabs">
        @php
            $currentStatus = request()->query('status');
            $tabs = [
                '' => ['label' => 'Semua', 'badge' => 'closed'],
                'open' => ['label' => 'Menunggu', 'badge' => 'open'],
                'in_progress' => ['label' => 'Diproses', 'badge' => 'in_progress'],
                'resolved' => ['label' => 'Selesai', 'badge' => 'resolved'],
            ];
        @endphp
        @foreach($tabs as $val => $tab)
            <a href="{{ route('tickets.index', array_filter(['status' => $val ?: null, 'cari' => request('cari'), 'prioritas' => request('prioritas')], fn($v) => $v !== null)) }}"
               class="sigap-tabbar__tab {{ ($currentStatus ?: '') === $val ? 'sigap-tabbar__tab--active' : '' }}">
                {{ $tab['label'] }}
            </a>
        @endforeach
    </div>

    <form method="GET" action="{{ route('tickets.index') }}" class="sigap-toolbar" id="sigap-tickets-index__filter">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" name="cari" class="sigap-form__input !pl-9 !w-64" placeholder="Cari nomor tiket atau judul..."
                   value="{{ request('cari') }}">
        </div>
        <select name="status" class="sigap-form__select !w-auto" aria-label="Filter status">
            <option value="">Semua status</option>
            <option value="open" @selected(request('status') === 'open')>Menunggu</option>
            <option value="in_progress" @selected(request('status') === 'in_progress')>Diproses</option>
            <option value="resolved" @selected(request('status') === 'resolved')>Selesai</option>
            <option value="closed" @selected(request('status') === 'closed')>Ditutup</option>
        </select>
        <select name="prioritas" class="sigap-form__select !w-auto" aria-label="Filter prioritas">
            <option value="">Semua prioritas</option>
            <option value="tinggi" @selected(request('prioritas') === 'tinggi')>Tinggi</option>
            <option value="sedang" @selected(request('prioritas') === 'sedang')>Sedang</option>
            <option value="rendah" @selected(request('prioritas') === 'rendah')>Rendah</option>
        </select>
        <button type="submit" class="sigap-btn sigap-btn--secondary">Terapkan</button>
        @if(request()->hasAny(['cari', 'status', 'prioritas']))
            <a href="{{ route('tickets.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-700">Reset</a>
        @endif
    </form>

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
@extends('layouts.app')
@section('title', 'Dashboard Admin - SIGAP')

@section('content')
<section id="sigap-admin-dashboard" class="sigap-page">
    <div class="sigap-page__header">
        <div>
            <h1 class="sigap-page__title">Dashboard</h1>
            <p class="sigap-page__subtitle">Ringkasan aktivitas tiket terkini.</p>
        </div>
    </div>

    <div class="sigap-stats-grid" id="sigap-admin-dashboard__stats">
        <div class="sigap-stat-card">
            <div class="sigap-stat-card__icon sigap-stat-card__icon--blue">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/>
                    <path d="M14 2v4a1 1 0 0 0 1 1h3"/>
                </svg>
            </div>
            <span class="sigap-stat-card__label">Total Tiket</span>
            <span class="sigap-stat-card__value">{{ $stats['total'] }}</span>
        </div>
        <div class="sigap-stat-card">
            <div class="sigap-stat-card__icon sigap-stat-card__icon--amber">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
            </div>
            <span class="sigap-stat-card__label">Menunggu</span>
            <span class="sigap-stat-card__value">{{ $stats['open'] }}</span>
        </div>
        <div class="sigap-stat-card">
            <div class="sigap-stat-card__icon sigap-stat-card__icon--emerald">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
            <span class="sigap-stat-card__label">Selesai</span>
            <span class="sigap-stat-card__value">{{ $stats['resolved'] }}</span>
        </div>
    </div>

    @if($slaWarning->count())
        <div class="sigap-card" id="sigap-admin-dashboard__sla-warning">
            <div class="sigap-card__header">
                <span class="text-sm font-semibold text-slate-900">Peringatan SLA</span>
                <span class="text-xs text-amber-600 font-medium">Segera melewati batas waktu</span>
            </div>
            <div class="sigap-table-wrapper">
                <table class="sigap-table">
                    <thead>
                        <tr>
                            <th>Nomor Tiket</th>
                            <th>Judul</th>
                            <th>Departemen</th>
                            <th>Target SLA</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($slaWarning as $ticket)
                            <tr>
                                <td class="font-mono text-xs">{{ $ticket->nomor_tiket }}</td>
                                <td>{{ Str::limit($ticket->judul, 40) }}</td>
                                <td>{{ $ticket->departemen->nama ?? '-' }}</td>
                                <td class="text-xs">{{ $ticket->sla_target_at->format('d M Y, H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="sigap-chart-grid" id="sigap-admin-dashboard__content">
        <div class="sigap-card">
            <div class="sigap-card__header">
                <span class="text-sm font-semibold text-slate-900">Tiket Terbaru</span>
                <a href="{{ route('admin.tickets.index') }}" class="text-xs font-medium text-sigap-600 hover:text-sigap-700">Lihat Semua</a>
            </div>
            <div class="sigap-table-wrapper">
                <table class="sigap-table">
                    <thead>
                        <tr>
                            <th>Nomor</th>
                            <th>Judul</th>
                            <th>Status</th>
                            <th>Prioritas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTickets as $ticket)
                            <tr>
                                <td class="font-mono text-xs">{{ $ticket->nomor_tiket }}</td>
                                <td>{{ Str::limit($ticket->judul, 35) }}</td>
                                <td>
                                    <span class="sigap-badge sigap-badge--{{ $ticket->status }}">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span>
                                </td>
                                <td>
                                    <span class="sigap-badge sigap-badge--priority-{{ $ticket->prioritas }}">{{ ucfirst($ticket->prioritas) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="sigap-empty">
                                        <p class="sigap-empty__text">Belum ada tiket masuk.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="sigap-card">
            <div class="sigap-card__header">
                <span class="text-sm font-semibold text-slate-900">Tiket per Departemen</span>
            </div>
            @if($perDepartemen->count())
                <div class="flex flex-col gap-3">
                    @foreach($perDepartemen as $dept)
                        @php
                            $pct = $stats['total'] > 0 ? round(($dept->jumlah / $stats['total']) * 100) : 0;
                        @endphp
                        <div class="flex items-center gap-3">
                            <span class="text-sm text-slate-700 w-32 truncate" title="{{ $dept->departemen->nama ?? '-' }}">{{ $dept->departemen->nama ?? '-' }}</span>
                            <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-sigap-500 rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                            <span class="text-xs font-semibold text-slate-500 w-10 text-right">{{ $dept->jumlah }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="sigap-empty">
                    <p class="sigap-empty__text">Belum ada data tiket.</p>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection

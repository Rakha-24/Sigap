@extends('layouts.app')
@section('title', 'Dashboard - SIGAP')

@section('content')
<section id="sigap-agent-dashboard" class="sigap-page">
    <div class="sigap-page__header">
        <div>
            <h1 class="sigap-page__title">Dashboard</h1>
            <p class="sigap-page__subtitle">Ringkasan performa dan tugas Anda hari ini.</p>
        </div>
        <a href="{{ route('agent.queue') }}" class="sigap-btn sigap-btn--secondary">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="4" rx="1"/>
                <rect x="3" y="10" width="18" height="4" rx="1"/>
                <rect x="3" y="16" width="18" height="4" rx="1"/>
            </svg>
            Buka Antrean Tiket
        </a>
    </div>

    {{-- Statistik --}}
    <div class="sigap-stats-grid sigap-stats-grid--4" id="sigap-agent-dashboard__stats">
        <div class="sigap-stat-card">
            <div class="sigap-stat-card__icon sigap-stat-card__icon--blue">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/>
                    <path d="M14 2v4a1 1 0 0 0 1 1h3"/>
                    <line x1="8" y1="13" x2="16" y2="13"/>
                    <line x1="8" y1="17" x2="13" y2="17"/>
                </svg>
            </div>
            <span class="sigap-stat-card__label">Total Tiket Ditugaskan</span>
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
            <span class="sigap-stat-card__label">Sedang Dikerjakan</span>
            <span class="sigap-stat-card__value">{{ $stats['in_progress'] }}</span>
        </div>
        <div class="sigap-stat-card">
            <div class="sigap-stat-card__icon sigap-stat-card__icon--emerald">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
            <span class="sigap-stat-card__label">Selesai Hari Ini</span>
            <span class="sigap-stat-card__value">{{ $stats['selesai_hari_ini'] }}</span>
        </div>
        <div class="sigap-stat-card">
            <div class="sigap-stat-card__icon sigap-stat-card__icon--red">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
            </div>
            <span class="sigap-stat-card__label">Melewati Target SLA</span>
            <span class="sigap-stat-card__value">{{ $stats['sla_terlampaui'] }}</span>
        </div>
    </div>

    {{-- Tiket prioritas --}}
    <div class="sigap-card" id="sigap-agent-dashboard__priority">
        <div class="sigap-card__header">
            <span class="text-sm font-semibold text-slate-900">Tiket Prioritas Anda</span>
            <a href="{{ route('agent.queue') }}" class="text-xs font-medium text-sigap-600 hover:text-sigap-700">Lihat Semua</a>
        </div>
        <div class="sigap-table-wrapper">
            <table class="sigap-table">
                <thead>
                    <tr>
                        <th>ID Tiket</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Sisa SLA</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tiketPrioritas as $ticket)
                        <tr>
                            <td>
                                <a href="{{ route('tickets.show', $ticket) }}" class="font-mono text-xs text-sigap-600 hover:text-sigap-700">
                                    {{ $ticket->nomor_tiket }}
                                </a>
                            </td>
                            <td>{{ $ticket->kategori->nama ?? '-' }}</td>
                            <td>
                                <span class="sigap-badge sigap-badge--{{ $ticket->status }}">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span>
                            </td>
                            <td>
                                @if($ticket->isSlaBreached())
                                    <span class="sigap-badge sigap-badge--closed text-amber-600">SLA terlampaui</span>
                                @elseif($ticket->sla_target_at)
                                    <span class="text-xs text-slate-500">{{ $ticket->sla_target_at->diffForHumans(null, true) }}</span>
                                @else
                                    <span class="text-xs text-slate-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="sigap-empty">
                                    <p class="sigap-empty__text">Tidak ada tiket prioritas yang sedang Anda kerjakan.</p>
                                    <p class="sigap-empty__hint">Ambil tiket dari antrean untuk mulai bekerja.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
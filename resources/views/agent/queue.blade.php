@extends('layouts.app')
@section('title', 'Antrean Tiket Prioritas')

@section('content')
<section id="sigap-agent-queue" class="sigap-page">
    <div class="sigap-page__header">
        <div>
            <h1 class="sigap-page__title">Antrean Tiket</h1>
            <p class="sigap-page__subtitle">Daftar tiket yang membutuhkan penanganan segera, diurutkan berdasarkan prioritas dan sisa SLA.</p>
        </div>
        <span class="sigap-badge sigap-badge--role-agent">{{ $tickets->count() }} tiket menunggu</span>
    </div>

    <div class="sigap-queue-list" id="sigap-agent-queue__list">
        @forelse($tickets as $ticket)
            @php
                $persenSla = 100;
                if ($ticket->sla_target_at) {
                    $total = $ticket->sla_target_at->diffInMinutes($ticket->created_at, false) ?: 1;
                    $sisa  = $ticket->sla_target_at->diffInMinutes(now(), false);
                    $persenSla = max(0, min(100, round(($sisa / $total) * 100)));
                }
            @endphp
            <div class="sigap-queue-card sigap-queue-card--priority-{{ $ticket->prioritas }}" id="sigap-queue-card-{{ $ticket->id }}">
                <div class="flex flex-col gap-3 flex-1 min-w-0">
                    <a href="{{ route('tickets.show', $ticket) }}" class="sigap-queue-card__title">
                        <span class="font-mono text-slate-400 font-normal">#{{ $ticket->nomor_tiket }}</span>
                        · {{ $ticket->judul }}
                    </a>
                    <div class="sigap-queue-card__tags">
                        <span class="sigap-badge sigap-badge--priority-{{ $ticket->prioritas }}">Prioritas {{ ucfirst($ticket->prioritas) }}</span>
                        <span class="sigap-badge sigap-badge--{{ $ticket->status }}">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span>
                        <span class="sigap-queue-card__dept">{{ $ticket->departemen->nama }}</span>
                    </div>
                    <div class="sigap-queue-card__meta">
                        <span class="sigap-queue-card__time">Masuk: {{ $ticket->created_at->format('d M Y, H:i') }} WIB</span>
                        <span class="sigap-queue-card__sla sigap-queue-card__sla--{{ $ticket->isSlaBreached() ? 'breach' : 'ok' }}">
                            {{ $ticket->isSlaBreached() ? 'SLA terlampaui' : 'Sisa SLA: ' . $ticket->sla_target_at?->diffForHumans(null, true) }}
                        </span>
                    </div>
                    @if($ticket->sla_target_at)
                        <div class="sigap-progress max-w-sm">
                            <div class="sigap-progress__track">
                                <div class="sigap-progress__fill {{ $persenSla <= 30 ? 'sigap-progress__fill--red' : ($persenSla <= 50 ? 'sigap-progress__fill--amber' : '') }}"
                                     style="width: {{ $persenSla }}%"></div>
                            </div>
                            <div class="sigap-progress__labels">
                                <span>Sisa waktu SLA</span>
                                <span>{{ $persenSla }}%</span>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="shrink-0">
                    @if($ticket->assigned_agent_id === auth()->id())
                        <a href="{{ route('tickets.show', $ticket) }}" class="sigap-btn sigap-btn--primary" id="sigap-queue-card-{{ $ticket->id }}__continue">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="5 12 19 12"/>
                                <polyline points="12 5 19 12 12 19"/>
                            </svg>
                            Lanjutkan
                        </a>
                    @else
                        <form method="POST" action="{{ route('agent.tickets.claim', $ticket) }}"
                              id="sigap-queue-card-{{ $ticket->id }}__claim-form">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="sigap-btn sigap-btn--primary">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                                Kerjakan
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="sigap-empty">
                <svg class="sigap-empty__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/>
                    <path d="M14 2v4a1 1 0 0 0 1 1h3"/>
                    <line x1="9" y1="15" x2="15" y2="15"/>
                </svg>
                <p class="sigap-empty__text">Tidak ada tiket dalam antrean.</p>
                <p class="sigap-empty__hint">Semua tiket di departemen Anda sudah ditangani.</p>
            </div>
        @endforelse
    </div>
</section>
@endsection
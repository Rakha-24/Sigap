@extends('layouts.app')
@section('title', 'Antrean Tiket Prioritas')

@section('content')
<section id="sigap-agent-queue" class="sigap-page">
    <h1 class="sigap-page__title">Antrean Tiket Prioritas</h1>
    <p class="sigap-page__subtitle">Daftar tiket yang membutuhkan penanganan segera (High Priority).</p>

    @if (session('success'))
        <div class="sigap-alert sigap-alert--success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="sigap-alert sigap-alert--error">{{ session('error') }}</div>
    @endif

    <div class="sigap-queue-list" id="sigap-agent-queue__list">
        @forelse($tickets as $ticket)
            <div class="sigap-queue-card" id="sigap-queue-card-{{ $ticket->id }}">
                <div class="sigap-queue-card__meta">
                    <span class="sigap-queue-card__time">Waktu Masuk: {{ $ticket->created_at->format('H:i') }} WIB</span>
                    <span class="sigap-queue-card__sla sigap-queue-card__sla--{{ $ticket->isSlaBreached() ? 'breach' : 'ok' }}">
                        Sisa SLA: {{ $ticket->sla_target_at?->diffForHumans(null, true) }}
                    </span>
                </div>
                <a href="{{ route('tickets.show', $ticket) }}" class="sigap-queue-card__title">
                    #{{ $ticket->nomor_tiket }}: {{ $ticket->judul }}
                </a>
                <div class="sigap-queue-card__tags">
                    <span class="sigap-badge sigap-badge--priority-{{ $ticket->prioritas }}">{{ ucfirst($ticket->prioritas) }}</span>
                    <span class="sigap-queue-card__dept">{{ $ticket->departemen->nama }}</span>
                </div>

                @if($ticket->assigned_agent_id === auth()->id())
                    <a href="{{ route('tickets.show', $ticket) }}" class="sigap-btn sigap-btn--primary" id="sigap-queue-card-{{ $ticket->id }}__continue">
                        Lanjutkan
                    </a>
                @else
                    <form method="POST" action="{{ route('agent.tickets.claim', $ticket) }}"
                          id="sigap-queue-card-{{ $ticket->id }}__claim-form" style="display:inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="sigap-btn sigap-btn--primary">Kerjakan</button>
                    </form>
                @endif
            </div>
        @empty
            <p>Tidak ada tiket dalam antrean.</p>
        @endforelse
    </div>
</section>
@endsection
@extends('layouts.guest')
@section('title', "Tiket {$ticket->nomor_tiket} - SIGAP")

@section('content')
<section id="sigap-guest-track-result" class="sigap-track-result">
    <header class="sigap-track-result__header">
        <span class="sigap-avatar">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/>
                <path d="M14 2v4a1 1 0 0 0 1 1h3"/>
            </svg>
        </span>
        <div>
            <h1 class="sigap-track-result__title">{{ $ticket->judul }}</h1>
            <span class="sigap-track-result__nomor">{{ $ticket->nomor_tiket }}</span>
        </div>
        <div class="ml-auto flex items-center gap-2">
            <span class="sigap-badge sigap-badge--priority-{{ $ticket->prioritas }}">{{ ucfirst($ticket->prioritas) }}</span>
            <span class="sigap-badge sigap-badge--{{ $ticket->status }}">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span>
        </div>
    </header>

    <div class="sigap-track-result__grid">
        <div class="sigap-track-result__detail" id="sigap-guest-track-result__detail">
            <h2 class="text-lg font-semibold text-slate-900">Detail Laporan</h2>
            <div class="sigap-detail-grid">
                <div class="sigap-detail-field">
                    <span class="sigap-detail-field__label">Departemen</span>
                    <span class="sigap-detail-field__value">{{ $ticket->departemen->nama ?? '-' }}</span>
                </div>
                <div class="sigap-detail-field">
                    <span class="sigap-detail-field__label">Kategori</span>
                    <span class="sigap-detail-field__value">{{ $ticket->kategori->nama ?? '-' }}</span>
                </div>
                <div class="sigap-detail-field">
                    <span class="sigap-detail-field__label">Dibuat</span>
                    <span class="sigap-detail-field__value">{{ $ticket->created_at->format('d M Y, H:i') }}</span>
                </div>
                <div class="sigap-detail-field">
                    <span class="sigap-detail-field__label">Target SLA</span>
                    <span class="sigap-detail-field__value">{{ $ticket->sla_target_at?->format('d M Y, H:i') ?? '-' }}</span>
                </div>
            </div>
            <div class="sigap-divider"></div>
            <p class="text-sm text-slate-600 leading-relaxed whitespace-pre-line">{{ $ticket->deskripsi }}</p>
        </div>

        <div class="sigap-track-result__comments" id="sigap-guest-track-result__comments">
            <h2 class="text-lg font-semibold text-slate-900">Balasan Teknisi</h2>
            @forelse($ticket->comments as $comment)
                <div class="sigap-comment">
                    <div class="sigap-comment__header">
                        <span class="sigap-comment__avatar">{{ strtoupper(substr($comment->user->name ?? 'T', 0, 1)) }}</span>
                        <div>
                            <span class="sigap-comment__author">{{ $comment->user->name ?? 'Tim SIGAP' }}</span>
                            <p class="sigap-comment__meta">{{ $comment->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <p class="sigap-comment__body">{{ $comment->pesan }}</p>
                </div>
            @empty
                <p class="text-sm text-slate-400">Belum ada balasan dari teknisi.</p>
            @endforelse
        </div>
    </div>

    <div class="sigap-track-result__timeline" id="sigap-guest-track-result__timeline">
        <h2 class="text-lg font-semibold text-slate-900">Riwayat Penanganan</h2>
        @if($ticket->auditLogs->count())
            <ul class="sigap-timeline">
                @foreach($ticket->auditLogs as $log)
                    <li class="sigap-timeline__item">
                        <span class="sigap-timeline__dot {{ in_array($log->aksi, ['resolved', 'closed']) ? 'sigap-timeline__dot--' . $log->aksi : '' }}"></span>
                        <span class="sigap-timeline__time">{{ $log->created_at->format('d M Y, H:i') }}</span>
                        <span class="sigap-timeline__desc">{{ $log->deskripsi }}</span>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-sm text-slate-400">Belum ada riwayat penanganan.</p>
        @endif
    </div>

    <a href="{{ route('guest.track.form') }}" class="sigap-btn sigap-btn--secondary self-start">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"/>
            <polyline points="12 19 5 12 12 5"/>
        </svg>
        Lacak Tiket Lain
    </a>
</section>
@endsection
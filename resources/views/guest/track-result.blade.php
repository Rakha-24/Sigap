@extends('layouts.guest')
@section('title', "Tiket {$ticket->nomor_tiket} - SIGAP")

@section('content')
<section id="sigap-guest-track-result" class="sigap-track-result">
    <header class="sigap-track-result__header">
        <h1 class="sigap-track-result__title">{{ $ticket->judul }}</h1>
        <span class="sigap-badge sigap-badge--{{ $ticket->status }}">{{ ucfirst(str_replace('_',' ', $ticket->status)) }}</span>
        <span class="sigap-track-result__nomor">{{ $ticket->nomor_tiket }}</span>
    </header>

    <div class="sigap-track-result__detail" id="sigap-guest-track-result__detail">
        <p><strong>Deskripsi:</strong> {{ $ticket->deskripsi }}</p>
        <p><strong>Prioritas:</strong> {{ ucfirst($ticket->prioritas) }}</p>
    </div>

    <div class="sigap-track-result__timeline" id="sigap-guest-track-result__timeline">
        <h2>Riwayat Penanganan</h2>
        <ul class="sigap-timeline">
            @foreach($ticket->auditLogs as $log)
                <li class="sigap-timeline__item">
                    <span class="sigap-timeline__time">{{ $log->created_at->format('d M Y, H:i') }}</span>
                    <span class="sigap-timeline__desc">{{ $log->deskripsi }}</span>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="sigap-track-result__comments" id="sigap-guest-track-result__comments">
        <h2>Balasan Teknisi</h2>
        @forelse($ticket->comments as $comment)
            <div class="sigap-comment">
                <p class="sigap-comment__meta">{{ $comment->user->name ?? 'Tim SIGAP' }} - {{ $comment->created_at->diffForHumans() }}</p>
                <p class="sigap-comment__body">{{ $comment->pesan }}</p>
            </div>
        @empty
            <p>Belum ada balasan dari teknisi.</p>
        @endforelse
    </div>
</section>
@endsection
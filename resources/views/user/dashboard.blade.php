@extends('layouts.app')
@section('title', 'Dashboard Saya')

@section('content')
<section id="sigap-user-dashboard" class="sigap-page">
    <h1 class="sigap-page__title">Dashboard Saya</h1>

    <div class="sigap-stats-grid" id="sigap-user-dashboard__stats">
        <div class="sigap-stat-card">
            <span class="sigap-stat-card__label">Total Tiket</span>
            <span class="sigap-stat-card__value">{{ $stats['total'] }}</span>
        </div>
        <div class="sigap-stat-card">
            <span class="sigap-stat-card__label">Sedang Diproses</span>
            <span class="sigap-stat-card__value">{{ $stats['in_progress'] }}</span>
        </div>
        <div class="sigap-stat-card">
            <span class="sigap-stat-card__label">Selesai</span>
            <span class="sigap-stat-card__value">{{ $stats['selesai'] }}</span>
        </div>
    </div>

    <div class="sigap-table-wrapper" id="sigap-user-dashboard__table">
        <table class="sigap-table">
            <thead>
                <tr><th>Nomor Tiket</th><th>Judul</th><th>Prioritas</th><th>Status</th><th></th></tr>
            </thead>
            <tbody>
                @foreach($tickets as $ticket)
                    <tr>
                        <td>{{ $ticket->nomor_tiket }}</td>
                        <td>{{ $ticket->judul }}</td>
                        <td><span class="sigap-badge sigap-badge--priority-{{ $ticket->prioritas }}">{{ ucfirst($ticket->prioritas) }}</span></td>
                        <td><span class="sigap-badge sigap-badge--{{ $ticket->status }}">{{ ucfirst($ticket->status) }}</span></td>
                        <td><a href="{{ route('tickets.show', $ticket) }}">Detail</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
@endsection
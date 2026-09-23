@extends('layouts.app')
@section('title', "Tiket {$ticket->nomor_tiket} - SIGAP")

@section('content')
<section id="sigap-tickets-show" class="sigap-page">
    {{-- Header --}}
    <div class="sigap-page__header">
        <div class="flex items-center gap-3 flex-wrap">
            <a href="{{ url()->previous() }}" class="sigap-icon-btn" title="Kembali">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"/>
                    <polyline points="12 19 5 12 12 5"/>
                </svg>
            </a>
            <div>
                <h1 class="sigap-page__title">{{ $ticket->judul }}</h1>
                <p class="sigap-page__subtitle font-mono">{{ $ticket->nomor_tiket }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <span class="sigap-badge sigap-badge--priority-{{ $ticket->prioritas }}">Prioritas {{ ucfirst($ticket->prioritas) }}</span>
            <span class="sigap-badge sigap-badge--{{ $ticket->status }}">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span>
        </div>
    </div>

    <div class="sigap-detail-layout" id="sigap-tickets-show__layout">

        {{-- Kolom utama: deskripsi + percakapan --}}
        <div class="sigap-detail-main">
            {{-- Deskripsi --}}
            <div class="sigap-card">
                <div class="sigap-card__header">
                    <h2 class="text-lg font-semibold text-slate-900">Deskripsi Masalah</h2>
                    @if($ticket->file_evidence_pelapor)
                        <a href="{{ route('tickets.evidence', ['ticket' => $ticket, 'jenis' => 'pelapor']) }}" target="_blank"
                           rel="noopener" class="sigap-btn sigap-btn--sm sigap-btn--secondary">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="7 10 12 15 17 10"/>
                                <line x1="12" y1="15" x2="12" y2="3"/>
                            </svg>
                            Lihat Lampiran Pelapor
                        </a>
                    @endif
                </div>
                <p class="text-sm text-slate-600 leading-relaxed whitespace-pre-line">{{ $ticket->deskripsi }}</p>
            </div>

            {{-- Komentar --}}
            <div class="sigap-card">
                <div class="sigap-card__header">
                    <h2 class="text-lg font-semibold text-slate-900">Komunikasi</h2>
                    <span class="text-xs font-medium text-slate-400">{{ $ticket->comments->count() }} pesan</span>
                </div>
                @forelse($ticket->comments as $comment)
                    <div class="sigap-comment">
                        <div class="sigap-comment__header">
                            <span class="sigap-comment__avatar">
                            @if ($comment->user?->avatar_url)
                                <img src="{{ $comment->user->avatar_url }}" alt="" class="sigap-avatar__img" loading="lazy" decoding="async">
                            @else
                                {{ strtoupper(substr($comment->user->name ?? 'S', 0, 1)) }}
                            @endif
                        </span>
                            <div>
                                <span class="sigap-comment__author">{{ $comment->user->name ?? 'Tim SIGAP' }}</span>
                                <p class="sigap-comment__meta">{{ $comment->created_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                        <p class="sigap-comment__body">{{ $comment->pesan }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">Belum ada komentar pada tiket ini.</p>
                @endforelse

                <form method="POST" action="{{ route('tickets.comments.store', $ticket) }}" class="sigap-form max-w-none mt-6">
                    @csrf
                    <div class="sigap-form__group">
                        <label for="pesan" class="sigap-form__label">Tulis tanggapan anda</label>
                        <textarea name="pesan" id="pesan" rows="3" maxlength="2000" required placeholder="Isi tanggapan atau keterangan tambahan..." class="sigap-form__textarea"></textarea>
                    </div>
                    @error('pesan')
                        <span class="sigap-form__error">{{ $message }}</span>
                    @enderror
                    <button type="submit" class="sigap-form__submit sm:!w-auto sm:px-8">
                        Kirim
                    </button>
                </form>
            </div>
        </div>

        {{-- Sidebar meta --}}
        <aside class="sigap-detail-side">

            {{-- Status & SLA --}}
            <div class="sigap-card">
                <h2 class="text-sm font-semibold text-slate-900 mb-4">Status Penanganan</h2>
                <div class="sigap-detail-grid--stack">
                    <div class="sigap-detail-field">
                        <span class="sigap-detail-field__label">Departemen</span>
                        <span class="sigap-detail-field__value">{{ $ticket->departemen->nama ?? '-' }}</span>
                    </div>
                    <div class="sigap-detail-field">
                        <span class="sigap-detail-field__label">Kategori</span>
                        <span class="sigap-detail-field__value">{{ $ticket->kategori->nama ?? '-' }}</span>
                    </div>
                    <div class="sigap-detail-field">
                        <span class="sigap-detail-field__label">Prioritas</span>
                        <span class="sigap-detail-field__value capitalize">{{ $ticket->prioritas }}</span>
                    </div>
                    <div class="sigap-detail-field">
                        <span class="sigap-detail-field__label">Dibuat</span>
                        <span class="sigap-detail-field__value">{{ $ticket->created_at->format('d M Y, H:i') }}</span>
                    </div>
                    <div class="sigap-detail-field">
                        <span class="sigap-detail-field__label">Pelapor</span>
                        <span class="sigap-detail-field__value">
                            {{ $ticket->pelapor?->name ?? $ticket->nama_guest ?? '-' }}
                            @if($ticket->isGuestTicket()) <span class="sigap-badge sigap-badge--role-user !py-0.5 !px-2">Publik</span> @endif
                        </span>
                    </div>
                    <div class="sigap-detail-field">
                        <span class="sigap-detail-field__label">Kontak Pelapor</span>
                        <span class="sigap-detail-field__value">{{ $ticket->kontak_guest ?? ($ticket->pelapor?->email ?? '-') }}</span>
                    </div>
                    <div class="sigap-detail-field">
                        <span class="sigap-detail-field__label">Petugas / Agent</span>
                        <span class="sigap-detail-field__value">{{ $ticket->agent?->name ?? 'Menunggu pengambilan' }}</span>
                    </div>
                </div>

                @if($ticket->sla_target_at && ! in_array($ticket->status, ['resolved', 'closed']))
                    @php
                        $totalJam  = max(1, round($ticket->sla_target_at->diffInHours($ticket->created_at, false)));
                        $terpakai  = max(0, round($ticket->created_at->diffInHours(now(), false)));
                        $sisaJam   = $ticket->sla_target_at->diffInHours(now(), false);
                        $pct       = min(100, round(($terpakai / $totalJam) * 100));
                    @endphp
                    <div class="sigap-progress mt-5">
                        <div class="sigap-progress__labels">
                            <span class="font-medium {{ $ticket->isSlaBreached() ? 'text-red-600' : ($sisaJam <= 24 ? 'text-amber-600' : 'text-emerald-600') }}">
                                @if($ticket->isSlaBreached())
                                    SLA terlampaui
                                @else
                                    Sisa {{ $ticket->sla_target_at->diffForHumans(null, true) }}
                                @endif
                            </span>
                            <span>Target {{ $ticket->sla_target_at->format('d M, H:i') }}</span>
                        </div>
                        <div class="sigap-progress__track">
                            <div class="sigap-progress__fill {{ $ticket->isSlaBreached() ? 'sigap-progress__fill--red' : ($sisaJam <= 24 ? 'sigap-progress__fill--amber' : '') }}" style="width: {{ min(100, max(8, $pct)) }}%"></div>
                        </div>
                    </div>
                @elseif($ticket->sla_target_at)
                    <div class="sigap-progress mt-5">
                        <div class="sigap-progress__labels">
                            <span class="font-medium text-emerald-600">Selesai sesuai target</span>
                            <span>Target {{ $ticket->sla_target_at->format('d M, H:i') }}</span>
                        </div>
                        <div class="sigap-progress__track">
                            <div class="sigap-progress__fill" style="width: 100%"></div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Panel penyelesaian agent --}}
            @role('agent')
                @if($ticket->assigned_agent_id === auth()->id() && ! in_array($ticket->status, ['resolved', 'closed']))
                    <div class="sigap-card border-sigap-100 bg-sigap-50/40">
                        <h2 class="text-sm font-semibold text-slate-900 mb-4">Selesaikan Tiket</h2>
                        @if ($errors->any())
                            <div class="sigap-alert sigap-alert--error w-full">
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif
                        <form method="POST" action="{{ route('agent.tickets.resolve', $ticket) }}" enctype="multipart/form-data" class="sigap-form max-w-none">
                            @csrf
                            @method('PATCH')
                            <div class="sigap-form__group">
                                <label for="catatan_penyelesaian" class="sigap-form__label">Catatan Penyelesaian</label>
                                <textarea name="catatan_penyelesaian" id="catatan_penyelesaian" class="sigap-form__textarea" rows="4" required></textarea>
                            </div>
                            <div class="sigap-form__group">
                                <label for="evidence_penyelesaian" class="sigap-form__label">Bukti Penanganan {{ $ticket->file_evidence_penyelesaian ? '(Opsional)' : '(Wajib, maks 2MB, JPG/PNG/PDF)' }}</label>
                                <input type="file" name="evidence_penyelesaian" id="evidence_penyelesaian" class="sigap-form__file">
                            </div>
                            <button type="submit" class="sigap-form__submit sm:!w-auto sm:px-8">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                    <polyline points="22 4 12 14.01 9 11.01"/>
                                </svg>
                                Tandai Selesai
                            </button>
                        </form>
                    </div>
                @endif
            @endrole

            {{-- Informasi penyelesaian --}}
            @if($ticket->status === 'resolved')
                <div class="sigap-card border-emerald-200 bg-emerald-50/40">
                    <h2 class="text-sm font-semibold text-emerald-800 mb-3">Penyelesaian Tiket</h2>
                    @if($ticket->catatan_penyelesaian)
                        <p class="text-sm text-slate-600 leading-relaxed whitespace-pre-line">{{ $ticket->catatan_penyelesaian }}</p>
                    @endif
                    @if($ticket->file_evidence_penyelesaian)
                        <p class="text-xs text-slate-400 mt-3">Bukti penyelesaian telah dilampirkan oleh agent.</p>
                        <a href="{{ route('tickets.evidence', ['ticket' => $ticket, 'jenis' => 'penyelesaian']) }}" target="_blank"
                           rel="noopener" class="sigap-btn sigap-btn--sm sigap-btn--secondary mt-2">
                            Lihat Bukti Penanganan
                        </a>
                    @endif
                </div>
            @endif

            {{-- Riwayat penanganan --}}
            <div class="sigap-card">
                <div class="sigap-card__header">
                    <h2 class="text-sm font-semibold text-slate-900">Riwayat Penanganan</h2>
                </div>
                @if($ticket->auditLogs->count())
                    <ul class="sigap-timeline">
                        @foreach($ticket->auditLogs as $log)
                            @php
                                $statusBaru    = $log->data_after['status'] ?? null;
                                $konteksStatus = $log->aksi === 'status_changed' ? ($statusBaru ?? 'open') : $log->aksi;

                                $judulAksi = match ($log->aksi) {
                                    'ticket_created' => 'Tiket berhasil dibuat',
                                    'status_changed' => match ($statusBaru) {
                                        'open'        => 'Tiket dibuka kembali',
                                        'in_progress' => 'Tiket mulai ditangani teknisi',
                                        'resolved'    => 'Tiket selesai ditangani',
                                        'closed'      => 'Tiket resmi ditutup',
                                        'rejected'    => 'Pengajuan tiket ditolak',
                                        default       => $log->deskripsi,
                                    },
                                    default => $log->deskripsi,
                                };
                            @endphp
                            <li class="sigap-timeline__item">
                                <span class="sigap-timeline__dot sigap-timeline__dot--{{ $konteksStatus }}"></span>
                                <p class="sigap-timeline__desc">{{ $judulAksi }}</p>
                                <p class="sigap-timeline__meta">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"/>
                                        <polyline points="12 6 12 12 16 14"/>
                                    </svg>
                                    {{ $log->created_at->format('d M Y, H:i') }} WIB · {{ $log->aktor_label ?? $log->user?->name ?? 'Sistem' }}
                                </p>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-slate-400">Belum ada riwayat penanganan.</p>
                @endif
            </div>
        </aside>
    </div>
</section>
@endsection
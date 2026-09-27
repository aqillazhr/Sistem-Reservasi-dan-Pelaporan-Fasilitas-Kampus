@extends('layouts.dashboard')

@section('title', 'Detail Reservasi')

@section('content')
    @include('reservations._styles')
    <div class="rsv">
        <h1 style="font-size:32px">Detail reservasi</h1>
        <p class="sub"><a href="{{ route('petugas.reservations.index') }}">Kembali ke Kelola Reservasi</a></p>

        @error('reservation')
            <div class="alert" role="alert">{{ $message }}</div>
        @enderror
        @error('note')
            <div class="alert" role="alert">{{ $message }}</div>
        @enderror
        @error('cancellation_reason')
            <div class="alert" role="alert">{{ $message }}</div>
        @enderror

        <div class="card">
            <dl>
                <dt>Status</dt>
                <dd><span class="badge {{ $reservation->status_badge_class }}">{{ $reservation->status_label }}</span></dd>
                <dt>Pemohon</dt>
                <dd>{{ $reservation->user->name }} <span class="muted">({{ ucfirst($reservation->user->user_type ?? $reservation->user->role) }})</span></dd>
                <dt>Fasilitas</dt>
                <dd>{{ $reservation->facility->name }} @if($reservation->facility->location) <span class="muted">— {{ $reservation->facility->location->fakultas ?? $reservation->facility->location->scope_level }}</span> @endif</dd>
                <dt>Tanggal</dt>
                <dd>{{ $reservation->reservation_date->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</dd>
                <dt>Waktu</dt>
                <dd>{{ $reservation->start_short }}–{{ $reservation->end_short }} WIB</dd>
                <dt>Tujuan</dt>
                <dd>{{ $reservation->purpose }}</dd>
                @if ($reservation->status === 'rejected')
                    <dt>Alasan ditolak</dt>
                    <dd>{{ optional($reservation->statusLogs->where('new_status', 'rejected')->last())->note }}</dd>
                @endif
                @if ($reservation->status === 'cancelled')
                    <dt>Alasan dibatalkan</dt>
                    <dd>{{ $reservation->cancellation_reason ?? optional($reservation->statusLogs->where('new_status', 'cancelled')->last())->note }}</dd>
                @endif
            </dl>
        </div>

        @if ($reservation->status === 'pending')
            <div class="card" style="margin-top:16px">
                <div class="row" style="gap:10px">
                    <form method="POST" action="{{ route('petugas.reservations.approve', $reservation) }}"
                          class="form-confirm" data-confirm-title="Setujui Reservasi"
                          data-confirm-msg="Apakah Anda yakin ingin menyetujui reservasi {{ $reservation->facility->name }} ini?">
                        @csrf
                        <button type="submit" class="btn primary">Setuju</button>
                    </form>
                    <button type="button" class="btn ghost"
                            data-reason-url="{{ route('petugas.reservations.reject', $reservation) }}"
                            data-reason-field="note"
                            data-reason-title="Tolak reservasi {{ $reservation->facility->name }}?">Tolak</button>
                </div>
            </div>
        @elseif ($reservation->status === 'approved' && ! $reservation->isFinished())
            <div class="card" style="margin-top:16px">
                <button type="button" class="btn danger"
                        data-reason-url="{{ route('petugas.reservations.petugas-cancel', $reservation) }}"
                        data-reason-field="cancellation_reason"
                        data-reason-title="Batalkan reservasi {{ $reservation->facility->name }}?">Batalkan</button>
            </div>
        @endif

        <div class="card" style="margin-top:16px">
            <h2>Riwayat status</h2>
            <ol class="tl">
                @forelse ($reservation->statusLogs as $log)
                    <li>
                        {{ $log->created_at->locale('id')->isoFormat('D MMM YYYY, HH.mm') }} —
                        {{ \App\Models\Reservation::STATUS_LABELS[$log->new_status] ?? $log->new_status }}
                        @if ($log->changedBy)<span class="muted">oleh {{ $log->changedBy->name }}</span>@endif
                        @if ($log->note)<span class="muted">({{ $log->note }})</span>@endif
                    </li>
                @empty
                    <li class="muted">Belum ada riwayat.</li>
                @endforelse
            </ol>
        </div>
    </div>
@endsection

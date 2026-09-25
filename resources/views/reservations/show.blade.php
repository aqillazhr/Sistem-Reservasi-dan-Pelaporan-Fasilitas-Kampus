@extends('layouts.dashboard')

@section('title', 'Detail Reservasi')

@section('content')
    @include('reservations._styles')
    <div class="rsv">
        <h1>Detail reservasi</h1>
        <p class="sub"><a href="{{ route('pengguna.reservations.index') }}">Kembali ke reservasi saya</a></p>

        @error('reservation')
            <div class="alert" role="alert">{{ $message }}</div>
        @enderror

        <div class="card">
            <dl>
                <dt>Status</dt>
                <dd><span class="badge {{ $reservation->isFinished() ? 'selesai' : $reservation->status }}">{{ $reservation->status_label }}</span></dd>
                <dt>Fasilitas</dt>
                <dd>{{ $reservation->facility->name }}</dd>
                <dt>Tanggal</dt>
                <dd>{{ $reservation->reservation_date->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</dd>
                <dt>Waktu</dt>
                <dd>{{ $reservation->start_short }}–{{ $reservation->end_short }}</dd>
                <dt>Tujuan</dt>
                <dd>{{ $reservation->purpose }}</dd>
                @if ($rejectNote)
                    <dt>Alasan ditolak</dt>
                    <dd>{{ $rejectNote }}</dd>
                @endif
                @if ($reservation->cancellation_reason)
                    <dt>Alasan dibatalkan petugas</dt>
                    <dd>{{ $reservation->cancellation_reason }}</dd>
                @endif
            </dl>
        </div>

        @if (in_array($reservation->status, ['pending', 'approved'], true))
            @php $canCancel = $reservation->canBeCancelledByOwner(); @endphp
            <div class="card" style="margin-top:16px">
                <button type="button" class="btn danger" id="openCancel" @disabled(! $canCancel)>Batalkan reservasi</button>
                @unless ($canCancel)
                    <p class="muted">Pembatalan hanya bisa sampai {{ config('reservation.cancel_min_hours') }} jam sebelum jam mulai
                        ({{ $reservation->cancelDeadline()->locale('id')->isoFormat('D MMM YYYY, HH.mm') }}).</p>
                @endunless
            </div>

            <dialog id="cancelDialog">
                <h2>Batalkan reservasi ini?</h2>
                <p class="muted">Slot akan dilepas dan bisa dipesan orang lain.</p>
                <form method="POST" action="{{ route('pengguna.reservations.cancel', $reservation) }}" id="cancelForm">
                    @csrf
                    <div class="row">
                        <button type="button" class="btn ghost" id="closeCancel">Tidak jadi</button>
                        <button type="submit" class="btn danger">Ya, batalkan</button>
                    </div>
                </form>
            </dialog>
            <script>
                (function () {
                    var d = document.getElementById('cancelDialog'), open = document.getElementById('openCancel');
                    if (open && !open.disabled) open.addEventListener('click', function () { d.showModal(); });
                    document.getElementById('closeCancel').addEventListener('click', function () { d.close(); });
                    document.getElementById('cancelForm').addEventListener('submit', function (e) {
                        var b = e.target.querySelector('.danger'); b.disabled = true; // cegah klik ganda
                    });
                })();
            </script>
        @endif

        <div class="card" style="margin-top:16px">
            <h2>Riwayat status</h2>
            <ol class="tl">
                @foreach ($reservation->statusLogs as $log)
                    <li>
                        {{ $log->created_at->locale('id')->isoFormat('D MMM YYYY, HH.mm') }} —
                        {{ \App\Models\Reservation::STATUS_LABELS[$log->new_status] ?? $log->new_status }}
                        @if ($log->note)<span class="muted">({{ $log->note }})</span>@endif
                    </li>
                @endforeach
            </ol>
        </div>
    </div>
@endsection

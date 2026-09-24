<section class="rsv-summary" style="background:#fff;border:1px solid #d8bcfa;border-radius:10px;padding:18px;max-width:640px;margin-top:16px">
    <h2 style="margin:0 0 8px;font-size:18px">Reservasi saya</h2>
    <p style="margin:0 0 10px;color:#5b4a75">{{ $pending }} menunggu persetujuan.</p>
    @forelse ($upcoming as $r)
        <div style="margin-bottom:6px">
            <a href="{{ route('pengguna.reservations.show', $r) }}">{{ $r->facility->name }}</a> —
            {{ $r->reservation_date->locale('id')->isoFormat('ddd, D MMM') }}, {{ $r->start_short }}–{{ $r->end_short }}
        </div>
    @empty
        <p style="margin:0 0 10px;color:#5b4a75">Belum ada reservasi aktif yang akan datang.</p>
    @endforelse
    <a href="{{ route('pengguna.reservations.hub') }}">Buka reservasi</a>
</section>

<section style="
    width: 100%;
    max-width: 640px;
    background: #ffffff;
    border: 1px solid #bd93f8;
    border-radius: 10px;
    padding: 20px;
    margin-top: 20px;
">
    <h2 style="
        margin: 0 0 10px 0;
        color: #260f45;
        font-size: 20px;
        font-weight: 700;
    ">
        Reservasi saya
    </h2>

    @if ($pending > 0)
        <p style="margin: 0 0 6px 0; color: #4e405d;">
            {{ $pending }} menunggu persetujuan.
        </p>
    @endif

    @forelse ($upcoming as $r)
        <p style="margin: 0 0 6px 0; color: #4e405d;">
            <a href="{{ route('pengguna.reservations.show', $r) }}"
               style="color: #4e405d; text-decoration: none;">
                {{ $r->facility->name }} —
                {{ $r->reservation_date->locale('id')->isoFormat('ddd, D MMM') }},
                {{ $r->start_short }}–{{ $r->end_short }}
            </a>
        </p>
    @empty
        <p style="margin: 0 0 12px 0; color: #4e405d;">
            Belum ada reservasi aktif yang akan datang.
        </p>
    @endforelse

    <a
        href="{{ route('pengguna.reservations.hub') }}"
        style="
            color: #9747ff;
            font-weight: 600;
            text-decoration: none;
        "
    >
        Buka reservasi
    </a>
</section>

@props([
    'reports',
    'newReports',
    'processingReports',
    'totalReports',
])

<div style="
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
        Laporan saya
    </h2>

    @if ($totalReports === 0)

        <p style="
            margin: 0 0 12px 0;
            color: #4e405d;
        ">
            Belum ada laporan kerusakan yang dikirim.
        </p>

    @else

        <p style="
            margin: 0 0 6px 0;
            color: #4e405d;
        ">
            {{ $totalReports }}
            laporan sudah dikirim.
        </p>

        @if ($newReports > 0)
            <p style="
                margin: 0 0 6px 0;
                color: #155a8a;
            ">
                {{ $newReports }}
                laporan menunggu diproses petugas.
            </p>
        @endif

        @if ($processingReports > 0)
            <p style="
                margin: 0 0 10px 0;
                color: #805b00;
            ">
                {{ $processingReports }}
                laporan sedang diproses.
            </p>
        @endif

    @endif

    <a
        href="{{ route('pengguna.reports.index') }}"
        style="
            color: #9747ff;
            font-weight: 600;
            text-decoration: none;
        "
    >
        Lihat laporan saya
    </a>

</div>
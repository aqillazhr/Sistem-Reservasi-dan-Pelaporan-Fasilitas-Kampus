@extends('layouts.dashboard')

@section('title', 'Detail Laporan')

@section('content')

<style>
    .detail-page {
        max-width: 900px;
        margin: 0 auto;
    }

    .back-link {
        color: #260f45;
        font-size: 13px;
        font-weight: 600;
    }

    .detail-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 18px 0;
    }

    .detail-title {
        font-size: 28px;
        font-weight: 700;
        color: #260f45;
    }

    .status {
        padding: 6px 13px;
        border-radius: 5px;
        font-size: 11px;
        font-weight: 700;
    }

    .status-baru {
        background: #d5ebff;
        color: #155a8a;
    }

    .status-diproses {
        background: #ffe9ad;
        color: #805b00;
    }

    .status-selesai {
        background: #c9f7d3;
        color: #176b2c;
    }

    .status-ditolak {
        background: #ffd2d2;
        color: #a12626;
    }

    .detail-card {
        background: #ffffff;
        border: 1px solid #bd93f8;
        border-radius: 7px;
        padding: 20px;
        margin-bottom: 15px;
    }

    .detail-card h2 {
        margin: 0 0 15px;
        font-size: 15px;
        color: #260f45;
    }

    .detail-row {
        margin-bottom: 10px;
        font-size: 12px;
    }

    .detail-label {
        font-weight: 700;
        color: #260f45;
    }

    .photo-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 12px;
    }

    .photo-grid img {
        width: 100%;
        height: 170px;
        object-fit: cover;
        border-radius: 5px;
        border: 1px solid #d5bbfb;
    }
</style>

<div class="detail-page">

    <a
        href="{{ route('pengguna.reports.index') }}"
        class="back-link"
    >
        ← Kembali ke Riwayat
    </a>


    <div class="detail-header">

        <h1 class="detail-title">
            Detail Laporan
        </h1>

        <span class="status status-{{ $report->status }}">
            {{ ucfirst($report->status) }}
        </span>

    </div>


    <div class="detail-card">

        <h2>
            Informasi Laporan
        </h2>

        <div class="detail-row">
            <span class="detail-label">
                Fasilitas:
            </span>

            {{ $report->facility->name }}
        </div>

        <div class="detail-row">
            <span class="detail-label">
                Lokasi:
            </span>

            {{ $report->facility->location->gedung ?? '-' }}
        </div>

        <div class="detail-row">
            <span class="detail-label">
                Kategori:
            </span>

            {{ $report->category }}
        </div>

        <div class="detail-row">
            <span class="detail-label">
                Deskripsi:
            </span>

            {{ $report->description ?: '-' }}
        </div>

        <div class="detail-row">
            <span class="detail-label">
                Dilaporkan:
            </span>

            {{ $report->created_at->format('d M Y H:i') }}
        </div>

    </div>


    <div class="detail-card">

        <h2>
            Dokumentasi
        </h2>

        <div class="photo-grid">

            @forelse ($report->photos as $photo)

                <img
                    src="{{ asset(
                        'storage/' . $photo->file_path
                    ) }}"
                    alt="Foto laporan"
                >

            @empty

                <p>
                    Tidak ada foto.
                </p>

            @endforelse

        </div>

    </div>


    @if (
        $report->status === 'selesai'
        || $report->status === 'ditolak'
    )

        <div class="detail-card">

            <h2>
                Catatan Resolusi
            </h2>

            <div class="detail-row">
                {{ $report->resolution_note ?: '-' }}
            </div>

            @if ($report->resolved_at)

                <div class="detail-row">

                    <span class="detail-label">
                        Ditutup:
                    </span>

                    {{ $report->resolved_at->format('d M Y H:i') }}

                </div>

            @endif

        </div>

    @endif

</div>

@endsection
@extends('layouts.dashboard')

@section('title', 'Laporan Saya')

@section('content')

<style>
    .history-page {
        width: 100%;
    }

    .history-title {
        font-family: 'Sora', Helvetica, sans-serif;
        font-weight: 700;
        font-size: 40px;
        color: #501e91;
        margin: 0 0 6px;
        letter-spacing: 0;
        line-height: normal;
    }

    .tabs {
        display: flex;
        margin-bottom: 18px;
    }

    .tab {
        padding: 7px 22px;
        border: 1px solid #bd93f8;
        color: #260f45;
        font-size: 12px;
    }

    .tab:first-child {
        border-radius: 20px 0 0 20px;
    }

    .tab:last-child {
        border-radius: 0 20px 20px 0;
    }

    .tab.active {
        background: #9747ff;
        color: #ffffff;
    }

    .table-wrap {
        width: 100%;
        overflow-x: auto;
        background: #ffffff;
        border: 1px solid #bd93f8;
        border-radius: 6px;
    }

    .report-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 900px;
    }

    .report-table th {
        background: #bd93f8;
        color: #260f45;
        padding: 10px 8px;
        font-size: 10px;
        text-align: left;
    }

    .report-table td {
        padding: 11px 8px;
        border-top: 1px solid #e2d3f8;
        font-size: 10px;
        vertical-align: top;
    }

    .status {
        display: inline-block;
        padding: 4px 9px;
        border-radius: 4px;
        font-size: 9px;
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

    .detail-link {
        color: #9747ff;
        font-weight: 700;
        text-decoration: underline;
    }

    .empty {
        padding: 40px;
        text-align: center;
        color: #76677f;
    }
</style>

<div class="history-page">

    <h1 class="history-title">
        Riwayat Saya
    </h1>

    <div class="tabs">

        <a
            href="{{ route('pengguna.reservations.index') }}"
            class="tab"
        >
            Reservasi
        </a>

        <span class="tab active">
            Laporan
        </span>

    </div>


    <div class="table-wrap">

        <table class="report-table">

            <thead>

                <tr>

                    <th>
                        Tgl. Pelaporan
                    </th>

                    <th>
                        Fasilitas
                    </th>

                    <th>
                        Tipe
                    </th>

                    <th>
                        Kategori Kerusakan
                    </th>

                    <th>
                        Deskripsi
                    </th>

                    <th>
                        Dokumentasi
                    </th>

                    <th>
                        Status
                    </th>

                    <th>
                        Aksi
                    </th>

                </tr>

            </thead>

            <tbody>

                @forelse ($reports as $report)

                    <tr>

                        <td>
                            {{ $report->created_at->format('d M Y') }}
                        </td>

                        <td>
                            {{ $report->facility->name }}

                            <br>

                            {{ $report->facility->location->gedung ?? '-' }}
                        </td>

                        <td>
                            {{ $report->facility->type->name ?? '-' }}
                        </td>

                        <td>
                            {{ $report->category }}
                        </td>

                        <td>
                            {{ $report->description ?: '-' }}
                        </td>

                        <td>

                            {{ $report->photos->count() }}
                            foto

                        </td>

                        <td>

                            <span class="status status-{{ $report->status }}">
                                {{ ucfirst($report->status) }}
                            </span>

                        </td>

                        <td>

                            <a
                                href="{{ route(
                                    'pengguna.reports.show',
                                    $report
                                ) }}"
                                class="detail-link"
                            >
                                Detail
                            </a>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="8"
                            class="empty"
                        >
                            Belum ada laporan yang dikirim.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection
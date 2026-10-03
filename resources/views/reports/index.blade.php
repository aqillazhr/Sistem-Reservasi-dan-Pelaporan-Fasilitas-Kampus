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
        background: #fff;
        border: 1px solid #9747ff;
        border-radius: 10px;
        overflow: hidden;
    }

    .report-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .report-table th {
        background: #BD93F8;
        color: #000;
        padding: 14px 12px;
        font-family: 'Sora', Helvetica, sans-serif;
        font-size: 15px;
        font-weight: 700;
        line-height: 1.3;
        text-align: left;
        vertical-align: middle;
        white-space: normal;
        overflow-wrap: anywhere;
        border-right: 1px solid #9747ff;
    }

    .report-table td {
        padding: 14px 12px;
        border-top: 1px solid #D9C3F4;
        border-right: 1px solid #D9C3F4;
        font-family: 'Sora', Helvetica, sans-serif;
        font-size: 15px;
        font-weight: 400;
        line-height: 1.45;
        color: #222;
        vertical-align: top;
        overflow-wrap: anywhere;
        word-break: normal;
    }

    .report-table td:last-child {
        white-space: normal;
        overflow: hidden;
    }

    .report-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin: 0 0 16px;
    }

    .report-filter {
        border: none;
        border-radius: 18px;
        padding: 8px 17px;
        background: #e4d0ff;
        color: #ffffff;
        font-family: 'Sora', Helvetica, sans-serif;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
    }

    .report-filter.active {
        background: #501e91;
    }

    .report-filter:hover {
        background: #9747ff;
    }

    .facility-name {
        font-family: 'Sora', Helvetica, sans-serif;
        font-size: 14px;
        font-weight: 700;
        line-height: 1.35;
        color: #222222;
    }

    .status {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 8px;
        font-family: 'Sora', Helvetica, sans-serif;
        font-size: 13px;
        font-weight: 700;
        white-space: nowrap;
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
        font-family: 'Sora', Helvetica, sans-serif;
        font-size: 13px;
        font-weight: 600;
        color: #9747FF;
        text-decoration: underline;
    }

    .empty {
        padding: 40px;
        text-align: center;
        color: #76677f;
    }

    @media (max-width: 1000px) {
        .report-table th,
        .report-table td {
            padding: 11px 9px;
        }

        .report-table th {
            font-size: 15px;
        }

        .report-table td {
            font-size: 15px;
        }

        .status {
            font-size: 12px;
            padding: 5px 8px;
        }
    }
</style>

<div class="history-page">

    <h1 class="history-title">
        Riwayat Saya
    </h1>

    <div class="tabs">

        <a
            href="{{ route('pengguna.reservations.index') }}"
            class="tab">
            Reservasi
        </a>

        <span class="tab active">
            Laporan
        </span>

    </div>

    <div class="report-filters">
        <button
            type="button"
            class="report-filter active"
            data-status="all"
        >
            Semua
        </button>

        <button
            type="button"
            class="report-filter"
            data-status="baru"
        >
            Baru
        </button>

        <button
            type="button"
            class="report-filter"
            data-status="diproses"
        >
            Diproses
        </button>

        <button
            type="button"
            class="report-filter"
            data-status="ditolak"
        >
            Ditolak
        </button>

        <button
            type="button"
            class="report-filter"
            data-status="selesai"
        >
            Selesai
        </button>

    </div>


    <div class="table-wrap">

        <table class="report-table">

            <thead>

                <tr>
                    <th style="width: 13%;">Tgl. Pelaporan</th>
                    <th style="width: 20%;">Fasilitas</th>
                    <th style="width: 10%;">Tipe</th>
                    <th style="width: 14%;">Kategori Kerusakan</th>
                    <th style="width: 18%;">Deskripsi</th>
                    <th style="width: 12%;">Dokumentasi</th>
                    <th style="width: 13%;">Status</th>
                </tr>

            </thead>

            <tbody>

                @forelse ($reports as $report)
                    <tr
                        class="report-row"
                        data-status="{{ $report->status }}"
                    >

                        <td>
                            {{ $report->created_at->locale('id')->isoFormat('D MMMM YYYY') }}
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

<script>
    const reportFilters = document.querySelectorAll('.report-filter');
    const reportRows = document.querySelectorAll('.report-row');

    reportFilters.forEach(function (button) {
        button.addEventListener('click', function () {

            reportFilters.forEach(function (item) {
                item.classList.remove('active');
            });

            this.classList.add('active');

            const selectedStatus = this.dataset.status;

            reportRows.forEach(function (row) {
                const rowStatus = row.dataset.status;

                if (
                    selectedStatus === 'all' ||
                    rowStatus === selectedStatus
                ) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
</script>

@endsection
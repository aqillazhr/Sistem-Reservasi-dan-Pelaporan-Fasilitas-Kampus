@extends('layouts.dashboard')

@section('title', 'Laporan Masuk')

@section('content')

<style>
    .page-title {
        font-size: 40px;
        font-weight: 700;
        color: #501e91;
        margin-bottom: 8px;
    }

    .page-subtitle {
        color: #6f6280;
        margin-bottom: 24px;
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
        color: #000000;
        padding: 16px;
        font-family: 'Sora', Helvetica, sans-serif;
        font-size: 16px;
        font-weight: 700;
        line-height: 1.3;
        text-align: left;
        vertical-align: middle;
        white-space: normal;
        border-right: 1px solid #9747ff;
    }

    .report-table td {
        padding: 16px;
        border-top: 1px solid #D9C3F4;
        border-right: 1px solid #D9C3F4;
        font-family: 'Sora', Helvetica, sans-serif;
        font-size: 15px;
        font-weight: 400;
        line-height: 1.45;
        color: #222222;
        vertical-align: top;
        overflow-wrap: anywhere;
    }

    .report-table td:last-child {
        padding: 12px;
        overflow: hidden;
    }

    .report-table td:last-child .btn-action {
        max-width: 100%;
    }

    @media (max-width: 1000px) {
        .report-table th,
        .report-table td {
            padding: 12px 10px;
        }

        .report-table th {
            font-size: 15px;
        }

        .report-table td {
            font-size: 15px;
        }

        .btn-action {
            min-width: 68px;
            padding: 7px 9px;
        }
    }
    .status {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 6px;
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

    .action-cell {
        vertical-align: middle !important;
        white-space: normal;
    }

    .btn-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 72px;
        min-height: 36px;
        padding: 7px 12px;
        box-sizing: border-box;
        border: 1px solid #9747ff;
        border-radius: 7px;
        background: #ffffff;
        color: #501e91;
        font-family: 'Sora', Helvetica, sans-serif;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
    }

    .btn-action:hover {
        background: #f3eaff;
    }

    .empty {
        text-align: center;
        padding: 40px;
        color: #76677f;
    }

    .report-search {
        position: relative;
        width: 460px;
        margin: 20px 0 35px;
    }

    .report-search-icon {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #777;
        pointer-events: none;
    }

    .report-search input {
        width: 100%;
        height: 50px;
        padding: 0 16px 0 48px;
        box-sizing: border-box;
        border: 1px solid #999;
        border-radius: 8px;
        background: #fff;
        color: #260f45;
        font-family: 'Sora', Helvetica, sans-serif;
        font-size: 15px;
        font-weight: 400;
    }

    .report-search input:focus {
        outline: none;
        border-color: #501e91;
    }

    .report-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin: -20px 0 16px;
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
</style>

<h1 class="page-title">
    Kelola Laporan
</h1>

<div class="report-search">

    <svg
        class="report-search-icon"
        width="20"
        height="20"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round">
        <circle cx="11" cy="11" r="7"></circle>
        <line x1="16.5" y1="16.5" x2="21" y2="21"></line>
    </svg>

    <input
        type="text"
        id="reportSearch"
        placeholder="Cari ...">

</div>

<div class="report-filters">
    <button type="button" class="report-filter active" data-status="all">
        Semua
    </button>

    <button type="button" class="report-filter" data-status="baru">
        Baru
    </button>

    <button type="button" class="report-filter" data-status="diproses">
        Diproses
    </button>

    <button type="button" class="report-filter" data-status="selesai">
        Selesai
    </button>

    <button type="button" class="report-filter" data-status="ditolak">
        Ditolak
    </button>
</div>

<div class="table-wrap">

    <table class="report-table" id="reportTable">

        <thead>
            <tr>
                <th style="width: 13%;">Tanggal</th>
                <th style="width: 14%;">Pelapor</th>
                <th style="width: 18%;">Fasilitas</th>
                <th style="width: 13%;">Kategori</th>
                <th style="width: 12%;">Dokumentasi</th>
                <th style="width: 11%;">Status</th>
                <th style="width: 10%;">Aksi</th>
            </tr>
        </thead>

        <tbody>

            @forelse ($reports as $report)
                <tr data-status="{{ $report->status }}">

                    <td>
                        {{ $report->created_at->locale('id')->isoFormat('D MMMM YYYY') }}
                    </td>

                    <td>
                        {{ $report->user->name }}
                    </td>

                    <td>
                        <strong>
                            {{ $report->facility->name }}
                        </strong>
                        <br>
                        {{ $report->facility->location->gedung ?? '-' }}
                    </td>

                    <td>
                        {{ $report->category }}
                    </td>

                    <td>
                        {{ $report->photos->count() }} foto
                    </td>

                    <td>
                        <span class="status status-{{ $report->status }}">
                            {{ ucfirst($report->status) }}
                        </span>
                    </td>

                    <td class="action-cell">
                        @if (in_array($report->status, ['baru', 'diproses']))
                            <a
                                href="{{ route('petugas.reports.show', $report) }}"
                                class="btn-action"
                            >
                                Kelola
                            </a>
                        @else
                            -
                        @endif
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="7" class="empty">
                        Belum ada laporan masuk.
                    </td>
                </tr>

            @endforelse

        </tbody>

    </table>

</div>

<script>
    const reportSearch = document.getElementById('reportSearch');
    const reportTable = document.getElementById('reportTable');
    const reportFilters = document.querySelectorAll('.report-filter');

    let selectedStatus = 'all';

    function filterReports() {
        const keyword = reportSearch.value.toLowerCase().trim();

        reportTable.querySelectorAll('tbody tr').forEach(function (row) {
            const rowText = row.textContent.toLowerCase();
            const rowStatus = row.dataset.status;

            const matchesSearch = rowText.includes(keyword);
            const matchesStatus =
                selectedStatus === 'all' ||
                rowStatus === selectedStatus;

            row.style.display =
                matchesSearch && matchesStatus
                    ? ''
                    : 'none';
        });
    }

    reportSearch.addEventListener('input', filterReports);

    reportFilters.forEach(function (button) {
        button.addEventListener('click', function () {
            reportFilters.forEach(function (item) {
                item.classList.remove('active');
            });

            this.classList.add('active');

            selectedStatus = this.dataset.status;

            filterReports();
        });
    });
</script>

@endsection
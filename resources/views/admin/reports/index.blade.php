@extends('layouts.dashboard')

@section('title', 'Rekap dan Ekspor')

@section('content')

<style>
    .admin-report-page {
        padding: 10px 0 40px;
    }

    .admin-report-title {
        margin: 0 0 28px;
        color: #501e91;
        font-family: 'Sora', Helvetica, sans-serif;
        font-size: 40px;
        font-weight: 700;
    }

    .filter-box {
        display: flex;
        align-items: end;
        gap: 14px;
        margin-bottom: 28px;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .filter-group label {
        color: #260f45;
        font-family: 'Sora', Helvetica, sans-serif;
        font-size: 14px;
        font-weight: 600;
    }

    .filter-group select,
    .filter-group input {
        width: 220px;
        height: 42px;
        box-sizing: border-box;
        padding: 0 12px;
        border: 1px solid #bd93f8;
        border-radius: 8px;
        background: #fff;
        color: #260f45;
        font-family: 'Sora', Helvetica, sans-serif;
        font-size: 14px;
    }

    .filter-button {
        height: 42px;
        padding: 0 18px;
        border: none;
        border-radius: 8px;
        background: #9747ff;
        color: #fff;
        font-family: 'Sora', Helvetica, sans-serif;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
    }

    .report-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
    }

    .report-card {
        background: #fff;
        border: 1px solid #bd93f8;
        border-radius: 10px;
        padding: 18px;
    }

    .report-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 18px;
    }

    .report-card h2 {
        margin: 0;
        color: #260f45;
        font-family: 'Sora', Helvetica, sans-serif;
        font-size: 17px;
        font-weight: 700;
    }

    .month-label {
        color: #777;
        font-family: 'Sora', Helvetica, sans-serif;
        font-size: 14px;
    }

    .chart-row {
        margin-bottom: 15px;
    }

    .chart-row:last-child {
        margin-bottom: 0;
    }

    .chart-label {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 5px;
        color: #260f45;
        font-family: 'Sora', Helvetica, sans-serif;
        font-size: 13px;
    }

    .chart-label span:first-child {
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .chart-bar {
        width: 100%;
        height: 12px;
        background: #f0e6fb;
        border-radius: 10px;
        overflow: hidden;
    }

    .chart-fill {
        height: 100%;
        background: #9747ff;
        border-radius: 10px;
    }

    .damage-fill {
        background: #ff7777;
    }

    .note {
        margin: 14px 0 0;
        color: #888;
        font-family: 'Sora', Helvetica, sans-serif;
        font-size: 12px;
        line-height: 1.5;
    }

    .export-section {
        margin-top: 26px;
    }

    .export-section h2 {
        margin: 0 0 12px;
        color: #260f45;
        font-family: 'Sora', Helvetica, sans-serif;
        font-size: 17px;
        font-weight: 700;
    }

    .export-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }

    .export-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 110px;
        height: 42px;
        padding: 0 18px;
        border-radius: 7px;
        background: #bd93f8;
        color: #260f45;
        font-family: 'Sora', Helvetica, sans-serif;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
    }

    .export-button:hover {
        background: #9747ff;
        color: #fff;
    }

    .note {
        white-space: pre-line;
    }

    @media (max-width: 850px) {

        .report-grid {
            grid-template-columns: 1fr;
        }

        .filter-box {
            align-items: stretch;
            flex-direction: column;
        }

        .filter-group select,
        .filter-group input {
            width: 100%;
        }
    }
</style>


<div class="admin-report-page">

    <h1 class="admin-report-title">
        Rekap dan Ekspor
    </h1>


    <form
        method="GET"
        action="{{ route('admin.reports.index') }}"
        class="filter-box"
    >

        <div class="filter-group">

            <label for="month">
                Bulan
            </label>

            <input
                type="month"
                name="month"
                id="month"
                value="{{ $month }}"
            >

        </div>


        <div class="filter-group">

            <label for="location_id">
                Lokasi
            </label>

            <select
                name="location_id"
                id="location_id"
            >

                <option value="">
                    Semua lokasi
                </option>

                @foreach ($locations as $location)

                    <option
                        value="{{ $location->id }}"
                        @selected(
                            (string) $locationId ===
                            (string) $location->id
                        )
                    >
                        {{ $location->ruangan
                            ?: ($location->gedung
                                ?: ($location->fakultas
                                    ?: 'Universitas')) }}
                    </option>

                @endforeach

            </select>

        </div>


        <button
            type="submit"
            class="filter-button"
        >
            Terapkan Filter
        </button>

    </form>


    <div class="report-grid">


        {{-- OKUPANSI --}}

        <div class="report-card">

            <div class="report-card-header">

                <h2>
                    Okupansi tiap fasilitas
                </h2>

                <span class="month-label">
                    {{ $data['start']->locale('id')->isoFormat('MMM YYYY') }}
                </span>

            </div>


            @forelse ($data['rows'] as $row)

                <div class="chart-row">

                    <div class="chart-label">

                        <span>
                            {{ $row['facility'] }}
                        </span>

                        <strong>
                            {{ $row['occupancy'] }}%
                        </strong>

                    </div>


                    <div class="chart-bar">

                        <div
                            class="chart-fill"
                            style="
                                width:
                                {{ min($row['occupancy'], 100) }}%;
                            "
                        ></div>

                    </div>

                </div>

            @empty

                <p>
                    Tidak ada data fasilitas.
                </p>

            @endforelse

        </div>


        {{-- KERUSAKAN --}}

        <div class="report-card">

            <div class="report-card-header">

                <h2>
                    Kerusakan tiap fasilitas
                </h2>

                <span class="month-label">
                    {{ $data['start']->locale('id')->isoFormat('MMM YYYY') }}
                </span>

            </div>


            @forelse ($data['rows'] as $row)

                <div class="chart-row">

                    <div class="chart-label">

                        <span>
                            {{ $row['facility'] }}
                        </span>

                        <strong>
                            {{ $row['damage_percentage'] }}%
                        </strong>

                    </div>


                    <div class="chart-bar">

                        <div
                            class="chart-fill damage-fill"
                            style="
                                width:
                                {{ min($row['damage_percentage'], 100) }}%;
                            "
                        ></div>

                    </div>

                </div>

            @empty

                <p>
                    Tidak ada data kerusakan.
                </p>

            @endforelse

        </div>

    </div>

    <p class="note">
        Catatan: 
        Okupansi dihitung berdasarkan durasi reservasi yang disetujui dibandingkan waktu operasional fasilitas pada bulan yang dipilih.
        Frekuensi kerusakan dihitung berdasarkan jumlah laporan per fasilitas dibandingkan dengan fasilitas yang memiliki jumlah laporan terbanyak.
    </p>



    <div class="export-section">

        <h2>
            Data lebih lengkap:
        </h2>


        <div class="export-buttons">

            <a
                href="{{ route('admin.reports.export.csv', [
                    'month' => $month,
                    'location_id' => $locationId,
                ]) }}"
                class="export-button"
            >
                Ekspor CSV
            </a>


            <a
                href="{{ route('admin.reports.export.excel', [
                    'month' => $month,
                    'location_id' => $locationId,
                ]) }}"
                class="export-button"
            >
                Ekspor Excel
            </a>


            <a
                href="{{ route('admin.reports.export.pdf', [
                    'month' => $month,
                    'location_id' => $locationId,
                ]) }}"
                class="export-button"
            >
                Ekspor PDF
            </a>

        </div>

    </div>

</div>

@endsection
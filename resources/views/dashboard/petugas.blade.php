@extends('layouts.dashboard')

@section('title', 'Dashboard Petugas')

@section('content')

<style>

    /* =========================================================
       DASHBOARD PETUGAS
    ========================================================= */
    .petugas-dashboard {
        width: 100%;
    }

    /* =========================================================
       JUDUL
    ========================================================= */
    .petugas-title {
        margin: 0 0 30px;
        font-family: 'Sora', Helvetica, sans-serif;
        font-size: 46px;
        font-weight: 700;
        line-height: 1.2;
        color: #501e91;
    }


    /* =========================================================
       BARIS KARTU
    ========================================================= */
    .petugas-summary {
        display: flex;
        gap: 24px;
        width: 100%;
        margin-bottom: 30px;
    }

    .petugas-summary-card {
        flex: 1;
        min-width: 0;
        height: 120px;
        background: rgba(255, 255, 255, 0.7);
        border: 1px solid rgba(151, 71, 255, 0.7);
        border-radius: 8px;
        font-family: 'Sora', Helvetica, sans-serif;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        color: inherit;
    }

    .petugas-report-summary-card {
        height: 145px;
    }

    .petugas-summary-title {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-bottom: 8px;
        font-family: 'Sora', Helvetica, sans-serif;
        color: #525151;
        font-size: 16px;
        line-height: 1.2;
        font-weight: 700;
    }

    .petugas-summary-number {
        font-family: 'Sora', Helvetica, sans-serif;
        color: #000000;
        font-size: 42px;
        line-height: 1;
        font-weight: 700;
    }


    /* =========================================================
       DUA ANTRIAN
    ========================================================= */

    .petugas-queues {
        display: flex;
        gap: 24px;
        width: 100%;
        align-items: flex-start;
    }

    .petugas-queue-card {
        flex: 1;
        min-width: 0;
        background: rgba(255, 255, 255, 0.7);
        border: 1px solid rgba(151, 71, 255, 0.7);
        border-radius: 10px;
        padding: 18px 20px;
        font-family: 'Sora', Helvetica, sans-serif;
    }

    .petugas-queue-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        margin-bottom: 14px;
        padding-bottom: 8px;
        border-bottom: 1px solid #d9c3f4;
    }

    .petugas-queue-title {
        margin: 0;
        font-size: 23px;
        font-weight: 700;
        color: #000000;
    }

    .petugas-queue-bottom-link:hover {
        text-decoration: underline;
    }


    .petugas-queue-link:hover {
        text-decoration: underline;
    }


    /* =========================================================
       LAPORAN ITEM
    ========================================================= */

    .petugas-report-item {
        position: relative;

        padding: 14px 75px 13px 0;

        border-bottom: 1px solid #d9c3f4;
    }


    .petugas-report-item:last-child {
        border-bottom: none;
    }


    .petugas-report-facility {
        margin-bottom: 4px;
        font-family: 'Sora', Helvetica, sans-serif;
        font-size: 16px;
        font-weight: 700;
        line-height: 1.25;
        color: #000000;
    }


    .petugas-report-info {
        font-family: 'Sora', Helvetica, sans-serif;
        font-size: 12px;
        font-weight: 400;
        line-height: 1.4;
        color: #333333;
    }


    /* =========================================================
       STATUS LAPORAN
    ========================================================= */
    .petugas-report-status {
        position: absolute;
        top: 15px;
        right: 0;
        padding: 4px 9px;
        border-radius: 5px;
        font-family: 'Sora', Helvetica, sans-serif;
        font-size: 10px;
        font-weight: 600;
        line-height: 1.2;
        white-space: nowrap;
    }

    .petugas-report-status.baru {
        background: #bff5ff;
        color: #155a6b;
    }

    .petugas-report-status.diproses {
        background: #ffd994;
        color: #805b00;
    }

    /* =========================================================
       KOSONG
    ========================================================= */
    .petugas-empty {
        margin: 0;
        padding: 15px 0;
        color: rgba(0, 0, 0, .5);
        font-size: 13px;
    }

    /* =========================================================
       RESPONSIVE
    ========================================================= */
    @media (max-width: 900px) {
        .petugas-summary {
            flex-direction: column;
        }

        .petugas-queues {
            flex-direction: column;
        }

        .petugas-summary-card,.petugas-queue-card {
            width: 100%;
        }
    }

    
</style>



<div class="petugas-dashboard">


    {{-- =====================================================
         JUDUL
    ====================================================== --}}

    <h1 class="petugas-title">
        Dashboard Petugas
    </h1>


    {{-- =====================================================
         KARTU RINGKASAN
    ====================================================== --}}

    <div class="petugas-summary">


        {{-- ================================================
             RESERVASI MENUNGGU
             Komponen Reservasi yang SUDAH ADA di project
        ================================================= --}}

        <x-dashboard.petugas-reservation-queue-card />


        {{-- ================================================
             LAPORAN BARU
        ================================================= --}}
        <div class="petugas-summary-card petugas-report-summary-card">

            <div class="petugas-summary-title">

                {{-- Icon dokumen --}}
                <svg
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="#525151"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="8" y1="13" x2="16" y2="13"/>
                    <line x1="8" y1="17" x2="16" y2="17"/>
                </svg>

                <span>
                    Laporan Baru
                </span>

            </div>

            <div class="petugas-summary-number">
                {{ $newReports }}
            </div>

        </div>


        {{-- ================================================
             DALAM PERBAIKAN
        ================================================= --}}
        <div class="petugas-summary-card petugas-report-summary-card">

            <div class="petugas-summary-title">

                {{-- Icon tools --}}
                <svg
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="#525151"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M14.7 6.3a4 4 0 0 0-5.4 5.4L3 18l3 3 6.3-6.3a4 4 0 0 0 5.4-5.4l-2.3 2.3-2.7-.6-.6-2.7z"/>
                </svg>

                <span>
                    Dalam Perbaikan
                </span>

            </div>


            <div class="petugas-summary-number">
                {{ $facilitiesUnderRepair }}
            </div>

        </div>

    </div>

    <div class="petugas-queues">
        <x-dashboard.petugas-reservation-queue-list />

        {{-- =================================================
             ANTRIAN LAPORAN
        ================================================== --}}

        <div style="
            flex: 1;
            min-width: 465px;
            background: rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(151, 71, 255, 0.7);
            border-radius: 10px;
            padding: 18px 20px;
            font-family: 'Sora', Helvetica, sans-serif;
        ">

            <h2 style="
                margin: 0 0 14px;
                font-size: 25px;
                font-weight: 700;
                color: #000;
            ">
                Antrian Laporan
            </h2>

            @forelse ($latestReports as $report)

                <div style="
                    padding: 14px 0;
                    border-top: 1px solid #BD93F8;
                    @if ($loop->first) border-top: none; @endif
                    position: relative;
                ">

                    {{-- Nama fasilitas --}}
                    <div style="
                        font-size: 16px;
                        font-weight: 700;
                        color: #000;
                        margin-bottom: 4px;
                    ">
                        {{ $report->facility->name }}
                    </div>

                    {{-- Informasi laporan --}}
                    <div style="
                        font-size: 13px;
                        color: #525151;
                        margin-bottom: 2px;
                    ">
                        Pelapor: {{ $report->user->name }}
                    </div>

                    <div style="
                        font-size: 13px;
                        color: #525151;
                        margin-bottom: 2px;
                    ">
                        {{ $report->description
                            ? '"' . \Illuminate\Support\Str::limit(
                                $report->description,
                                55
                            ) . '"'
                            : $report->category
                        }}
                    </div>

                    <div style="
                        font-size: 13px;
                        color: #525151;
                    ">
                        Dilaporkan pada
                        {{ $report->created_at
                            ->locale('id')
                            ->isoFormat('D MMMM YYYY')
                        }}
                    </div>


                    {{-- Status --}}
                    <span style="
                        position: absolute;
                        top: 14px;
                        right: 0;

                        padding: 4px 9px;

                        border-radius: 5px;

                        font-family: 'Sora', Helvetica, sans-serif;

                        font-size: 10px;
                        font-weight: 600;

                        white-space: nowrap;

                        @if ($report->status === 'baru')
                            background: #BFF5FF;
                            color: #155A6B;
                        @elseif ($report->status === 'diproses')
                            background: #FFD994;
                            color: #805B00;
                        @elseif ($report->status === 'selesai')
                            background: #BFF5C3;
                            color: #176B2C;
                        @elseif ($report->status === 'ditolak')
                            background: #FFB5B5;
                            color: #9E1D1D;
                        @endif">
                        {{ ucfirst($report->status) }}
                    </span>

                </div>

            @empty

                <p class="petugas-empty">
                    Tidak ada laporan yang sedang menunggu proses.
                </p>

            @endforelse

            @if ($latestReports->isNotEmpty())
                <a
                    href="{{ route('petugas.reports.index') }}"
                    style="
                        display: block;
                        margin-top: 10px;
                        color: #9747FF;
                        font-weight: 600;
                        text-decoration: none;
                        font-family: 'Sora', Helvetica, sans-serif;">
                    Lihat semua &rarr;
                </a>

            @endif

        </div>

    </div>

</div>

@endsection
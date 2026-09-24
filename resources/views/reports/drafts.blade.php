@extends('layouts.dashboard')

@section('title', 'Draft Laporan')

@section('content')

<style>
    .draft-page {
        max-width: 1050px;
        margin: 0 auto;
    }

    .back-link {
        display: inline-block;
        margin-bottom: 15px;
        color: #260f45;
        font-size: 13px;
        font-weight: 600;
    }

    .draft-title {
        color: #260f45;
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 20px;
    }

    .draft-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .draft-item {
        background: #ffffff;
        border: 1px solid #bd93f8;
        border-radius: 6px;
        padding: 13px 17px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: #260f45;
    }

    .draft-item:hover {
        background: #fbf7ff;
    }

    .draft-main {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .draft-facility {
        font-size: 13px;
        font-weight: 700;
    }

    .draft-info {
        font-size: 10px;
        color: #6d6078;
    }

    .draft-arrow {
        font-size: 22px;
        color: #260f45;
    }

    .empty-draft {
        background: #ffffff;
        border: 1px solid #d5bbfb;
        border-radius: 7px;
        padding: 40px;
        text-align: center;
        color: #76677f;
    }
</style>

<div class="draft-page">

    <a
        href="{{ route('pengguna.reports.create') }}"
        class="back-link"
    >
        ← Kembali ke Lapor Kerusakan
    </a>

    <h1 class="draft-title">
        Draft Laporan
    </h1>

    @if (session('status'))
        <div class="flash-status">
            {{ session('status') }}
        </div>
    @endif

    @if ($reports->isEmpty())

        <div class="empty-draft">
            Belum ada draft laporan.
        </div>

    @else

        <div class="draft-list">

            @foreach ($reports as $report)

                <a
                    href="{{ route(
                        'pengguna.reports.preview',
                        $report
                    ) }}"
                    class="draft-item"
                >

                    <div class="draft-main">

                        <div class="draft-facility">
                            {{ $report->facility->name }},
                            {{ $report->facility->location->gedung ?? '-' }},
                            {{ $report->facility->location->fakultas ?? '-' }}
                        </div>

                        <div class="draft-info">
                            Kategori Kerusakan:
                            {{ $report->category }}
                        </div>

                        <div class="draft-info">
                            {{ $report->description ?: 'Belum ada deskripsi.' }}
                        </div>

                    </div>

                    <div class="draft-arrow">
                        ›
                    </div>

                </a>

            @endforeach

        </div>

    @endif

</div>

@endsection
@extends('layouts.dashboard')

@section('title', 'Draft Laporan')

@section('content')

<style>
    .preview-page {
        max-width: 1050px;
        margin: 0 auto;
    }

    .back-link {
        display: inline-block;
        margin-bottom: 18px;
        color: #260f45;
        font-size: 13px;
        font-weight: 600;
    }

    .preview-card {
        background: #ffffff;
        border: 1px solid #bd93f8;
        border-radius: 7px;
        padding: 16px;
    }

    .preview-location {
        font-size: 13px;
        font-weight: 700;
        color: #260f45;
    }

    .preview-category,
    .preview-description {
        margin-top: 5px;
        font-size: 11px;
        color: #4e405d;
    }

    .photo-grid {
        margin-top: 15px;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 12px;
    }

    .photo-grid img {
        width: 100%;
        height: 160px;
        object-fit: cover;
        border-radius: 5px;
        border: 1px solid #d5bbfb;
    }

    .no-photo {
        padding: 25px;
        border: 1px dashed #bd93f8;
        text-align: center;
        color: #76677f;
        font-size: 12px;
    }

    .button-row {
        margin-top: 18px;
        display: flex;
        justify-content: flex-end;
        gap: 8px;
    }

    .btn {
        border: 1px solid #9747ff;
        border-radius: 5px;
        padding: 8px 16px;
        font-family: 'Sora', sans-serif;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-edit {
        background: #d5bbfb;
        color: #260f45;
    }

    .btn-submit {
        background: #9747ff;
        color: #ffffff;
    }

    .error-box {
        margin-bottom: 15px;
        padding: 12px;
        border: 1px solid #f3aaaa;
        background: #fff1f1;
        color: #a12626;
        border-radius: 7px;
        font-size: 12px;
    }
</style>

<div class="preview-page">

    <a
        href="{{ route('pengguna.reports.drafts') }}"
        class="back-link"
    >
        ← Draft Laporan
    </a>

    @if ($errors->any())

        <div class="error-box">

            {{ $errors->first() }}

        </div>

    @endif


    <div class="preview-card">

        <div class="preview-location">
            {{ $report->facility->name }},
            {{ $report->facility->location->gedung ?? '-' }},
            {{ $report->facility->location->fakultas ?? '-' }}
        </div>

        <div class="preview-category">
            Kategori Kerusakan:
            {{ $report->category }}
        </div>

        <div class="preview-description">
            {{ $report->description ?: '-' }}
        </div>


        <div class="photo-grid">

            @forelse ($report->photos as $photo)

                <img
                    src="{{ asset(
                        'storage/' . $photo->file_path
                    ) }}"
                    alt="Foto kerusakan"
                >

            @empty

                <div class="no-photo">
                    Belum ada foto.
                </div>

            @endforelse

        </div>


        <div class="button-row">

            <a
                href="{{ route(
                    'pengguna.reports.edit',
                    $report
                ) }}"
                class="btn btn-edit"
            >
                Edit data
            </a>

            <form
                action="{{ route(
                    'pengguna.reports.submit',
                    $report
                ) }}"
                method="POST"
            >

                @csrf

                <button
                    type="submit"
                    class="btn btn-submit"
                >
                    Kirim laporan
                </button>

            </form>

        </div>

    </div>

</div>

@endsection
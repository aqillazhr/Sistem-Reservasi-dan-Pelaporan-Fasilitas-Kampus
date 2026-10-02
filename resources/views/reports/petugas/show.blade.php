@extends('layouts.dashboard')

@section('title', 'Detail Laporan')

@section('content')

<style>
    .detail-page {
        max-width: 950px;
        margin: 0 auto;
    }

    .back-link {
        color: #501e91;
        font-weight: 600;
        text-decoration: none;
    }

    .detail-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 20px 0;
    }

    .detail-title {
        margin: 0;
        font-size: 32px;
        font-weight: 700;
        color: #501e91;
    }

    .card {
        background: #fff;
        border: 1px solid #bd93f8;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 18px;
    }

    .card h2 {
        margin: 0 0 16px;
        color: #260f45;
        font-size: 18px;
    }

    .row {
        margin-bottom: 10px;
        font-size: 14px;
        color: #260f45;
    }

    .label {
        font-weight: 700;
    }

    .status {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
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

    .photo-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
    }

    .photo-grid img {
        width: 100%;
        height: 190px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #d5bbfb;
    }

    .form-group {
        margin-bottom: 14px;
    }

    .form-label {
        display: block;
        margin-bottom: 7px;
        color: #260f45;
        font-size: 14px;
        font-weight: 600;
    }

    .form-control {
        width: 100%;
        box-sizing: border-box;
        padding: 10px 12px;
        border: 1px solid #bd93f8;
        border-radius: 7px;
        background: #fff;
        color: #260f45;
        font-family: inherit;
        font-size: 14px;
    }

    textarea.form-control {
        min-height: 100px;
        resize: vertical;
    }

    .error-message {
        margin-top: 6px;
        color: #a12626;
        font-size: 13px;
    }

    .btn {
        border: none;
        border-radius: 7px;
        padding: 10px 16px;
        font-weight: 700;
        cursor: pointer;
        font-family: inherit;
    }

    .btn-primary {
        background: #9747ff;
        color: #fff;
    }

    .btn-primary:hover {
        background: #8035e6;
    }

    .facility-status {
        margin: 0;
        color: #260f45;
        font-size: 15px;
    }

    .facility-status strong {
        font-weight: 700;
    }

    .closed-note {
        color: #260f45;
        font-size: 14px;
        line-height: 1.6;
        white-space: pre-line;
    }

    @media (max-width: 700px) {
        .detail-page {
            width: 100%;
        }

        .detail-header {
            align-items: flex-start;
            gap: 12px;
        }

        .detail-title {
            font-size: 28px;
        }

        .photo-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="detail-page">

    <a
        href="{{ route('petugas.reports.index') }}"
        class="back-link"
    >
        ← Kembali ke Laporan Masuk
    </a>

    <div class="detail-header">

        <h1 class="detail-title">
            Detail Laporan
        </h1>

        <span class="status status-{{ $report->status }}">
            {{ ucfirst($report->status) }}
        </span>

    </div>


    {{-- Informasi laporan --}}
    <div class="card">

        <h2>Informasi Laporan</h2>

        <div class="row">
            <span class="label">Pelapor:</span>
            {{ $report->user->name }}
        </div>

        <div class="row">
            <span class="label">Email:</span>
            {{ $report->user->email }}
        </div>

        <div class="row">
            <span class="label">Fasilitas:</span>
            {{ $report->facility->name }}
        </div>

        <div class="row">
            <span class="label">Tipe:</span>
            {{ $report->facility->type->name ?? '-' }}
        </div>

        <div class="row">
            <span class="label">Lokasi:</span>
            {{ $report->facility->location->gedung ?? '-' }}
        </div>

        <div class="row">
            <span class="label">Ruangan:</span>
            {{ $report->facility->location->ruangan ?? '-' }}
        </div>

        <div class="row">
            <span class="label">Kategori:</span>
            {{ $report->category }}
        </div>

        <div class="row">
            <span class="label">Deskripsi:</span>
            {{ $report->description ?: '-' }}
        </div>

        <div class="row">
            <span class="label">Dilaporkan:</span>
            {{ $report->created_at->format('d M Y H:i') }}
        </div>

    </div>


    {{-- Dokumentasi kerusakan --}}
    <div class="card">

        <h2>Dokumentasi Kerusakan</h2>

        <div class="photo-grid">

            @forelse ($report->photos as $photo)

                <img
                    src="{{ asset('storage/' . $photo->file_path) }}"
                    alt="Foto kerusakan"
                >

            @empty

                <p>Tidak ada foto.</p>

            @endforelse

        </div>

    </div>


    {{-- Proses laporan --}}
    @if (in_array($report->status, ['baru', 'diproses']))

        <div class="card">

            <h2>Proses Laporan</h2>

            <form
                method="POST"
                action="{{ route('petugas.reports.update-status', $report) }}"
                class="form-confirm"
                data-confirm-title="Konfirmasi perubahan"
                data-confirm-msg="Apakah Anda yakin ingin menyimpan perubahan status laporan ini?"
                id="reportStatusForm">

                @csrf
                @method('PATCH')


                <div class="form-group">

                    <label
                        for="status"
                        class="form-label"
                    >
                        Status laporan
                    </label>

                    <select
                        name="status"
                        id="status"
                        class="form-control"
                    >

                        @if ($report->status === 'baru')
                            <option
                                value="diproses"
                                {{ old('status') === 'diproses' ? 'selected' : '' }}>
                                Diproses
                            </option>

                            <option
                                value="ditolak"
                                {{ old('status') === 'ditolak' ? 'selected' : '' }}>
                                Ditolak
                            </option>

                        @elseif ($report->status === 'diproses')

                            <option
                                value="diproses"
                                {{ old('status', 'diproses') === 'diproses' ? 'selected' : '' }}>
                                Diproses
                            </option>

                            <option
                                value="selesai"
                                {{ old('status') === 'selesai' ? 'selected' : '' }}>
                                Selesai
                            </option>

                            <option
                                value="ditolak"
                                {{ old('status') === 'ditolak' ? 'selected' : '' }}>
                                Ditolak
                            </option>

                        @endif

                    </select>

                    @error('status')
                        <div class="error-message">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                <div
                    class="form-group"
                    id="facility-action-group"
                >

                    <label
                        for="facility_action"
                        class="form-label"
                    >
                        Kondisi fasilitas
                    </label>

                    <select
                        name="facility_action"
                        id="facility_action"
                        class="form-control">

                        <option
                            value="tetap_aktif"
                            {{ old(
                                'facility_action',
                                $report->facility->status === 'aktif'
                                    ? 'tetap_aktif'
                                    : ''
                            ) === 'tetap_aktif' ? 'selected' : '' }}
                        >
                            Aktif
                        </option>

                        <option
                            value="dalam_perbaikan"
                            {{ old(
                                'facility_action',
                                $report->facility->status === 'dalam perbaikan'
                                    ? 'dalam_perbaikan'
                                    : ''
                            ) === 'dalam_perbaikan' ? 'selected' : '' }}
                        >
                            Dalam Perbaikan
                        </option>

                    </select>

                    @error('facility_action')
                        <div class="error-message">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                <div
                    class="form-group"
                    id="resolution-note-group"
                >

                    <label
                        for="resolution_note"
                        class="form-label">
                        Catatan resolusi <span style="color: #d93025;">*</span>
                    </label>

                    <textarea
                        name="resolution_note"
                        id="resolution_note"
                        class="form-control"
                        placeholder="Isi catatan resolusi."
                    >{{ old('resolution_note') }}</textarea>

                    <div
                        id="resolution-client-error"
                        class="error-message"
                        style="display: none;"
                    >
                        Catatan resolusi wajib diisi.
                    </div>

                    @error('resolution_note')
                        <div class="error-message">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Simpan Perubahan
                </button>

            </form>

        </div>

    @endif


    {{-- Status fasilitas --}}
    <div class="card">

        <h2>Status Fasilitas</h2>

        <p class="facility-status">

            Status fasilitas saat ini:

            <strong>
                {{ ucfirst($report->facility->status) }}
            </strong>

        </p>

    </div>


    {{-- Catatan resolusi setelah laporan ditutup --}}
    @if (in_array($report->status, ['selesai', 'ditolak']))

        <div class="card">

            <h2>Catatan Resolusi</h2>

            <p class="closed-note">
                {{ $report->resolution_note ?: '-' }}
            </p>

            @if ($report->resolved_at)

                <p class="closed-note">

                    <strong>Ditutup:</strong>
                    {{ $report->resolved_at->format('d M Y H:i') }}

                </p>

            @endif

        </div>

    @endif

</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {

        const statusSelect = document.getElementById('status');
        const facilityGroup = document.getElementById('facility-action-group');
        const resolutionGroup = document.getElementById('resolution-note-group');
        const resolutionNote = document.getElementById('resolution_note');
        const resolutionError = document.getElementById('resolution-client-error');
        const reportForm = document.getElementById('reportStatusForm');

        if (!statusSelect) {
            return;
        }

        function updateFormVisibility() {

            const status = statusSelect.value;

            facilityGroup.style.display =
                status === 'diproses'
                    ? 'block'
                    : 'none';

            resolutionGroup.style.display =
                status === 'selesai' || status === 'ditolak'
                    ? 'block'
                    : 'none';

            if (status !== 'selesai' && status !== 'ditolak') {
                resolutionError.style.display = 'none';
            }
        }

        statusSelect.addEventListener(
            'change',
            updateFormVisibility
        );

        reportForm.addEventListener('submit', function (event) {

            const status = statusSelect.value;

            const needsResolutionNote =
                status === 'selesai' ||
                status === 'ditolak';

            if (needsResolutionNote && resolutionNote.value.trim() === '') {
                event.preventDefault();
                event.stopPropagation();

                resolutionError.style.display = 'block';

                resolutionNote.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });

                resolutionNote.focus();

                return;
            }

            resolutionError.style.display = 'none';
        });

        updateFormVisibility();

        const firstError = document.querySelector('.error-message');

        if (firstError && firstError.id !== 'resolution-client-error') {
            setTimeout(function () {
                firstError.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }, 100);
        }

    });
</script>

@endsection
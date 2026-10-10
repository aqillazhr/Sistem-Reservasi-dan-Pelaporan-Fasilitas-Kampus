@extends('layouts.dashboard')

@section('title', 'Kelola Fasilitas')

@push('styles')
    <style>
        .admin-facilities {
            max-width: 1500px;
            margin: 0 auto;
            color: #321750;
        }

        .admin-facilities * {
            box-sizing: border-box;
        }

        /* Header halaman */
        .admin-facilities .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 28px;
        }

        .admin-facilities .eyebrow {
            margin: 0 0 8px;
            color: #8050b8;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .admin-facilities h1 {
            margin: 0;
            color: #321750;
            font-size: 30px;
            font-weight: 700;
        }

        .admin-facilities .page-description {
            margin: 8px 0 0;
            color: #756580;
            font-size: 14px;
            line-height: 1.6;
        }

        /* Tombol tambah */
        .admin-facilities .add-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            flex-shrink: 0;
            padding: 12px 18px;
            border: 0;
            border-radius: 9px;
            background: #7542b5;
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            box-shadow: 0 4px 10px rgba(117, 66, 181, 0.18);
            transition: background .2s, transform .2s;
        }

        .admin-facilities .add-button:hover {
            background: #603195;
            transform: translateY(-1px);
        }

        .admin-facilities .plus-icon {
            font-size: 21px;
            line-height: 1;
        }

        /* Panel tabel */
        .admin-facilities .content-panel {
            overflow: hidden;
            border: 1px solid #e9def5;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 5px 20px rgba(65, 34, 91, 0.06);
        }

        .admin-facilities .panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 22px 24px;
            border-bottom: 1px solid #eee6f5;
        }

        .admin-facilities .panel-title {
            margin: 0;
            color: #321750;
            font-size: 18px;
            font-weight: 700;
        }

        .admin-facilities .panel-description {
            margin: 6px 0 0;
            color: #897c96;
            font-size: 12px;
        }

        /* Pencarian */
        .admin-facilities .search-form {
            display: flex;
            width: min(100%, 390px);
            gap: 8px;
        }

        .admin-facilities .search-input {
            width: 100%;
            min-width: 0;
            height: 40px;
            padding: 0 13px;
            border: 1px solid #dfd1ee;
            border-radius: 8px;
            outline: none;
            background: #fff;
            color: #321750;
            font-family: inherit;
            font-size: 12px;
        }

        .admin-facilities .search-input:focus {
            border-color: #9361cb;
            box-shadow: 0 0 0 3px rgba(147, 97, 203, 0.12);
        }

        .admin-facilities .search-button,
        .admin-facilities .reset-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 40px;
            padding: 0 14px;
            border-radius: 8px;
            font-family: inherit;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            white-space: nowrap;
        }

        .admin-facilities .search-button {
            border: 1px solid #7542b5;
            background: #7542b5;
            color: #fff;
        }

        .admin-facilities .search-button:hover {
            background: #603195;
        }

        .admin-facilities .reset-button {
            border: 1px solid #dfd1ee;
            background: #fff;
            color: #68438c;
        }

        /* Tabel */
        .admin-facilities .table-scroll {
            width: 100%;
            overflow-x: auto;
        }

        .admin-facilities .facility-table {
            width: 100%;
            min-width: 950px;
            border-collapse: separate;
            border-spacing: 0;
            text-align: left;
        }

        .admin-facilities .facility-table th {
            padding: 14px 16px;
            border-bottom: 1px solid #e9def5;
            background: #f5effc;
            color: #69438e;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .3px;
            white-space: nowrap;
        }

        /* Judul kolom Aksi rata tengah */
        .admin-facilities .facility-table th:last-child {
            text-align: center;
        }

        /* Tombol aksi ikut berada di tengah kolom */
        .admin-facilities .facility-table td:last-child .action-buttons {
            justify-content: center;
        }

        .admin-facilities .facility-table td {
            padding: 15px 16px;
            border-bottom: 1px solid #f0eaf5;
            color: #4c3b5d;
            font-size: 12px;
            line-height: 1.6;
            vertical-align: middle;
        }

        .admin-facilities .facility-table tbody tr:last-child td {
            border-bottom: none;
        }

        .admin-facilities .facility-table tbody tr:hover {
            background: #fcf9ff;
        }

        .admin-facilities .number-cell {
            width: 55px;
            color: #9788a5 !important;
            text-align: center;
        }

        .admin-facilities .facility-name {
            min-width: 170px;
            color: #321750;
            font-size: 12px;
            font-weight: 700;
        }

        .admin-facilities .secondary-text {
            display: block;
            margin-top: 2px;
            color: #93859f;
            font-size: 11px;
        }

        .admin-facilities .capacity-cell {
            white-space: nowrap;
        }

        /* Label status */
        .admin-facilities .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 9px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
            white-space: nowrap;
        }

        .admin-facilities .status-badge::before {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
            content: "";
        }

        .admin-facilities .status-active {
            background: #e7f7ed;
            color: #25834c;
        }

        .admin-facilities .status-maintenance {
            background: #fff3d9;
            color: #996b12;
        }

        .admin-facilities .status-inactive {
            background: #f0edf2;
            color: #756b7d;
        }

        /* Tombol aksi */
        .admin-facilities .edit-button {
            padding: 7px 12px;
            border: 1px solid #d4b9f0;
            border-radius: 7px;
            background: #f3eafb;
            color: #60349a;
            font-family: inherit;
            font-size: 11px;
            font-weight: 600;
            cursor: not-allowed;
            opacity: .8;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.2s, color 0.2s;
        }

        /* Tombol aksi dalam satu baris */
        .admin-facilities .action-buttons {
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }

        .admin-facilities .action-buttons form {
            margin: 0;
        }

        .admin-facilities .toggle-button {
            width: 100px;
            min-width: 100px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 7px 10px;
            border: 1px solid transparent;
            border-radius: 7px;
            font-family: inherit;
            font-size: 11px;
            font-weight: 600;
            text-align: center;
            cursor: pointer;
            white-space: nowrap;
            transition: background .2s;
        }

        /* Tombol Nonaktifkan */
        .admin-facilities .toggle-disable {
            background: #fff0ee;
            border-color: #f4c6c1;
            color: #b42318;
        }

        .admin-facilities .toggle-disable:hover {
            background: #ffded9;
        }

        /* Tombol Aktifkan */
        .admin-facilities .toggle-enable {
            background: #e7f7ed;
            border-color: #b8e5c7;
            color: #247943;
        }

        .admin-facilities .toggle-enable:hover {
            background: #d1f0dc;
        }

        /* Fasilitas yang sedang dalam perbaikan */
        .admin-facilities .toggle-maintenance {
            background: #fff3d9;
            border-color: #f0dda9;
            color: #996b12;
            cursor: not-allowed;
        }

        .admin-facilities .empty-state {
            padding: 45px 20px !important;
            color: #887792 !important;
            text-align: center;
        }

        /* Pagination */
        .admin-facilities .pagination-wrap {
            padding: 18px 24px;
            border-top: 1px solid #eee6f5;
            color: #897c96;
            font-size: 12px;
        }

        .admin-facilities .pagination-wrap nav p {
            font-size: 12px;
        }

        .admin-facilities .pagination-wrap nav a {
            color: #7542b5;
        }

        @media (max-width: 760px) {

            .admin-facilities .page-header,
            .admin-facilities .panel-header {
                align-items: stretch;
                flex-direction: column;
            }

            .admin-facilities h1 {
                font-size: 25px;
            }

            .admin-facilities .add-button {
                align-self: flex-start;
            }

            .admin-facilities .search-form {
                width: 100%;
            }

            .admin-facilities .panel-header {
                padding: 18px;
            }
        }

        /* Judul kolom Status rata tengah */
        .admin-facilities .facility-table th:nth-child(7) {
            text-align: center;
        }

        /* Isi kolom Status rata tengah */
        .admin-facilities .facility-table td:nth-child(7) {
            text-align: center;
        }
    </style>
@endpush

@section('content')
    <div class="admin-facilities">

        <div class="page-header">
            <div>
                <p class="eyebrow">Administrasi</p>
                <h1>Kelola Fasilitas</h1>
                <p class="page-description">
                    Kelola informasi dan data fasilitas kampus.
                </p>
            </div>

            <a href="{{ route('admin.facilities.create') }}" class="add-button">
                <span class="plus-icon">+</span>
                Tambah Fasilitas
            </a>
        </div>

        <div class="content-panel">

            <div class="panel-header">
                <div>
                    <h2 class="panel-title">Daftar Fasilitas</h2>
                    <p class="panel-description">
                        Daftar fasilitas yang terdaftar dalam sistem.
                    </p>
                </div>

                <form action="{{ route('admin.facilities.index') }}" method="GET" class="search-form">

                    <input type="text" name="search" class="search-input"
                        placeholder="Cari nama, tipe, fakultas, gedung..." value="{{ $search ?? '' }}">

                    <button type="submit" class="search-button">
                        Cari
                    </button>

                    @if (!empty($search))
                        <a href="{{ route('admin.facilities.index') }}" class="reset-button">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            <div class="table-scroll">
                <table class="facility-table">
                    <thead>
                        <tr>
                            <th class="number-cell">No.</th>
                            <th>Nama Fasilitas</th>
                            <th>Tipe</th>
                            <th>Fakultas / Prodi</th>
                            <th>Gedung / Ruangan</th>
                            <th>Kapasitas</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($facilities as $facility)
                            <tr>
                                <td class="number-cell">
                                    {{ $facilities->firstItem() + $loop->index }}
                                </td>

                                <td class="facility-name">
                                    {{ $facility->name }}
                                </td>

                                <td>
                                    {{ $facility->type->name ?? '-' }}
                                </td>

                                <td>
                                    {{ $facility->location->fakultas ?? '-' }}
                                    <span class="secondary-text">
                                        {{ $facility->location->prodi ?? '-' }}
                                    </span>
                                </td>

                                <td>
                                    {{ $facility->location->gedung ?? '-' }}
                                    <span class="secondary-text">
                                        {{ $facility->location->ruangan ?? '-' }}
                                    </span>
                                </td>

                                <td class="capacity-cell">
                                    @if ($facility->capacity !== null)
                                        {{ $facility->capacity }} orang
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>
                                    @if ($facility->status === 'aktif')
                                        <span class="status-badge status-active">
                                            Aktif
                                        </span>
                                    @elseif ($facility->status === 'dalam perbaikan')
                                        <span class="status-badge status-maintenance">
                                            Dalam Perbaikan
                                        </span>
                                    @else
                                        <span class="status-badge status-inactive">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <div class="action-buttons">

                                        <a href="{{ route('admin.facilities.edit', $facility) }}" class="edit-button">
                                            Edit
                                        </a>

                                        {{-- Fasilitas aktif: tampilkan Nonaktifkan --}}
                                        @if ($facility->status === 'aktif')
                                            <form action="{{ route('admin.facilities.toggle-active', $facility) }}"
                                                method="POST"
                                                onsubmit="return confirm('Yakin ingin menonaktifkan fasilitas ini?')">
                                                @csrf
                                                @method('PATCH')

                                                <button type="submit" class="toggle-button toggle-disable">
                                                    Nonaktifkan
                                                </button>
                                            </form>

                                            {{-- Fasilitas nonaktif: tampilkan Aktifkan --}}
                                        @elseif ($facility->status === 'nonaktif')
                                            <form action="{{ route('admin.facilities.toggle-active', $facility) }}"
                                                method="POST"
                                                onsubmit="return confirm('Yakin ingin mengaktifkan kembali fasilitas ini?')">
                                                @csrf
                                                @method('PATCH')

                                                <button type="submit" class="toggle-button toggle-enable">
                                                    Aktifkan
                                                </button>
                                            </form>

                                            {{-- Fasilitas dalam perbaikan tidak diubah lewat tombol ini --}}
                                        @else
                                            <button type="button" class="toggle-button toggle-maintenance" disabled
                                                title="Status fasilitas sedang dalam perbaikan">
                                                Dalam Perbaikan
                                            </button>
                                        @endif

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="empty-state">
                                    @if (!empty($search))
                                        Fasilitas yang dicari tidak ditemukan.
                                    @else
                                        Belum ada data fasilitas.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrap">
                {{ $facilities->links() }}
            </div>

        </div>
    </div>
@endsection

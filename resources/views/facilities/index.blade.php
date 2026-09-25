@extends('layouts.dashboard')

@section('title', 'Hasil Pencarian')

@push('styles')
    <style>
        .search-page {
            padding: 10px 0 40px;
        }

        .search-page h1 {
            color: #54269a;
            font-size: 40px;
            margin-top: 0;
            margin-bottom: 20px;
        }

        .search-page>p {
            margin-bottom: 20px;
        }

        /* =========================
           FILTER
        ========================= */
        .filter-box {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .filter-title {
            color: #54269a;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 18px;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .filter-group label {
            font-size: 14px;
            font-weight: 600;
            color: #444;
        }

        .filter-group select,
        .filter-group input {
            width: 100%;
            box-sizing: border-box;
            padding: 10px 12px;
            border: 1px solid #d8cde7;
            border-radius: 8px;
            background: white;
            font-size: 14px;
            outline: none;
        }

        .filter-group select:focus,
        .filter-group input:focus {
            border-color: #54269a;
        }

        .capacity-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .filter-actions {
            display: flex;
            gap: 10px;
            margin-top: 18px;
        }

        .filter-button {
            background: #54269a;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }

        .filter-button:hover {
            background: #7133d1;
        }

        .reset-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 20px;
            border-radius: 8px;
            background: #eee7f7;
            color: #54269a;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }

        .reset-button:hover {
            background: #e2d6f0;
        }

        /* =========================
           HASIL
        ========================= */
        .search-page hr {
            border: none;
            border-top: 1px solid #e0d5ee;
            margin: 20px 0 25px;
        }

        .result-info {
            margin-bottom: 20px;
        }

        .facility-card {
            display: flex;
            align-items: center;
            gap: 20px;
            background: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .facility-image {
            width: 180px;
            height: 180px;
            flex-shrink: 0;
        }

        .facility-image img,
        .no-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            background: #ddd;
            border-radius: 5px;
        }

        .facility-info {
            flex: 1;
        }

        .facility-info h2 {
            color: #54269a;
            margin-top: 0;
        }

        .facility-details div {
            display: grid;
            grid-template-columns: 130px 20px 1fr;
            margin: 5px 0;
        }

        .facility-details span::after {
            content: ":";
            margin-left: 10px;
        }

        .facility-arrow {
            font-size: 60px;
            color: #8f6bc1 !important;
            text-decoration: none !important;
            padding: 20px;
            line-height: 1;
        }

        .facility-arrow:hover {
            color: #54269a !important;
        }

        /* =========================
           RESPONSIVE
        ========================= */
        @media (max-width: 1000px) {
            .filter-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 700px) {
            .filter-grid {
                grid-template-columns: 1fr;
            }

            .facility-card {
                flex-direction: column;
                align-items: flex-start;
            }

            .facility-image {
                width: 100%;
                height: 220px;
            }

            .facility-arrow {
                align-self: flex-end;
            }
        }
    </style>
@endpush

@section('content')

    <div class="search-page">

        <h1>Hasil Pencarian</h1>


    {{-- =========================
         FILTER
    ========================= --}}

        <div class="filter-box">

            <div class="filter-title">
                Filter Fasilitas
            </div>

            <form method="GET" action="{{ route('facilities.index') }}">

                {{-- supaya search navbar tetap ikut saat filter --}}
                <input type="hidden" name="search" value="{{ $search }}">

                <div class="filter-grid">

                    {{-- Tipe --}}
                    <div class="filter-group">

                        <label for="type_id">
                            Tipe
                        </label>

                        <select name="type_id" id="type_id">

                            <option value="">
                                Semua tipe
                            </option>

                            @foreach ($types as $type)
                                <option value="{{ $type->id }}" @selected((string) $typeId === (string) $type->id)>
                                    {{ $type->name }}
                                </option>
                            @endforeach

                        </select>

                    </div>


                    {{-- Kapasitas --}}
                    <div class="filter-group">

                        <label>
                            Kapasitas
                        </label>

                        <div class="capacity-group">

                            <input type="number" name="capacity_min" placeholder="Minimal" min="1"
                                value="{{ $capacityMin }}">

                            <input type="number" name="capacity_max" placeholder="Maksimal" min="1"
                                value="{{ $capacityMax }}">

                        </div>

                    </div>


                    {{-- Status --}}
                    <div class="filter-group">

                        <label for="status">
                            Status
                        </label>

                        <select name="status" id="status">

                            <option value="">
                                Semua status
                            </option>

                            <option value="aktif" @selected($status === 'aktif')>
                                Aktif
                            </option>

                            <option value="dalam perbaikan" @selected($status === 'dalam perbaikan')>
                                Dalam Perbaikan
                            </option>

                            <option value="nonaktif" @selected($status === 'nonaktif')>
                                Nonaktif
                            </option>

                        </select>

                    </div>

                </div>


                <div class="filter-actions">

                    <button type="submit" class="filter-button">
                        Terapkan Filter
                    </button>

                    <a href="{{ route('facilities.index') }}" class="reset-button">
                        Reset
                    </a>

                </div>

            </form>

        </div>


        {{-- =========================
         INFORMASI SEARCH/FILTER
    ========================= --}}

        @if ($search || $typeId || $capacityMin || $capacityMax)

            <div class="result-info">

                <p>
                    Hasil pencarian

                    @if ($search)
                        untuk: <strong>{{ $search }}</strong>
                    @endif

                    @if ($capacityMin)
                        · kapasitas minimal
                        <strong>{{ $capacityMin }}</strong> orang
                    @endif

                    @if ($capacityMax)
                        · kapasitas maksimal
                        <strong>{{ $capacityMax }}</strong> orang
                    @endif
                </p>

            </div>

        @endif


        <hr>


        {{-- =========================
         HASIL FASILITAS
    ========================= --}}

        @forelse ($facilities as $facility)
            <div class="facility-card">


                {{-- Foto --}}

                <div class="facility-image">

                    @if ($facility->photos->first())
                        <img src="{{ asset('storage/' . $facility->photos->first()->file_path) }}"
                            alt="{{ $facility->name }}">
                    @else
                        <div class="no-image"></div>
                    @endif

                </div>


                {{-- Informasi --}}

                <div class="facility-info">

                    <h2>
                        {{ $facility->name }}
                    </h2>


                    <div class="facility-details">

                        <div>
                            <span>Tipe</span>

                            <strong>
                                {{ $facility->type->name ?? '-' }}
                            </strong>
                        </div>


                        <div>
                            <span>Kapasitas</span>

                            <strong>
                                {{ $facility->capacity }} orang
                            </strong>
                        </div>


                        <div>
                            <span>Status</span>

                            <strong>
                                {{ ucfirst($facility->status) }}
                            </strong>
                        </div>


                        <div>
                            <span>Ketersediaan</span>

                            <strong>
                                Tersedia
                            </strong>
                        </div>


                        <div>
                            <span>Lokasi</span>

                            <strong>
                                {{ $facility->location->ruangan ?? '-' }}
                            </strong>
                        </div>

                    </div>

                </div>


                {{-- Panah / detail --}}

                <a href="{{ route('facilities.show', [
                    'facility' => $facility,
                    'from' => url()->full(),
                ]) }}"
                    class="facility-arrow">
                    ›
                </a>

            </div>


        @empty

            <p>
                Tidak ada fasilitas yang ditemukan.
            </p>
        @endforelse


        {{-- Pagination --}}

        @if ($facilities instanceof \Illuminate\Pagination\LengthAwarePaginator)
            {{ $facilities->links() }}
        @endif

    </div>

@endsection

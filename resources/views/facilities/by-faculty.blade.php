@extends('layouts.dashboard')

@section('title', $faculty)

@push('styles')
    <style>
        .faculty-page {
            padding: 40px;
        }

        .faculty-page h1 {
            color: #54269a;
            margin-bottom: 30px;
        }

        .facility-card {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
        }

        .facility-card h2 {
            margin-top: 0;
        }

        .faculty-page {
            padding: 20px 40px 40px;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            text-decoration: none;
            margin-bottom: 5px;
        }

        .back-button span {
            width: 13px;
            height: 13px;
            border-left: 4px solid #54269a;
            border-bottom: 4px solid #54269a;
            transform: rotate(45deg);
        }

        .filter-toggle {
            display: inline-flex;
            align-items: center;
            gap: 10px;

            background: #f3ebff;
            color: #54269a;

            border: 1.5px solid #d8c5f5;
            border-radius: 10px;

            padding: 9px 14px;

            font-size: 15px;
            font-weight: 700;

            cursor: pointer;

            transition:
                background 0.2s ease,
                border-color 0.2s ease,
                transform 0.2s ease;

            margin-bottom: 15px;
        }

        .filter-toggle:hover {
            background: #e9ddfb;
            border-color: #54269a;
        }

        /* Chevron */
        #filter-icon {
            width: 8px;
            height: 8px;

            border-right: 2px solid #54269a;
            border-bottom: 2px solid #54269a;

            transform: rotate(45deg);
            margin-top: -4px;

            transition: transform 0.25s ease;
        }

        /* Saat filter terbuka */
        .filter-toggle.active #filter-icon {
            transform: rotate(225deg);
            margin-top: 4px;
        }

        #filter-icon {
            font-size: 18px;
            transition: transform 0.2s ease;
        }

        .filter-panel {
            display: none;
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .filter-panel.active {
            display: block;
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

        .apply-filter {
            background: #54269a;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
        }

        .apply-filter:hover {
            background: #7133d1;
        }

        .reset-filter {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 20px;
            border-radius: 8px;
            background: #eee7f7;
            color: #54269a;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
@endpush

@section('content')

    <div class="faculty-page">
        <a href="{{ route('pengguna.dashboard') }}" class="back-button">
            <span></span>
        </a>

        <h1>{{ $faculty }}</h1>
        <button type="button" class="filter-toggle" onclick="toggleFilter()">
            <span>Filter</span>
            <span id="filter-icon"></span>
        </button>

        <div id="filter-panel" class="filter-panel">

            <form method="GET" action="{{ url()->current() }}">

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

                    <button type="submit" class="apply-filter">
                        Terapkan Filter
                    </button>

                    <a href="{{ url()->current() }}" class="reset-filter">
                        Reset
                    </a>

                </div>

            </form>

        </div>
        @forelse ($facilities as $facility)
            <div class="facility-card">

                <h2>{{ $facility->name }}</h2>

                <p>
                    <strong>Tipe:</strong>
                    {{ $facility->type->name ?? '-' }}
                </p>

                <p>
                    <strong>Kapasitas:</strong>
                    {{ $facility->capacity }} orang
                </p>

                <p>
                    <strong>Status:</strong>
                    {{ $facility->status }}
                </p>

                <p>
                    <strong>Lokasi:</strong>
                    {{ $facility->location->fakultas ?? '-' }}
                </p>

                <a
                    href="{{ route('facilities.show', [
                        'facility' => $facility,
                        'from' => url()->full(),
                    ]) }}">
                    Lihat Detail
                </a>

            </div>

        @empty

            <p>
                Belum ada fasilitas di fakultas ini.
            </p>
        @endforelse

    </div>
    <script>
        function toggleFilter() {
            const panel = document.getElementById('filter-panel');
            const button = document.querySelector('.filter-toggle');

            panel.classList.toggle('active');
            button.classList.toggle('active');
        }
    </script>
@endsection

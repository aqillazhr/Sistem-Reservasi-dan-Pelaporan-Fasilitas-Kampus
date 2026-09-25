@extends('layouts.dashboard')

@section('title', $faculty)

@push('styles')
<style>
    .group-header {
        display: flex;
        align-items: center;
        gap: 16px;
        background: #fff;
        border-radius: 8px;
        padding: 24px 40px;
        margin-bottom: 24px;
    }
    .group-header__back {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        color: #511F91;
        text-decoration: none;
        font-size: 22px;
        flex-shrink: 0;
    }
    .group-header__back:hover { background: #F0E6FF; }
    .group-header h1 {
        font-family: 'Sora', Helvetica, sans-serif;
        font-weight: 700;
        font-size: 40px;
        color: #511F91;
        margin: 0;
    }

    .group-body {
        background: #D5BBFB;
        border-radius: 8px;
        padding: 28px 40px;
    }

    /* Tombol toggle "Filter" */
    .filter-toggle {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: #fff;
        color: #511F91;
        border: none;
        border-radius: 8px;
        padding: 11px 20px;
        font-family: 'Sora', Helvetica, sans-serif;
        font-size: 18px;
        font-weight: 600;
        cursor: pointer;
        margin-bottom: 16px;
        transition: background .15s;
    }
    .filter-toggle:hover { background: #F0E6FF; }
    #filter-icon {
        width: 8px;
        height: 8px;
        border-right: 2px solid #511F91;
        border-bottom: 2px solid #511F91;
        transform: rotate(45deg);
        margin-top: -4px;
        transition: transform 0.2s ease;
    }
    .filter-toggle.active #filter-icon {
        transform: rotate(225deg);
        margin-top: 4px;
    }

    /* Panel filter */
    .filter-panel {
        display: none;
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
    }
    .filter-panel.active { display: block; }
    .filter-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 18px;
    }
    .filter-group { display: flex; flex-direction: column; gap: 8px; }
    .filter-group label {
        font-family: 'Sora', Helvetica, sans-serif;
        font-size: 14px;
        font-weight: 600;
        color: #511F91;
    }
    .filter-group select,
    .filter-group input {
        width: 100%;
        box-sizing: border-box;
        padding: 10px 12px;
        border: 1px solid #d8c5f5;
        border-radius: 8px;
        background: #FBF7FF;
        font-family: 'Sora', Helvetica, sans-serif;
        font-size: 14px;
        outline: none;
    }
    .filter-group select:focus,
    .filter-group input:focus { border-color: #511F91; }
    .capacity-group { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    .filter-actions { display: flex; gap: 10px; margin-top: 20px; }
    .apply-filter {
        background: #511F91;
        color: #fff;
        border: none;
        padding: 11px 22px;
        border-radius: 8px;
        font-family: 'Sora', Helvetica, sans-serif;
        font-weight: 600;
        cursor: pointer;
    }
    .apply-filter:hover { background: #3e1770; }
    .reset-filter {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 11px 22px;
        border-radius: 8px;
        background: #F0E6FF;
        color: #511F91;
        text-decoration: none;
        font-family: 'Sora', Helvetica, sans-serif;
        font-weight: 600;
    }

    /* Kartu fasilitas */
    .facility-card {
        display: flex;
        gap: 28px;
        align-items: center;
        background: #fff;
        border-radius: 8px;
        padding: 24px 32px;
        margin-bottom: 20px;
    }
    .facility-card:last-child { margin-bottom: 0; }
    .facility-card__photo {
        width: 170px;
        height: 150px;
        background: #D9D9D9;
        border-radius: 5px;
        flex-shrink: 0;
        object-fit: cover;
    }
    .facility-card__info { flex: 1; min-width: 0; }
    .facility-card__name {
        font-family: 'Sora', Helvetica, sans-serif;
        font-weight: 700;
        font-size: 25px;
        color: #511F91;
        margin: 0 0 12px;
    }
    .facility-card__details {
        display: grid;
        grid-template-columns: 160px 16px 1fr;
        row-gap: 6px;
        font-family: 'Sora', Helvetica, sans-serif;
        font-weight: 600;
        font-size: 20px;
        color: #511F91;
    }
    .facility-card__arrow {
        color: #511F91;
        opacity: .6;
        font-size: 28px;
        text-decoration: none;
        flex-shrink: 0;
    }

    .empty-note { color: #4a3868; font-size: 18px; }

    @media (max-width: 900px) {
        .filter-grid { grid-template-columns: 1fr; }
        .facility-card { flex-wrap: wrap; }
        .facility-card__details { grid-template-columns: 130px 16px 1fr; font-size: 16px; }
        .facility-card__name { font-size: 20px; }
    }
</style>
@endpush

@section('content')
    <div class="group-header">
        <a href="{{ route('pengguna.dashboard') }}" class="group-header__back" aria-label="Kembali ke dashboard">&lsaquo;</a>
        <h1>{{ $faculty }}</h1>
    </div>

    <div class="group-body">
        <button type="button" class="filter-toggle" onclick="toggleFilter()">
            <span>Filter</span>
            <span id="filter-icon"></span>
        </button>

        <div id="filter-panel" class="filter-panel">
            <form method="GET" action="{{ url()->current() }}">
                <div class="filter-grid">
                    <div class="filter-group">
                        <label for="type_id">Tipe</label>
                        <select name="type_id" id="type_id">
                            <option value="">Semua tipe</option>
                            @foreach ($types as $type)
                                <option value="{{ $type->id }}" @selected((string) $typeId === (string) $type->id)>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Kapasitas</label>
                        <div class="capacity-group">
                            <input type="number" name="capacity_min" placeholder="Minimal" min="1" value="{{ $capacityMin }}">
                            <input type="number" name="capacity_max" placeholder="Maksimal" min="1" value="{{ $capacityMax }}">
                        </div>
                    </div>

                    <div class="filter-group">
                        <label for="status">Status</label>
                        <select name="status" id="status">
                            <option value="">Semua status</option>
                            <option value="aktif" @selected($status === 'aktif')>Aktif</option>
                            <option value="dalam perbaikan" @selected($status === 'dalam perbaikan')>Dalam Perbaikan</option>
                        </select>
                    </div>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="apply-filter">Terapkan Filter</button>
                    <a href="{{ url()->current() }}" class="reset-filter">Reset</a>
                </div>
            </form>
        </div>

        @forelse ($facilities as $facility)
            <x-facility-card :facility="$facility" />
        @empty
            <p class="empty-note">Belum ada fasilitas di {{ $faculty }}.</p>
        @endforelse

        @if ($facilities instanceof \Illuminate\Pagination\LengthAwarePaginator)
            {{ $facilities->links() }}
        @endif
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
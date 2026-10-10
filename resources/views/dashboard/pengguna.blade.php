@extends('layouts.dashboard')

@section('title', 'Dashboard Saya')

@push('styles')
<style>
    .pengguna-title {
        font-family: 'Sora', Helvetica, sans-serif;
        font-weight: 700;
        font-size: 46px;
        color: #501e91;
        margin: 0 0 8px;
    }

    .dashboard-summary-row {
        display: flex;
        align-items: flex-start;
        gap: 24px;
    }
    
    .dashboard-section-title {
        font-family: 'Sora', Helvetica, sans-serif;
        font-weight: 700;
        font-size: 32px;
        color: #511F91;
        margin: 32px 0 16px;
    }

    .facility-groups-wrap {
        background: rgba(213, 187, 251, 0.6);
        border-radius: 8px;
        padding: 24px;
    }

    .facility-list {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .facility-group-card {
        display: flex;
        align-items: center;
        gap: 28px;
        background: #fff;
        border-radius: 7px;
        padding: 20px 32px;
        text-decoration: none;
        transition: box-shadow .15s;
    }
    .facility-group-card:hover { box-shadow: 0 4px 14px rgba(38,15,69,.12); }

    .facility-group-card__thumb {
        width: 160px;
        height: 120px;
        background: #D9D9D9;
        border-radius: 5px;
        flex-shrink: 0;
        object-fit: cover;
    }

    .facility-group-card__body { flex: 1; min-width: 0; }
    .facility-group-card__name {
        font-family: 'Sora', Helvetica, sans-serif;
        font-weight: 700;
        font-size: 24px;
        color: #000;
        margin: 0 0 6px;
    }
    .facility-group-card__meta {
        font-family: 'Sora', Helvetica, sans-serif;
        font-weight: 400;
        font-size: 18px;
        color: #000;
        margin: 0;
    }

    .facility-group-card__arrow {
        font-size: 32px;
        color: #63220E;
        flex-shrink: 0;
        line-height: 1;
    }

    .empty-note { color: #6b5a87; font-size: 14px; }

    .tbl-pager {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 12px;
        margin-top: 20px;
    }
    .tbl-pager button {
        padding: 8px 22px;
        border: 1px solid #bd93f8;
        border-radius: 8px;
        background: #fff;
        color: #501e91;
        font-family: 'Sora', Helvetica, sans-serif;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
    }
    .tbl-pager button:hover { background: #eee7f7; }
    .tbl-pager button:disabled { opacity: .4; cursor: not-allowed; }
    .tbl-pager-info { font-size: 13px; color: #5b4a78; }
</style>
@endpush

@section('content')
    <h1 class="pengguna-title">Dashboard Saya</h1>

    <div class="dashboard-summary-row">
        {{-- Reservasi (Orang 3) --}}
        <x-dashboard.reservation-summary />

        {{-- Laporan (Orang 4) --}}
        <x-dashboard.report-summary
            :reports="$reports"
            :new-reports="$newReports"
            :processing-reports="$processingReports"
            :total-reports="$totalReports"/>
    </div>

    <h2 class="dashboard-section-title">Semua Fasilitas</h2>

    @if ($facilityGroups->isNotEmpty())
        <div class="facility-groups-wrap">
            <div class="facility-list" id="facilityList">
                @foreach ($facilityGroups as $i => $group)
                    <a href="{{ $group->url }}" class="facility-group-card fg-item" data-index="{{ $i }}" style="{{ $i >= 5 ? 'display:none;' : '' }}">
                        @if ($group->thumbnail)
                            <img src="{{ asset('storage/'.$group->thumbnail) }}"
                                 alt="{{ $group->name }}" class="facility-group-card__thumb">
                        @else
                            <div class="facility-group-card__thumb"></div>
                        @endif

                        <div class="facility-group-card__body">
                            <p class="facility-group-card__name">{{ $group->name }}</p>
                            <p class="facility-group-card__meta">{{ $group->available }}/{{ $group->total }} Fasilitas Tersedia</p>
                        </div>

                        <span class="facility-group-card__arrow" aria-hidden="true">&rsaquo;</span>
                    </a>
                @endforeach
            </div>

            @if ($facilityGroups->count() > 5)
                <div class="tbl-pager">
                    <button type="button" id="fgPrev" disabled>&larr; Sebelumnya</button>
                    <span class="tbl-pager-info" id="fgInfo"></span>
                    <button type="button" id="fgNext">Berikutnya &rarr;</button>
                </div>
            @endif
        </div>
    @else
        <p class="empty-note">Belum ada data fasilitas.</p>
    @endif

    <script>
    (function () {
        var items = document.querySelectorAll('.fg-item');
        if (items.length <= 5) return;
        var perPage = 5, page = 0, totalPages = Math.ceil(items.length / perPage);
        var prev = document.getElementById('fgPrev');
        var next = document.getElementById('fgNext');
        var info = document.getElementById('fgInfo');

        function render() {
            var start = page * perPage, end = start + perPage;
            items.forEach(function (el, i) {
                el.style.display = (i >= start && i < end) ? '' : 'none';
            });
            prev.disabled = page === 0;
            next.disabled = page >= totalPages - 1;
            info.textContent = (page + 1) + ' / ' + totalPages;
        }
        prev.addEventListener('click', function () { if (page > 0) { page--; render(); } });
        next.addEventListener('click', function () { if (page < totalPages - 1) { page++; render(); } });
        render();
    })();
    </script>
@endsection

{{-- resources/views/dashboard/pengguna.blade.php --}}
@extends('layouts.dashboard')

@section('title', 'Dashboard Saya')

@push('styles')
<style>
    .dashboard-section-title {
        font-family: 'Sora', Helvetica, sans-serif;
        font-weight: 700;
        font-size: 32px;
        color: #511F91;
        margin: 32px 0 16px;
    }

    .facility-groups-wrap {
        background: #D5BBFB;
        border-radius: 8px;
        padding: 24px;
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
</style>
@endpush

@section('content')
    <h1>Dashboard Saya</h1>
    <p>Ini shell dashboard pengguna. Isi riwayat reservasi & laporan milik user login diisi Orang 3 & Orang 4.</p>

    {{-- Reservasi (Orang 3) --}}
    <x-dashboard.reservation-summary />

    {{-- Laporan (Orang 4) --}}
    <x-dashboard.report-summary
        :reports="$reports"
        :new-reports="$newReports"
        :processing-reports="$processingReports"
        :total-reports="$totalReports"
    />

    <h2 class="dashboard-section-title">Semua Fasilitas</h2>

    @if ($facilityGroups->isNotEmpty())
        <div class="facility-groups-wrap">
            @foreach ($facilityGroups as $group)
                <a href="{{ $group->url }}" class="facility-group-card">
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
    @else
        <p class="empty-note">Belum ada data fasilitas.</p>
    @endif
@endsection
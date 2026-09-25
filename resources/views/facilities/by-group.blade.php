@extends('layouts.dashboard')

@section('title', $groupName)

@push('styles')
<style>
    .group-header {
        background: #fff;
        border-radius: 8px;
        padding: 28px 40px;
        margin-bottom: 24px;
    }
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

    .group-filter {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: #fff;
        border-radius: 5px;
        padding: 10px 20px;
        font-family: 'Sora', Helvetica, sans-serif;
        font-weight: 600;
        font-size: 20px;
        color: #511F91;
        margin-bottom: 24px;
        border: none;
        cursor: pointer;
    }
    .group-filter svg { flex-shrink: 0; }
    .group-filter__chevron { margin-left: 4px; opacity: .6; }

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
        .facility-card { flex-wrap: wrap; }
        .facility-card__details { grid-template-columns: 130px 16px 1fr; font-size: 16px; }
        .facility-card__name { font-size: 20px; }
    }
</style>
@endpush

@section('content')
    <div class="group-header">
        <h1>{{ $groupName }}</h1>
    </div>

    <div class="group-body">
        <button type="button" class="group-filter">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M3 5h18M6 12h12M10 19h4" stroke="#511F91" stroke-width="2" stroke-linecap="round"/>
            </svg>
            Filter
            <svg class="group-filter__chevron" width="14" height="8" viewBox="0 0 14 8" fill="none" aria-hidden="true">
                <path d="M1 1l6 6 6-6" stroke="#511F91" stroke-width="1.5"/>
            </svg>
        </button>

        @forelse ($facilities as $facility)
            <x-facility-card :facility="$facility" />
        @empty
            <p class="empty-note">Belum ada fasilitas di {{ $groupName }}.</p>
        @endforelse

        @if ($facilities instanceof \Illuminate\Pagination\LengthAwarePaginator)
            {{ $facilities->links() }}
        @endif
    </div>
@endsection
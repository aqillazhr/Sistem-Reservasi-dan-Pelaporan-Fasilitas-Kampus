@extends('layouts.dashboard')

@section('title', $groupName)

@push('styles')
<style>
    .group-header {
        background: #fff;
        border-radius: 8px;
        padding: 20px 32px;
        margin-bottom: 24px;
    }
    .group-header h1 {
        font-family: 'Sora', Helvetica, sans-serif;
        font-weight: 700;
        font-size: 32px;
        color: #511F91;
        margin: 0;
    }

    .group-body {
        background: #D5BBFB;
        border-radius: 8px;
        padding: 24px;
    }

    .group-filter {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #fff;
        border-radius: 5px;
        padding: 10px 18px;
        font-family: 'Sora', Helvetica, sans-serif;
        font-weight: 600;
        font-size: 18px;
        color: #511F91;
        margin-bottom: 20px;
    }

    .facility-card {
        display: flex;
        gap: 20px;
        align-items: center;
        background: #fff;
        border-radius: 8px;
        padding: 18px 20px;
        margin-bottom: 16px;
    }
    .facility-card:last-child { margin-bottom: 0; }
    .facility-card__photo {
        width: 140px;
        height: 120px;
        background: #D9D9D9;
        border-radius: 5px;
        flex-shrink: 0;
        object-fit: cover;
    }
    .facility-card__info { flex: 1; min-width: 0; }
    .facility-card__name {
        font-family: 'Sora', Helvetica, sans-serif;
        font-weight: 700;
        font-size: 20px;
        color: #511F91;
        margin: 0 0 10px;
    }
    .facility-card__details {
        display: grid;
        grid-template-columns: 120px 12px 1fr;
        row-gap: 4px;
        font-family: 'Sora', Helvetica, sans-serif;
        font-weight: 600;
        font-size: 15px;
        color: #511F91;
    }
    .facility-card__arrow {
        color: #511F91;
        opacity: .6;
        font-size: 24px;
        text-decoration: none;
        flex-shrink: 0;
    }

    .empty-note { color: #4a3868; }
</style>
@endpush

@section('content')
    <div class="group-header">
        <h1>{{ $groupName }}</h1>
    </div>

    <div class="group-body">
        <div class="group-filter">
            <span aria-hidden="true">&#9662;</span> Filter
        </div>

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
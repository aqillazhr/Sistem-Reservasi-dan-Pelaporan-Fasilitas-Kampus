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

    .group-filter-form {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 24px;
    }
    .group-filter-select {
        background: #fff;
        border: none;
        border-radius: 5px;
        padding: 11px 18px;
        font-family: 'Sora', Helvetica, sans-serif;
        font-weight: 600;
        font-size: 18px;
        color: #511F91;
        min-width: 200px;
    }
    .group-filter-submit {
        background: #511F91;
        color: #fff;
        border: none;
        border-radius: 5px;
        padding: 11px 24px;
        font-family: 'Sora', Helvetica, sans-serif;
        font-weight: 600;
        font-size: 18px;
        cursor: pointer;
    }
    .group-filter-submit:hover { background: #3e1770; }
    .group-filter-reset {
        font-family: 'Sora', Helvetica, sans-serif;
        font-weight: 600;
        font-size: 16px;
        color: #511F91;
        text-decoration: underline;
    }

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
        <a href="{{ route('pengguna.dashboard') }}" class="group-header__back" aria-label="Kembali ke dashboard">&lsaquo;</a>
        <h1>{{ $faculty }}</h1>
    </div>

    <div class="group-body">
        <form method="GET" class="group-filter-form">
            <select name="type_id" class="group-filter-select">
                <option value="">Semua Tipe</option>
                @foreach ($types as $type)
                    <option value="{{ $type->id }}" @selected(request('type_id') == $type->id)>
                        {{ $type->name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="group-filter-submit">Filter</button>
            @if (request('type_id'))
                <a href="{{ route('facilities.by-faculty', $faculty) }}" class="group-filter-reset">Reset</a>
            @endif
        </form>

        @forelse ($facilities as $facility)
            <x-facility-card :facility="$facility" />
        @empty
            <p class="empty-note">Belum ada fasilitas di {{ $faculty }}.</p>
        @endforelse

        @if ($facilities instanceof \Illuminate\Pagination\LengthAwarePaginator)
            {{ $facilities->links() }}
        @endif
    </div>
@endsection
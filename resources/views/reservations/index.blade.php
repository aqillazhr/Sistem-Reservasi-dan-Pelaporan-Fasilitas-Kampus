@extends('layouts.dashboard')

@section('title', 'Reservasi Saya')

@section('content')
    @include('reservations._styles')
    <div class="rsv">
        <h1>Reservasi saya</h1>
        <p class="sub"><a href="{{ route('pengguna.reservations.create') }}">Ajukan reservasi baru</a></p>

        <nav class="tabs" aria-label="Filter status">
            <a href="{{ route('pengguna.reservations.index') }}" @class(['on' => $tab === null])>Semua</a>
            @foreach ($tabs as $t)
                <a href="{{ route('pengguna.reservations.index', ['status' => $t]) }}" @class(['on' => $tab === $t])>{{ ucfirst($t) }}</a>
            @endforeach
        </nav>

        <div class="list">
            @forelse ($reservations as $r)
                <a class="card item" href="{{ route('pengguna.reservations.show', $r) }}">
                    <div>
                        <strong>{{ $r->facility->name }}</strong>
                        <div class="muted">
                            {{ $r->reservation_date->locale('id')->isoFormat('dddd, D MMMM YYYY') }},
                            {{ $r->start_short }}–{{ $r->end_short }}
                        </div>
                    </div>
                    <span class="badge {{ $r->status }}">{{ $r->status_label }}</span>
                </a>
            @empty
                <div class="card">
                    <p>Belum ada reservasi{{ $tab ? ' dengan status ini' : '' }}.</p>
                    <a class="btn" href="{{ route('pengguna.reservations.create') }}">Ajukan reservasi</a>
                </div>
            @endforelse
        </div>

        <div class="pager">
            @if ($reservations->previousPageUrl())
                <a class="btn ghost" href="{{ $reservations->previousPageUrl() }}">Sebelumnya</a>
            @else <span></span> @endif
            @if ($reservations->nextPageUrl())
                <a class="btn ghost" href="{{ $reservations->nextPageUrl() }}">Berikutnya</a>
            @endif
        </div>
    </div>
@endsection

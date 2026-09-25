@extends('layouts.dashboard')

@section('title', 'Reservasi Saya')

@section('content')
    @include('reservations._styles')
    <div class="rsv">
        <h1>Reservasi saya</h1>
        <p class="sub"><a href="{{ route('pengguna.reservations.create') }}">Ajukan reservasi baru</a></p>

        <nav class="tabs" aria-label="Filter status" id="rsvTabs">
            <a href="#" class="on" data-tab="semua">Semua</a>
            <a href="#" data-tab="menunggu">Menunggu</a>
            <a href="#" data-tab="aktif">Aktif</a>
            <a href="#" data-tab="selesai">Selesai</a>
            <a href="#" data-tab="ditolak">Ditolak</a>
            <a href="#" data-tab="dibatalkan">Dibatalkan</a>
        </nav>

        <div class="list" id="rsvList">
            @forelse ($reservations as $r)
                @php
                    $tabKey = $r->isFinished() ? 'selesai'
                        : match($r->status) {
                            'pending'   => 'menunggu',
                            'approved'  => 'aktif',
                            'rejected'  => 'ditolak',
                            'cancelled' => 'dibatalkan',
                            default     => 'semua',
                        };
                @endphp
                <a class="card item" href="{{ route('pengguna.reservations.show', $r) }}" data-tab="{{ $tabKey }}">
                    <div>
                        <strong>{{ $r->facility->name }}</strong>
                        <div class="muted">
                            {{ $r->reservation_date->locale('id')->isoFormat('dddd, D MMMM YYYY') }},
                            {{ $r->start_short }}–{{ $r->end_short }}
                        </div>
                    </div>
                    <span class="badge {{ $r->isFinished() ? 'selesai' : $r->status }}">{{ $r->status_label }}</span>
                </a>
            @empty
                <div class="card" id="emptyCard">
                    <p>Belum ada reservasi.</p>
                    <a class="btn" href="{{ route('pengguna.reservations.create') }}">Ajukan reservasi</a>
                </div>
            @endforelse
        </div>

        {{-- Pesan kosong per tab (muncul via JS) --}}
        <div class="card" id="emptyTab" hidden>
            <p>Tidak ada reservasi di kategori ini.</p>
        </div>

        <div class="pager" id="rsvPager">
            @if ($reservations->previousPageUrl())
                <a class="btn ghost" href="{{ $reservations->previousPageUrl() }}">Sebelumnya</a>
            @else <span></span> @endif
            @if ($reservations->nextPageUrl())
                <a class="btn ghost" href="{{ $reservations->nextPageUrl() }}">Berikutnya</a>
            @endif
        </div>
    </div>

    <script>
    (function () {
        var tabs     = document.querySelectorAll('#rsvTabs a');
        var cards    = document.querySelectorAll('#rsvList .card');
        var emptyTab = document.getElementById('emptyTab');

        function filter(tab) {
            var visible = 0;
            cards.forEach(function (c) {
                var show = tab === 'semua' || c.dataset.tab === tab;
                c.style.display = show ? '' : 'none';
                if (show) visible++;
            });
            emptyTab.hidden = visible > 0;
            tabs.forEach(function (t) {
                t.classList.toggle('on', t.dataset.tab === tab);
            });
            // Update URL tanpa reload
            var url = new URL(window.location);
            if (tab === 'semua') url.searchParams.delete('status');
            else url.searchParams.set('status', tab);
            history.replaceState(null, '', url);
        }

        tabs.forEach(function (t) {
            t.addEventListener('click', function (e) {
                e.preventDefault();
                filter(t.dataset.tab);
            });
        });

        // Restore dari URL param saat halaman dibuka
        var initTab = new URL(window.location).searchParams.get('status') || 'semua';
        filter(initTab);
    })();
    </script>
@endsection

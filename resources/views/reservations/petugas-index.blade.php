@extends('layouts.dashboard')

@section('title', 'Kelola Reservasi')

@section('content')
    @php
        $tabs = ['menunggu' => 'Menunggu', 'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak', 'dibatalkan' => 'Dibatalkan'];
        $label = fn ($t) => str_replace(':', '.', $t);
    @endphp
    <style>
        .kr { font-family: 'Sora', Helvetica, sans-serif; color: #000; }
        .kr h1 { color: #000; font-size: 40px; font-weight: 700; margin: 0 0 20px; }
        .kr .search-bar {
            display: flex; align-items: center; gap: 10px; background: #fff; border: 1px solid rgba(0,0,0,.5);
            border-radius: 7px; padding: 12px 18px; margin-bottom: 18px; max-width: 460px;
        }
        .kr .search-bar input { border: 0; outline: 0; font: inherit; font-size: 18px; flex: 1; color: #000; }
        .kr .search-bar input::placeholder { color: rgba(0,0,0,.5); }

        .kr .tabs { display: flex; gap: 8px; margin-bottom: 16px; flex-wrap: wrap; }
        .kr .tabs a {
            padding: 7px 18px; border-radius: 999px; background: #FBF7FF; color: #270F45;
            text-decoration: none; font-size: 14px; font-weight: 600; border: 1px solid #BD93F8;
        }
        .kr .tabs a.on { background: #9747FF; color: #fff; border-color: #9747FF; }

        .kr table { width: 100%; border-collapse: collapse; background: #fff; }
        .kr thead th {
            background: #D5BBFB; color: #000; font-size: 16px; font-weight: 700; text-align: left;
            padding: 14px 16px; border-right: 2px solid #fff;
        }
        .kr thead th:last-child { border-right: none; }
        .kr tbody td { 
            padding: 14px 16px; border-bottom: 1px solid #D5BBFB; border-right: 1px solid #D5BBFB; 
            vertical-align: top; font-size: 14px; 
        }
        .kr tbody td:last-child { border-right: none; }
        .kr tbody tr:last-child td { border-bottom: none; }
        .kr .name { font-weight: 700; font-size: 14px; color: #000; }
        .kr .muted { color: #525151; font-size: 12px; margin-top: 2px; }
        .kr .badge { display: inline-block; padding: 4px 12px; border-radius: 5px; font-size: 12px; font-weight: 600; }
        .kr .badge.pending { background: #FFDEA5; color: #000; }
        .kr .badge.approved { background: #C3FFC3; color: #000; }
        .kr .badge.rejected, .kr .badge.cancelled { background: #f3d1d1; color: #7a271a; }
        .kr .actions { display: flex; gap: 8px; flex-wrap: wrap; }
        .kr .actions form { margin: 0; }
        .kr .actions button {
            padding: 6px 14px; border-radius: 5px; font: inherit; font-size: 12px; font-weight: 700;
            cursor: pointer;
        }
        .kr .actions .btn-approve { background: #F4EBFF; color: #270F45; border: 1px solid #9747FF; }
        .kr .actions .btn-reject { background: #fff; color: #270F45; border: 1px solid #9747FF; }
        .kr .actions .btn-cancel { background: #f3d1d1; color: #7a271a; border: none; }
        .kr .empty { padding: 30px 16px; text-align: center; color: rgba(0,0,0,.5); }
        .kr .pager { display: flex; justify-content: space-between; margin-top: 16px; }
        .kr .pager a { color: #9747FF; font-weight: 600; text-decoration: none; }
    </style>

    <div class="kr">
        <h1>Kelola Reservasi</h1>

        <form method="GET" action="{{ route('petugas.reservations.index') }}" class="search-bar">
            <input type="hidden" name="status" value="{{ $tab }}">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="rgba(0,0,0,.5)" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="q" value="{{ $q }}" placeholder="Cari pemohon atau fasilitas" onchange="this.form.submit()">
        </form>

        <nav class="tabs">
            @foreach ($tabs as $key => $tabLabel)
                <a href="{{ route('petugas.reservations.index', ['status' => $key, 'q' => $q ?: null]) }}"
                   @class(['on' => $tab === $key])>{{ $tabLabel }}</a>
            @endforeach
        </nav>

        <div id="tableContainer">
            @include('reservations.partials.petugas-table')
        </div>
    </div>

    <script>
    (function () {
        var container = document.getElementById('tableContainer');
        var tabs = document.querySelectorAll('.kr .tabs a');
        var currentController = null;

        tabs.forEach(function (t) {
            t.addEventListener('click', function (e) {
                e.preventDefault();
                var url = this.getAttribute('href');

                // Update active tab style
                tabs.forEach(function (tab) { tab.classList.remove('on'); });
                this.classList.add('on');

                // Cancel previous request if any
                if (currentController) currentController.abort();
                currentController = new AbortController();

                // Fetch new table HTML
                fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    signal: currentController.signal
                })
                .then(res => res.text())
                .then(html => {
                    container.innerHTML = html;
                    window.history.replaceState(null, '', url);
                })
                .catch(err => {
                    if (err.name !== 'AbortError') console.error('Failed to fetch tabs', err);
                });
            });
        });

        // Also intercept pagination clicks
        container.addEventListener('click', function (e) {
            if (e.target.tagName === 'A' && e.target.closest('.pager')) {
                e.preventDefault();
                var url = e.target.getAttribute('href');

                if (currentController) currentController.abort();
                currentController = new AbortController();

                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, signal: currentController.signal })
                .then(res => res.text())
                .then(html => {
                    container.innerHTML = html;
                    window.history.replaceState(null, '', url);
                });
            }
        });
    })();
    </script>

@endsection

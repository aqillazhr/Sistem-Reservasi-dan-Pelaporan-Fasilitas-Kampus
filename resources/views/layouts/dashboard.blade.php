<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Sistem Reservasi & Pelaporan Fasilitas Kampus')</title>

    {{-- Google Fonts: Sora + Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;700&family=Sora:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        /* ── Reset & base ── */
        *, *::before, *::after { box-sizing: border-box; }
        html, body { margin: 0; min-height: 100%; }
        a { text-decoration: none; }
        button:focus-visible { outline: 2px solid #4a90e2 !important; }

        body {
            font-family: 'Sora', Helvetica, sans-serif;
            background-color: #fbf7ff;
            color: #260f45;
        }

        /* ── Navbar ── */
        .navbar {
            width: 100%;
            height: 117px;
            background-color: #260f45;
            display: flex;
            align-items: center;
            padding: 0 20px;
            gap: 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        /* Logo */
        .navbar .brand {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 119px;
            flex-shrink: 0;
            margin-top: 15px;
            align-self: flex-start;
        }
        .navbar .brand-icon {
            width: 64px;
            height: 63px;
            background: linear-gradient(180deg, #9747ff 0%, #bd93f8 100%);
            border-radius: 4px;
        }
        .navbar .brand-name {
            font-family: 'Inter', Helvetica, sans-serif;
            font-weight: 700;
            color: #ffffff;
            font-size: 16px;
            margin-top: 6px;
            white-space: nowrap;
        }

        /* Nav links */
        .navbar .nav-links {
            display: flex;
            align-items: center;
            gap: 0;
            margin-left: 54px;
        }
        .navbar .nav-links a {
            display: flex;
            align-items: center;
            gap: 7px;
            font-family: 'Sora', Helvetica, sans-serif;
            font-weight: 600;
            color: #ffffff;
            font-size: 18px;
            margin-right: 40px;
            white-space: nowrap;
            opacity: 0.85;
            transition: opacity .15s;
        }
        .navbar .nav-links a:hover,
        .navbar .nav-links a.active { opacity: 1; }
        .navbar .nav-links a svg { flex-shrink: 0; width: 20px; height: 20px; }

        /* Search bar */
        .navbar .search-wrap {
            margin-left: auto;
            position: relative;
            width: 294px;
            height: 43px;
            flex-shrink: 0;
        }
        .navbar .search-wrap input {
            width: 100%;
            height: 100%;
            background: #ffffff;
            border: 2px solid #bd93f8;
            border-radius: 8px;
            padding: 0 12px 0 44px;
            font-family: 'Sora', Helvetica, sans-serif;
            font-weight: 300;
            font-size: 16px;
            color: rgba(0,0,0,.5);
        }
        .navbar .search-wrap input::placeholder { color: rgba(0,0,0,.5); }
        .navbar .search-wrap svg {
            position: absolute;
            top: 50%;
            left: 14px;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
        }

        /* Avatar + dropdown */
        .navbar .avatar-wrap {
            margin-left: 28px;
            position: relative;
            flex-shrink: 0;
        }
        .navbar .avatar {
            width: 68px;
            height: 68px;
            border-radius: 50%;
            background-color: #bd93f8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', Helvetica, sans-serif;
            font-weight: 500;
            color: #ffffff;
            font-size: 24px;
            cursor: pointer;
            border: none;
            transition: opacity .15s;
        }
        .navbar .avatar:hover { opacity: .85; }
        .navbar .avatar-dropdown {
            display: none;
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            background: #fff;
            border: 1px solid #d5bbfb;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(38,15,69,.18);
            min-width: 160px;
            z-index: 200;
            overflow: hidden;
        }
        .navbar .avatar-dropdown.open { display: block; }
        .navbar .avatar-dropdown a,
        .navbar .avatar-dropdown button {
            display: block;
            width: 100%;
            padding: 12px 18px;
            text-align: left;
            font-family: 'Sora', Helvetica, sans-serif;
            font-size: 15px;
            font-weight: 600;
            color: #260f45;
            background: none;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: background .12s;
        }
        .navbar .avatar-dropdown a:hover,
        .navbar .avatar-dropdown button:hover { background: #fbf7ff; }
        .navbar .avatar-dropdown .dd-logout { color: #b42318; }
        .navbar .avatar-name {
            font-family: 'Sora', Helvetica, sans-serif;
            font-size: 12px;
            color: rgba(255,255,255,.6);
            padding: 10px 18px 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 160px;
        }
        .navbar .dd-divider { border: none; border-top: 1px solid #ede5fb; margin: 4px 0; }

        /* Guest links (Pengunjung, belum login) */
        .navbar .guest-links {
            margin-left: 28px;
            display: flex;
            align-items: center;
            gap: 16px;
            flex-shrink: 0;
        }
        .navbar .guest-links a {
            font-family: 'Sora', Helvetica, sans-serif;
            font-weight: 600;
            font-size: 16px;
            color: #ffffff;
            white-space: nowrap;
        }
        .navbar .guest-links a.btn-daftar {
            background: #bd93f8;
            padding: 8px 16px;
            border-radius: 8px;
        }

        /* ── Content area ── */
        .page-content {
            padding: 32px 44px;
            min-height: calc(100vh - 117px);
        }

        /* ── Status flash ── */
        .flash-status {
            background: #d5bbfb;
            color: #260f45;
            font-weight: 600;
            padding: 10px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 15px;
        }
    </style>
    @stack('styles')

</head>
<body>

    <nav class="navbar">
        {{-- Logo --}}
        <div class="brand">
            <div class="brand-icon"></div>
            <span class="brand-name">SI Reservasi</span>
        </div>

        {{-- Nav links: HANYA muncul kalau ada yang login. Pengunjung (belum
             login) nggak dapat menu role sama sekali, karena auth()->user()
             pasti null buat mereka. --}}
        @auth
            @php $role = auth()->user()->role; @endphp

            <div class="nav-links">
                @if ($role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" @class(['active' => request()->routeIs('admin.dashboard')])>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        Dashboard
                    </a>
                    <a href="{{ route('facilities.index') }}" @class(['active' => request()->routeIs('facilities.*')])>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                        Fasilitas
                    </a>
                    <a href="{{ route('admin.accounts.index') }}" @class(['active' => request()->routeIs('admin.accounts.*')])>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        Pengguna
                    </a>
                @elseif ($role === 'petugas')
                    <a href="{{ route('petugas.dashboard') }}" @class(['active' => request()->routeIs('petugas.dashboard')])>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        Dashboard
                    </a>
                    <a href="#" @class(['active' => request()->routeIs('petugas.reservations.*')])>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        Reservasi
                    </a>
                    <a href="#" @class(['active' => request()->routeIs('petugas.reports.*')])>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        Laporan
                    </a>
                @else
                    <a href="{{ route('pengguna.dashboard') }}" @class(['active' => request()->routeIs('pengguna.dashboard')])>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        Dashboard
                    </a>
                    <a href="{{ route('pengguna.reservations.index') }}" @class(['active' => request()->routeIs('pengguna.reservations.*') || request()->routeIs('pengguna.reports.index')])>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        Riwayat
                    </a>
                    <a href="{{ route('pengguna.reports.create') }}" @class(['active' => request()->routeIs('pengguna.reports.*')])>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        Lapor Kerusakan
                    </a>
                @endif
            </div>
        @else
            {{-- Pengunjung tetap lihat menu Fasilitas (boleh diakses tanpa
                 login, sesuai User Story 1-2), cuma nggak dapat menu lain. --}}
            <div class="nav-links">
                <a href="{{ route('facilities.index') }}" @class(['active' => request()->routeIs('facilities.*')])>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    Fasilitas
                </a>
            </div>
        @endauth

        {{-- Search --}}
        <div class="search-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="#bd93f8" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" placeholder="Cari fasilitas"
                   onkeydown="if(event.key==='Enter'){ window.location='{{ route('facilities.index') }}?search='+encodeURIComponent(this.value); }">
        </div>

        {{-- Avatar + dropdown kalau login, tombol Login/Daftar kalau belum --}}
        @auth
            @php
                $initials = collect(explode(' ', auth()->user()->name))
                    ->take(2)->map(fn($w) => strtoupper($w[0] ?? ''))->implode('');
            @endphp
            <div class="avatar-wrap">
                <button class="avatar" id="avatarBtn" title="{{ auth()->user()->name }}" aria-haspopup="true" aria-expanded="false">
                    {{ $initials }}
                </button>
                <div class="avatar-dropdown" id="avatarDropdown" role="menu">
                    <div class="avatar-name">{{ auth()->user()->name }}</div>
                    <hr class="dd-divider">
                    <a href="{{ route('profile.show') }}" role="menuitem">Profil</a>
                    <hr class="dd-divider">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dd-logout" role="menuitem">Logout</button>
                    </form>
                </div>
            </div>
            <script>
                (function () {
                    var btn = document.getElementById('avatarBtn');
                    var dd  = document.getElementById('avatarDropdown');
                    btn.addEventListener('click', function (e) {
                        e.stopPropagation();
                        var open = dd.classList.toggle('open');
                        btn.setAttribute('aria-expanded', open);
                    });
                    document.addEventListener('click', function () {
                        dd.classList.remove('open');
                        btn.setAttribute('aria-expanded', 'false');
                    });
                })();
            </script>
        @else
            <div class="guest-links">
                <a href="{{ route('login') }}">Login</a>
                <a href="{{ route('register') }}" class="btn-daftar">Daftar</a>
            </div>
        @endauth
    </nav>

    <div class="page-content">
        @if (session('status'))
            <div class="flash-status">{{ session('status') }}</div>
        @endif

        @yield('content')
    </div>

</body>
</html>
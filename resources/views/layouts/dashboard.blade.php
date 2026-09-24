<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Sistem Reservasi & Pelaporan Fasilitas Kampus')</title>
    {{-- TODO: ganti/tambah CSS di sini sesuai styling Figma tim kamu --}}
    <style>
        * { box-sizing: border-box; }
        body { font-family: system-ui, sans-serif; margin: 0; background: #f4f5f7; }
        .navbar { height: 56px; background: #1f2937; color: #fff; display: flex; align-items: center; justify-content: space-between; padding: 0 20px; }
        .navbar .brand { font-weight: 700; }
        .navbar .profile-menu { display: flex; align-items: center; gap: 12px; font-size: 14px; }
        .navbar .profile-menu a { color: #fff; text-decoration: none; }
        .navbar .profile-menu form button { background: none; border: none; color: #fca5a5; cursor: pointer; font-size: 14px; }
        .layout { display: flex; min-height: calc(100vh - 56px); }
        .sidebar { width: 220px; background: #111827; color: #d1d5db; padding: 16px 0; }
        .sidebar a { display: block; padding: 10px 20px; color: #d1d5db; text-decoration: none; font-size: 14px; }
        .sidebar a:hover, .sidebar a.active { background: #1f2937; color: #fff; }
        .content { flex: 1; padding: 24px; }
        .status-text { color: #16a34a; margin-bottom: 16px; }
    </style>
</head>
<body>

    <div class="navbar">
        <div class="brand">SI Reservasi Fasilitas Kampus</div>
        <div class="profile-menu">
            <span>{{ auth()->user()->name }} ({{ auth()->user()->role }})</span>
            <a href="{{ route('profile.show') }}">Profil</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Logout</button>
            </form>
        </div>
    </div>

    <div class="layout">
        <div class="sidebar">
            {{-- Menu beda per role, sesuai dokumen pembagian tugas.
                 Link yang modulnya belum jadi (belum ada route index/history)
                 sementara diarahin ke '#', tinggal diganti pas modul itu jadi. --}}
            @if (auth()->user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <a href="{{ route('facilities.index') }}">Fasilitas</a>
                <a href="#">Reservasi</a> {{-- TODO: ganti ke route index reservasi (Orang 3) --}}
                <a href="#">Laporan</a>   {{-- TODO: ganti ke route index laporan (Orang 4) --}}
                <a href="{{ route('admin.accounts.index') }}">Pengguna</a>
                <a href="#">Rekap</a>     {{-- TODO: ganti ke route rekap/export (Orang 4) --}}
                <a href="{{ route('profile.show') }}">Profil</a>
            @elseif (auth()->user()->role === 'petugas')
                <a href="{{ route('petugas.dashboard') }}">Dashboard</a>
                <a href="#">Reservasi</a> {{-- TODO: ganti ke route antrian reservasi (Orang 3) --}}
                <a href="#">Laporan</a>   {{-- TODO: ganti ke route antrian laporan (Orang 4) --}}
                <a href="{{ route('profile.show') }}">Profil</a>
            @else
                <a href="{{ route('pengguna.dashboard') }}">Dashboard</a>
                <a href="{{ route('facilities.index') }}">Fasilitas</a>
                <a href="#">Reservasi Saya</a> {{-- TODO: ganti ke route riwayat reservasi (Orang 3) --}}
                <a href="#">Laporan Saya</a>   {{-- TODO: ganti ke route riwayat laporan (Orang 4) --}}
                <a href="{{ route('profile.show') }}">Profil</a>
            @endif
        </div>

        <div class="content">
            @if (session('status'))
                <div class="status-text">{{ session('status') }}</div>
            @endif

            @yield('content')
        </div>
    </div>

</body>
</html>
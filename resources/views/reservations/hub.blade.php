@extends('layouts.dashboard')

@section('title', 'Reservasi')

@section('content')
    @include('reservations._styles')
    <div class="rsv">
        <h1>Reservasi</h1>
        <p class="sub">Pilih yang mau kamu lakukan.</p>

        <div class="grid2">
            <a class="card" href="{{ route('pengguna.reservations.create') }}">
                <h2>Ajukan reservasi</h2>
                <p class="muted">Cari fasilitas, pilih tanggal dan slot waktu, lalu kirim ke petugas.</p>
            </a>
            <a class="card" href="{{ route('pengguna.reservations.index') }}">
                <h2>Reservasi saya</h2>
                <p class="muted">
                    {{ $counts['pending'] ?? 0 }} menunggu ·
                    {{ $counts['aktif'] ?? 0 }} aktif ·
                    {{ $counts['selesai'] ?? 0 }} selesai ·
                    {{ $counts['rejected'] ?? 0 }} ditolak ·
                    {{ $counts['cancelled'] ?? 0 }} dibatalkan
                </p>
            </a>
        </div>
    </div>
@endsection

{{-- resources/views/dashboard/petugas.blade.php --}}
@extends('layouts.dashboard')

@section('title', 'Dashboard Petugas')

@section('content')
    <style>
        .dp { font-family: 'Sora', Helvetica, sans-serif; }
        .dp h1 { font-size: 50px; font-weight: 700; color: #000; margin: 0 0 24px; }
        .dp-cards { display: flex; gap: 24px; flex-wrap: wrap; margin-bottom: 24px; }
        .dp-lists { display: flex; gap: 24px; flex-wrap: wrap; align-items: flex-start; }
        .dp-placeholder {
            flex: 1; min-width: 271px; height: 145px; background: #fff; border: 2px dashed #BD93F8; border-radius: 8px;
            display: flex; align-items: center; justify-content: center; color: rgba(0,0,0,.4); font-size: 14px;
            text-align: center; padding: 12px;
        }
        .dp-list-placeholder {
            flex: 1; min-width: 465px; min-height: 200px; background: #fff; border: 1px dashed #BD93F8; border-radius: 10px;
            display: flex; align-items: center; justify-content: center; color: rgba(0,0,0,.4); font-size: 14px;
            text-align: center; padding: 20px;
        }
    </style>

    <div class="dp">
        <h1>Dashboard Petugas</h1>

        <div class="dp-cards">
            {{-- Reservasi (Orang 3) --}}
            <x-dashboard.petugas-reservation-queue-card />

            {{--
                TODO (Orang 4): dua kartu di Desktop 5 ini ("Laporan Baru",
                "Dalam Perbaikan") sebaiknya dibuat sebagai component sendiri
                juga, mengikuti pola <x-dashboard.petugas-reservation-queue-card />
                di atas — supaya tidak perlu menyentuh dashboard/petugas.blade.php
                ini lagi, cukup tambah <x-dashboard.xxx /> di sini.
            --}}
            <div class="dp-placeholder">Kartu "Laporan Baru" — punya Orang 4</div>
            <div class="dp-placeholder">Kartu "Dalam Perbaikan" — punya Orang 4</div>
        </div>

        <div class="dp-lists">
            {{-- Antrian Reservasi (Orang 3) --}}
            <x-dashboard.petugas-reservation-queue-list />

            {{-- TODO (Orang 4): ganti placeholder ini dengan component
                 "Antrian Laporan" (mengikuti pola yang sama). --}}
            <div class="dp-list-placeholder">"Antrian Laporan" — punya Orang 4</div>
        </div>
    </div>
@endsection

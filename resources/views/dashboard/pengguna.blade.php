{{-- resources/views/dashboard/pengguna.blade.php --}}
@extends('layouts.dashboard')

@section('title', 'Dashboard Saya')

@section('content')
    <h1>Dashboard Saya</h1>
    <p>Ini shell dashboard pengguna. Isi riwayat reservasi & laporan milik user login diisi Orang 3 & Orang 4.</p>
    {{-- Reservasi (Orang 3) --}}
    <x-dashboard.reservation-summary />
    {{-- Laporan (Orang 4) --}}
    <x-dashboard.report-summary
        :reports="$reports"
        :new-reports="$newReports"
        :processing-reports="$processingReports"
        :total-reports="$totalReports"
    />
@endsection
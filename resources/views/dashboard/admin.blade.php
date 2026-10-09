@extends('layouts.dashboard')

@section('title', 'Dashboard Admin')

@section('content')

<style>
    .admin-dashboard {
        position: relative;
        overflow: hidden;
    }

    .admin-title {
        font-size: 46px;
        font-weight: 700;
        color: #501e91;
        margin: 0 0 8px;
    }

    .admin-subtitle {
        margin: 0 0 32px;
        color: #6f6280;
        font-size: 17px;
    }

    .admin-decoration-top {
        position: absolute;
        right: -20px;
        top: -35px;
        width: 210px;
        opacity: .75;
        pointer-events: none;
    }

    .admin-decoration-bottom {
        position: absolute;
        right: -30px;
        bottom: -90px;
        width: 250px;
        opacity: .65;
        pointer-events: none;
    }

    .admin-stat-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 18px;
        margin-bottom: 28px;
        position: relative;
        z-index: 1;
    }

    .admin-stat {
        background: #fff;
        border: 1px solid #9747ff;
        border-radius: 8px;
        padding: 22px 24px;
        min-height: 125px;
        text-align: center;
    }

    .admin-stat-label {
        font-size: 21px;
        color: #6f6280;
        margin-bottom: 8px;
    }

    .admin-stat-number {
        font-size: 36px;
        font-weight: 700;
        color: #260f45;
    }

    .admin-stat-note {
        margin-top: 7px;
        font-size: 13px;
        color: #8b7b99;
    }

    .admin-main-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
        position: relative;
        z-index: 1;
    }

    .admin-panel {
        background: #fff;
        border: 1px solid #9747ff;
        border-radius: 8px;
        padding: 24px;
    }

    .admin-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 15px;
    }

    .admin-panel-title {
        margin: 0;
        font-size: 21px;
        font-weight: 700;
        color: #260f45;
    }

    .admin-panel-period {
        color: #8b7b99;
        font-size: 12px;
    }

    .admin-registration-item {
        padding: 10px 0;
        border-bottom: 1px solid #ddd6e8;
    }

    .admin-registration-item:last-child {
        border-bottom: none;
    }

    .admin-registration-date {
        font-size: 10px;
        color: #8b7b99;
        margin-bottom: 2px;
    }

    .admin-registration-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .admin-registration-info {
        min-width: 0;
    }

    .admin-registration-name {
        font-size: 14px;
        font-weight: 700;
        color: #260f45;
    }

    .admin-registration-role {
        font-size: 12px;
        color: #6f6280;
        margin-top: 2px;
    }

    .admin-registration-email {
        font-size: 12px;
        color: #260f45;
        margin-top: 2px;
        word-break: break-word;
    }

    .admin-registration-actions {
        display: flex;
        gap: 8px;
        flex-shrink: 0;
    }

    .admin-approve,.admin-reject {
        width: 27px;
        height: 27px;
        border: none;
        border-radius: 5px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 15px;
        font-weight: 700;
    }

    .admin-approve {
        background: #b9f6c5;
        color: #16842b;
    }

    .admin-reject {
        background: #ff9b9b;
        color: #8f1111;
    }

    .admin-panel-footer {
        display: flex;
        justify-content: center;
        margin-top: 12px;
    }

    .admin-link {
        color: #260f45;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
    }

    .admin-link:hover {
        color: #9747ff;
    }

    .admin-recap-item {
        padding: 12px 0;
        border-bottom: 1px solid #ddd6e8;
    }

    .admin-recap-item:last-child {
        border-bottom: none;
    }

    .admin-recap-name {
        font-size: 15px;
        font-weight: 700;
        color: #260f45;
    }

    .admin-recap-description {
        font-size: 12px;
        color: #6f6280;
        margin-top: 3px;
    }

    .admin-recap-value {
        float: right;
        font-size: 11px;
        font-weight: 700;
        color: #260f45;
        padding: 3px 10px;
        border-radius: 5px;
        min-width: 48px;
        text-align: center;
    }

    .admin-recap-value.high {
        background: #bff3f7;
    }

    .admin-recap-value.low {
        background: #ffdca3;
    }

    .admin-recap-value.damage {
        background: #ff9999;
    }

    .admin-empty {
        padding: 30px 0;
        text-align: center;
        color: #8b7b99;
        font-size: 13px;
    }

    .admin-recap-link {
        display: inline-block;
        background: #bd93f8;
        color: #260f45;
        padding: 10px 18px;
        border-radius: 7px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        transition: .15s;
    }

    .admin-recap-link:hover {
        background: #9747ff;
        color: #fff;
    }

    @media (max-width: 900px) {
        .admin-stat-grid,
        .admin-main-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="admin-dashboard">

    <svg
        class="admin-decoration-top"
        viewBox="0 0 220 180"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
    >
        <path
            d="M215 10C160 30 130 65 140 95C150 125 190 120 210 100"
            stroke="#9747ff"
            stroke-width="1.5"
            opacity=".45"
        />
        <path
            d="M215 25C170 40 145 65 152 88C160 112 190 110 215 92"
            stroke="#9747ff"
            stroke-width="1.5"
            opacity=".35"
        />
        <path
            d="M215 42C182 52 164 69 169 84C175 100 195 98 215 86"
            stroke="#9747ff"
            stroke-width="1.5"
            opacity=".28"
        />
    </svg>

    <svg
        class="admin-decoration-bottom"
        viewBox="0 0 250 210"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
    >
        <path
            d="M245 20C180 40 155 75 165 110C175 145 220 145 245 120"
            stroke="#9747ff"
            stroke-width="2"
            opacity=".45"
        />
        <path
            d="M245 45C195 60 177 87 184 108C192 130 220 130 245 112"
            stroke="#9747ff"
            stroke-width="2"
            opacity=".35"
        />
        <path
            d="M245 70C212 80 200 95 205 108C210 120 228 120 245 108"
            stroke="#9747ff"
            stroke-width="2"
            opacity=".25"
        />
    </svg>

    <h1 class="admin-title">
        Dashboard Admin
    </h1>

    <p class="admin-subtitle">
        Ringkasan fasilitas, akun, dan aktivitas sistem.
    </p>

    {{-- STATISTIK --}}

    <div class="admin-stat-grid">

        <div class="admin-stat">
            <div class="admin-stat-label">
                Total Fasilitas
            </div>

            <div class="admin-stat-number">
                {{ $totalFacilities }}
            </div>

            <div class="admin-stat-note">
                Data fasilitas kampus
            </div>
        </div>

        <div class="admin-stat">
            <div class="admin-stat-label">
                Total Akun
            </div>

            <div class="admin-stat-number">
                {{ $totalAccounts }}
            </div>

            <div class="admin-stat-note">
                Pengguna, petugas, dan admin
            </div>
        </div>

        <div class="admin-stat">
            <div class="admin-stat-label">
                Permohonan ACC
            </div>

            <div class="admin-stat-number">
                {{ $pendingAccounts }}
            </div>

            <div class="admin-stat-note">
                Menunggu verifikasi
            </div>
        </div>

    </div>


    {{-- ISI DASHBOARD --}}

    <div class="admin-main-grid">

        {{-- PERMOHONAN REGISTRASI --}}

        <div class="admin-panel">

            <div class="admin-panel-header">
                <h2 class="admin-panel-title">
                    Permohonan Registrasi
                </h2>
            </div>

            @forelse ($pendingRegistrations as $user)

                <div class="admin-registration-item">

                    <div class="admin-registration-date">
                        {{ $user->created_at?->locale('id')->isoFormat('D MMMM YYYY') }}
                    </div>

                    <div class="admin-registration-content">

                        <div class="admin-registration-info">

                            <div class="admin-registration-name">
                                {{ $user->name }}
                            </div>

                            <div class="admin-registration-role">
                                {{ ucfirst($user->role) }}
                            </div>

                            <div class="admin-registration-email">
                                {{ $user->email }}
                            </div>

                        </div>

                        <div class="admin-registration-actions">

                            <form
                                method="POST"
                                action="{{ route('admin.accounts.verify', $user) }}"
                                class="form-confirm"
                                data-confirm-title="Konfirmasi verifikasi"
                                data-confirm-msg="Apakah Anda yakin ingin memverifikasi akun ini?"
                            >
                                @csrf

                                <button
                                    type="submit"
                                    class="admin-approve"
                                    title="Verifikasi"
                                >
                                    ✓
                                </button>
                            </form>

                            <form
                                method="POST"
                                action="{{ route('admin.accounts.reject', $user) }}"
                                class="form-confirm"
                                data-confirm-title="Konfirmasi penolakan"
                                data-confirm-msg="Apakah Anda yakin ingin menolak akun ini?"
                            >
                                @csrf

                                <button
                                    type="submit"
                                    class="admin-reject"
                                    title="Tolak"
                                >
                                    ×
                                </button>
                            </form>

                        </div>

                    </div>

                </div>

            @empty

                <div class="admin-empty">
                    Tidak ada permohonan registrasi.
                </div>

            @endforelse

            <div class="admin-panel-footer">
                <a
                    href="{{ route('admin.accounts.index') }}"
                    class="admin-link"
                >
                    Lihat semua akun →
                </a>
            </div>

        </div>


        {{-- REKAP BULAN INI --}}

        <div class="admin-panel">

            <div class="admin-panel-header">
                <h2 class="admin-panel-title">
                    Rekap Bulan Ini
                </h2>

                <span class="admin-panel-period">
                    {{ \Carbon\Carbon::createFromFormat('Y-m', $dashboardMonth)->locale('id')->isoFormat('MMM YYYY') }}
                </span>
            </div>


            {{-- OKUPANSI TERTINGGI --}}

            @if ($highestOccupancy)

                <div class="admin-recap-item">

                    <div class="admin-recap-name">
                        {{ $highestOccupancy['name'] }}
                    </div>

                    <span class="admin-recap-value high">
                        {{ $highestOccupancy['percentage'] }}%
                    </span>

                    <div class="admin-recap-description">
                        Okupansi tertinggi
                    </div>

                </div>

            @else

                <div class="admin-empty">
                    Belum ada data okupansi.
                </div>

            @endif


            {{-- OKUPANSI TERENDAH --}}

            @if ($lowestOccupancy)

                <div class="admin-recap-item">

                    <div class="admin-recap-name">
                        {{ $lowestOccupancy['name'] }}
                    </div>

                    <span class="admin-recap-value low">
                        {{ $lowestOccupancy['percentage'] }}%
                    </span>

                    <div class="admin-recap-description">
                        Okupansi terendah
                    </div>

                </div>

            @endif


            {{-- KERUSAKAN TERBANYAK --}}

            @if ($mostDamaged)

                <div class="admin-recap-item">

                    <div class="admin-recap-name">
                        {{ $mostDamaged['name'] }}
                    </div>

                    <span class="admin-recap-value damage">
                        {{ $mostDamaged['count'] }} laporan
                    </span>

                    <div class="admin-recap-description">
                        Laporan kerusakan terbanyak
                    </div>

                </div>

            @else

                <div class="admin-recap-item">

                    <div class="admin-recap-name">
                        Belum ada laporan
                    </div>

                    <div class="admin-recap-description">
                        Belum ada laporan kerusakan pada periode ini.
                    </div>

                </div>

            @endif


            <div class="admin-panel-footer">
                <a
                    href="{{ route('admin.reports.index') }}"
                    class="admin-link"
                >
                    Lihat rekap lengkap →
                </a>
            </div>

        </div>

    </div>

</div>

@endsection
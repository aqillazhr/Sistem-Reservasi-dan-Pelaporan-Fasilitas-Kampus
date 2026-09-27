@extends('layouts.dashboard')

@section('title', 'Reservasi Saya')

@section('content')
    @include('reservations._styles')

    <style>
        .history-switcher {
            display: flex;
            margin-bottom: 18px;
        }

        .history-switcher a {
            padding: 7px 22px;
            border: 1px solid #bd93f8;
            color: #260f45;
            font-size: 12px;
            text-decoration: none;
        }

        .history-switcher a:first-child {
            border-radius: 20px 0 0 20px;
        }

        .history-switcher a:last-child {
            border-radius: 0 20px 20px 0;
        }

        .history-switcher a.active {
            background: #9747ff;
            color: #ffffff;
        }

        /* ── Tabel riwayat (sesuai mockup) ── */
        .rsv .rsv-table-wrap {
            background: #fff;
            border: 1px solid #9747ff;
            border-radius: 10px;
            overflow: hidden;
        }
        .rsv table.rsv-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .rsv table.rsv-table thead th {
            background: #bd93f8;
            color: #000;
            font-family: 'Sora', Helvetica, sans-serif;
            font-weight: 600;
            font-size: 16px;
            text-align: left;
            padding: 18px 16px;
            border-right: 1px solid #9747ff;
        }
        .rsv table.rsv-table thead th:last-child { border-right: none; }
        .rsv table.rsv-table tbody td {
            padding: 16px;
            border-right: 1px solid #d5bbfb;
            border-bottom: 1px solid #bd93f8;
            font-size: 15px;
            vertical-align: top;
        }
        .rsv table.rsv-table tbody td:last-child { border-right: none; }
        .rsv table.rsv-table tbody tr:last-child td { border-bottom: none; }
        .rsv table.rsv-table tbody tr.rsv-row { cursor: pointer; }
        .rsv table.rsv-table tbody tr.rsv-row:hover { background: #fbf7ff; }
        .rsv table.rsv-table .status-note {
            margin-top: 6px;
            padding: 6px 10px;
            background: rgba(189, 147, 248, .3);
            border-radius: 8px;
            font-size: 12px;
            font-weight: 300;
            color: #000;
        }
        .rsv table.rsv-table .aksi-dash {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            max-width: 140px;
            height: 36px;
            border: 1px solid #9747ff;
            border-radius: 10px;
            color: #000;
            font-weight: 600;
        }
        .rsv table.rsv-table .btn-batalkan {
            display: inline-block;
            width: 100%;
            max-width: 140px;
            padding: 8px 0;
            background: #fff;
            border: 1px solid #f10606;
            border-radius: 10px;
            color: #f10606;
            font-family: 'Sora', Helvetica, sans-serif;
            font-weight: 600;
            font-size: 15px;
            text-align: center;
            cursor: pointer;
        }
        .rsv table.rsv-table .btn-batalkan:hover { background: #fff5f5; }
        .rsv table.rsv-table .empty td { text-align: center; padding: 30px 16px; color: #7a6497; }
    </style>

    <div class="rsv">
        <h1>Riwayat Saya</h1>
        <div class="history-switcher">
            <a href="{{ route('pengguna.reservations.index') }}"class="active">Reservasi</a>
            <a href="{{ route('pengguna.reports.index') }}">Laporan</a>
        </div>

        <nav class="tabs" aria-label="Filter status" id="rsvTabs">
            <a href="#" class="on" data-tab="semua">Semua</a>
            <a href="#" data-tab="menunggu">Menunggu</a>
            <a href="#" data-tab="aktif">Aktif</a>
            <a href="#" data-tab="selesai">Selesai</a>
            <a href="#" data-tab="ditolak">Ditolak</a>
            <a href="#" data-tab="dibatalkan">Dibatalkan</a>
        </nav>

        <div class="rsv-table-wrap">
            <table class="rsv-table">
                <thead>
                    <tr>
                        <th>Tgl. Pengajuan</th>
                        <th>Fasilitas</th>
                        <th>Jadwal</th>
                        <th>Keperluan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="rsvList">
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
                            // Alasan ditolak selalu ada di status_logs.note (wajib diisi petugas saat reject()).
                            // Alasan dibatalkan bisa dari kolom cancellation_reason (petugas/sistem) ATAU,
                            // kalau pengguna sendiri yang batalkan, dari status_logs.note ("Dibatalkan oleh pengguna")
                            // karena cancelByOwner() tidak mengisi cancellation_reason.
                            $statusNote = match ($r->status) {
                                'rejected' => optional($r->statusLogs->where('new_status', 'rejected')->last())->note,
                                'cancelled' => $r->cancellation_reason
                                    ?? optional($r->statusLogs->where('new_status', 'cancelled')->last())->note,
                                default => null,
                            };
                            $canCancel = $r->canBeCancelledByOwner();
                        @endphp
                        <tr class="rsv-row" data-tab="{{ $tabKey }}" data-href="{{ route('pengguna.reservations.show', $r) }}">
                            <td>{{ $r->created_at->locale('id')->isoFormat('D MMMM YYYY') }}</td>
                            <td>
                                <strong>{{ $r->facility->name }}</strong>
                            </td>
                            <td>{{ $r->reservation_date->locale('id')->isoFormat('D MMMM YYYY') }}</td>
                            <td>{{ $r->purpose }}</td>
                            <td>
                                <span class="badge {{ $r->status_badge_class }}">{{ $r->status_label }}</span>
                                @if ($statusNote)
                                    <div class="status-note">Catatan: {{ $statusNote }}</div>
                                @endif
                            </td>
                            <td>
                                @if ($canCancel)
                                    <form method="POST" action="{{ route('pengguna.reservations.cancel', $r) }}"
                                          class="form-confirm" data-confirm-title="Batalkan Reservasi"
                                          data-confirm-msg="Slot akan dilepas dan bisa dipesan orang lain. Anda yakin ingin membatalkan reservasi {{ $r->facility->name }} ini?">
                                        @csrf
                                        <button type="submit" class="btn-batalkan">Batalkan</button>
                                    </form>
                                @else
                                    <span class="aksi-dash">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr id="emptyCard" class="empty">
                            <td colspan="6">
                                Belum ada reservasi.
                                <a class="btn" href="{{ route('pengguna.reservations.create') }}">Ajukan reservasi</a>
                            </td>
                        </tr>
                    @endforelse

                    {{-- Pesan kosong per tab (muncul via JS) --}}
                    <tr id="emptyTab" class="empty" hidden>
                        <td colspan="6">Tidak ada reservasi di kategori ini.</td>
                    </tr>
                </tbody>
            </table>
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
        var rows     = document.querySelectorAll('#rsvList tr.rsv-row');
        var emptyTab = document.getElementById('emptyTab');

        function filter(tab) {
            var visible = 0;
            rows.forEach(function (r) {
                var show = tab === 'semua' || r.dataset.tab === tab;
                r.style.display = show ? '' : 'none';
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

        // Baris tabel bisa diklik untuk lihat detail (kecuali klik di tombol/form Batalkan).
        document.getElementById('rsvList').addEventListener('click', function (e) {
            if (e.target.closest('a, button, form')) return;
            var row = e.target.closest('tr.rsv-row');
            if (row) window.location.href = row.dataset.href;
        });
    })();
    </script>
@endsection

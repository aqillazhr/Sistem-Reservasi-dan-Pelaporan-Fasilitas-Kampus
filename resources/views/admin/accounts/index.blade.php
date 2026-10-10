@extends('layouts.dashboard')

@section('title', 'Manajemen Pengguna')

@section('content')
    <style>
        .account-page-title {
            font-family: 'Sora', Helvetica, sans-serif;
            font-weight: 700;
            font-size: 46px;
            color: #501e91;
            margin: 0 0 16px;
        }
        table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 6px; overflow: hidden; }
        th, td { text-align: left; padding: 10px 12px; border-bottom: 1px solid #e5e7eb; font-size: 14px; }
        th { background: #f9fafb; font-weight: 600; }
        .btn { padding: 6px 12px; border-radius: 5px; border: none; font-size: 13px; cursor: pointer; color: #fff; text-decoration: none; display: inline-block; }
        .btn-verify { background: #16a34a; }
        .btn-reject { background: #dc2626; }
        .btn-toggle { background: #6b7280; }
        .btn-edit { background: #2563eb; }
        .empty-text { color: #6b7280; font-style: italic; padding: 12px; }
        .actions form, .actions a { display: inline-block; margin-right: 4px; }
        h2 { font-size: 16px; margin-top: 32px; }

        .tbl-pager {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            margin: 12px 0 0;
        }
        .tbl-pager button {
            padding: 6px 18px;
            border: 1px solid #bd93f8;
            border-radius: 8px;
            background: #fff;
            color: #501e91;
            font-family: 'Sora', Helvetica, sans-serif;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }
        .tbl-pager button:hover { background: #eee7f7; }
        .tbl-pager button:disabled { opacity: .4; cursor: not-allowed; }
        .tbl-pager-info { font-size: 12px; color: #5b4a78; }
    </style>

    <h1 class="account-page-title">Manajemen Pengguna</h1>

    {{-- Search --}}
    <form method="GET" action="{{ route('admin.accounts.index') }}" style="margin-bottom:16px;">
        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau email..." style="padding:8px; width:280px;">
        <button type="submit" style="padding:8px 16px;">Cari</button>
        @if ($search)
            <a href="{{ route('admin.accounts.index') }}">Reset</a>
        @endif
    </form>

    <h2>Menunggu Verifikasi ({{ $pendingUsers->count() }})</h2>
    @if ($pendingUsers->isEmpty())
        <div class="empty-text">Tidak ada akun yang menunggu verifikasi.</div>
    @else
        <table id="tblPending">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Jenis</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pendingUsers as $user)
                    <tr class="paged-row" data-table="pending">
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->user_type }}</td>
                        <td class="actions">
                            <a href="{{ route('admin.accounts.show', $user) }}" class="btn btn-edit">Detail</a>
                            <form method="POST" action="{{ route('admin.accounts.verify', $user) }}">
                                @csrf
                                <button type="submit" class="btn btn-verify">Verifikasi</button>
                            </form>
                            <form method="POST" action="{{ route('admin.accounts.reject', $user) }}">
                                @csrf
                                <button type="submit" class="btn btn-reject">Tolak</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if ($pendingUsers->count() > 5)
            <div class="tbl-pager" id="pagerPending">
                <button type="button" class="pg-prev">&larr; Sebelumnya</button>
                <span class="tbl-pager-info pg-info"></span>
                <button type="button" class="pg-next">Berikutnya &rarr;</button>
            </div>
        @endif
    @endif

    <h2>Sudah Terverifikasi ({{ $verifiedUsers->count() }})</h2>
    @if ($verifiedUsers->isEmpty())
        <div class="empty-text">Belum ada akun yang terverifikasi.</div>
    @else
        <table id="tblVerified">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status Akun</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($verifiedUsers as $user)
                    <tr class="paged-row" data-table="verified">
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->role }}</td>
                        <td>{{ $user->account_status }}</td>
                        <td class="actions">
                            <a href="{{ route('admin.accounts.edit', $user) }}" class="btn btn-edit">Edit</a>
                            <form method="POST" action="{{ route('admin.accounts.toggle-active', $user) }}">
                                @csrf
                                <button type="submit" class="btn btn-toggle">
                                    {{ $user->account_status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if ($verifiedUsers->count() > 5)
            <div class="tbl-pager" id="pagerVerified">
                <button type="button" class="pg-prev">&larr; Sebelumnya</button>
                <span class="tbl-pager-info pg-info"></span>
                <button type="button" class="pg-next">Berikutnya &rarr;</button>
            </div>
        @endif
    @endif

    <h2>Buat Akun Petugas/Pengguna Langsung</h2>
    <form method="POST" action="{{ route('admin.accounts.store') }}" style="background:#fff; padding:16px; border-radius:6px; max-width:400px;">
        @csrf
        <div style="margin-bottom:12px;">
            <label>Nama</label><br>
            <input type="text" name="name" required style="width:100%; padding:8px;">
        </div>
        <div style="margin-bottom:12px;">
            <label>Email</label><br>
            <input type="email" name="email" required style="width:100%; padding:8px;">
        </div>
        <div style="margin-bottom:12px;">
            <label>Password</label><br>
            <input type="password" name="password" required minlength="8" style="width:100%; padding:8px;">
        </div>
        <div style="margin-bottom:12px;">
            <label>Role</label><br>
            <select name="role" style="width:100%; padding:8px;">
                <option value="petugas">Petugas</option>
                <option value="pengguna">Pengguna</option>
            </select>
        </div>
        <div style="margin-bottom:12px;">
            <label>Jenis (khusus role pengguna)</label><br>
            <select name="user_type" style="width:100%; padding:8px;">
                <option value="mahasiswa">Mahasiswa</option>
                <option value="dosen">Dosen</option>
                <option value="staf">Staf</option>
            </select>
        </div>
        <button type="submit" class="btn btn-verify">Buat Akun</button>
    </form>

    <script>
    (function () {
        function setupPager(tableGroup, pagerId, perPage) {
            var rows = document.querySelectorAll('tr.paged-row[data-table="' + tableGroup + '"]');
            var pagerEl = document.getElementById(pagerId);
            if (!pagerEl || rows.length <= perPage) return;

            var page = 0, totalPages = Math.ceil(rows.length / perPage);
            var prev = pagerEl.querySelector('.pg-prev');
            var next = pagerEl.querySelector('.pg-next');
            var info = pagerEl.querySelector('.pg-info');

            function render() {
                var start = page * perPage, end = start + perPage;
                rows.forEach(function (r, i) {
                    r.style.display = (i >= start && i < end) ? '' : 'none';
                });
                prev.disabled = page === 0;
                next.disabled = page >= totalPages - 1;
                info.textContent = (page + 1) + ' / ' + totalPages;
            }
            prev.addEventListener('click', function () { if (page > 0) { page--; render(); } });
            next.addEventListener('click', function () { if (page < totalPages - 1) { page++; render(); } });
            render();
        }
        setupPager('pending', 'pagerPending', 5);
        setupPager('verified', 'pagerVerified', 5);
    })();
    </script>
@endsection

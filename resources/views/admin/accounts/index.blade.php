@extends('layouts.dashboard')

@section('title', 'Manajemen Pengguna')

@section('content')
<style>
    .account-page-title {
        font-family: 'Sora', Helvetica, sans-serif;
        font-weight: 700;
        font-size: 46px;
        color: #501e91;
        margin: 0 0 20px;
    }

    .account-search {
        display: flex;
        gap: 10px;
        margin-bottom: 24px;
        max-width: 460px;
    }
    .account-search input {
        flex: 1;
        padding: 10px 14px;
        border: 1px solid #d8cde7;
        border-radius: 8px;
        background: #fff;
        font-family: 'Sora', Helvetica, sans-serif;
        font-size: 14px;
        color: #260f45;
        outline: none;
    }
    .account-search input:focus { border-color: #9747ff; }
    .account-search button {
        padding: 10px 20px;
        border: 1px solid #bd93f8;
        border-radius: 8px;
        background: #fff;
        color: #501e91;
        font-family: 'Sora', Helvetica, sans-serif;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
    }
    .account-search button:hover { background: #eee7f7; }
    .account-search a.reset-link {
        display: inline-flex;
        align-items: center;
        padding: 0 10px;
        color: #501e91;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
    }

    .section-heading {
        font-family: 'Sora', Helvetica, sans-serif;
        font-weight: 700;
        font-size: 24px;
        color: #501e91;
        margin: 28px 0 14px;
    }

    .acc-table-wrap {
        background: #fff;
        border: 1px solid #9747ff;
        border-radius: 10px;
        overflow: hidden;
    }
    .acc-table {
        width: 100%;
        border-collapse: collapse;
    }
    .acc-table thead th {
        background: #BD93F8;
        color: #000;
        font-family: 'Sora', Helvetica, sans-serif;
        font-size: 16px;
        font-weight: 700;
        text-align: left;
        padding: 14px 18px;
        border-right: 1px solid #9747ff;
    }
    .acc-table thead th:last-child { border-right: none; text-align: center; }
    .acc-table tbody td {
        padding: 14px 18px;
        border-top: 1px solid #D9C3F4;
        border-right: 1px solid #D9C3F4;
        font-family: 'Sora', Helvetica, sans-serif;
        font-size: 14px;
        color: #222;
        vertical-align: middle;
    }
    .acc-table tbody td:last-child { border-right: none; text-align: center; }

    .peran-text { color: #260f45; }
    .peran-text .peran-type { color: #9747ff; }

    .status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        min-width: 100px;
        padding: 6px 12px;
        border-radius: 8px;
        font-family: 'Sora', Helvetica, sans-serif;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        border: none;
    }
    .status-aktif {
        background: #c9f7d3;
        color: #176b2c;
    }
    .status-nonaktif {
        background: #ffb3b3;
        color: #8f1111;
    }

    .pending-actions {
        display: inline-flex;
        gap: 8px;
    }
    .pending-actions .btn {
        padding: 7px 14px;
        border-radius: 6px;
        border: none;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        color: #fff;
        text-decoration: none;
        display: inline-block;
        font-family: 'Sora', Helvetica, sans-serif;
    }
    .btn-detail { background: #2563eb; }
    .btn-verify { background: #16a34a; }
    .btn-reject { background: #dc2626; }
    .btn-detail:hover { background: #1e40af; }
    .btn-verify:hover { background: #15803d; }
    .btn-reject:hover { background: #b91c1c; }

    .empty-text {
        padding: 20px;
        text-align: center;
        color: #76677f;
        font-style: italic;
        font-size: 14px;
    }

    .tbl-pager {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 12px;
        margin: 14px 0 0;
    }
    .tbl-pager button {
        padding: 7px 18px;
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

    /* ===== Form "Buat Akun" dengan style ala profil ===== */
    .create-section { margin-top: 40px; max-width: 600px; }
    .create-card {
        background: #FFFFFF;
        box-shadow: 0 4px 4px rgba(0, 0, 0, 0.08);
        border-radius: 15px;
        padding: 24px;
    }
    .create-card .field-label {
        display: block;
        font-family: 'Sora', Helvetica, sans-serif;
        font-weight: 600;
        font-size: 16px;
        color: #260f45;
        margin: 0 0 8px;
    }
    .create-card .field-input,
    .create-card .field-select {
        display: block;
        width: 100%;
        height: 46px;
        background: rgba(240, 230, 255, 0.7);
        border: none;
        border-radius: 10px;
        padding: 0 15px;
        margin-bottom: 18px;
        font-family: 'Sora', Helvetica, sans-serif;
        font-weight: 500;
        font-size: 15px;
        color: #000000;
        box-sizing: border-box;
    }
    .create-card .field-input::placeholder { color: #a089c0; }
    .create-card .field-select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='8' viewBox='0 0 14 8'%3E%3Cpath d='M1 1l6 6 6-6' stroke='%23511F91' stroke-width='1.5' fill='none'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 15px center;
        background-color: rgba(240, 230, 255, 0.7);
        padding-right: 40px;
    }
    .btn-buat-akun {
        display: block;
        margin: 8px 0 0 auto;
        padding: 12px 32px;
        background: #511F91;
        border: none;
        border-radius: 13px;
        color: #FFFFFF;
        font-family: 'Sora', Helvetica, sans-serif;
        font-weight: 600;
        font-size: 18px;
        cursor: pointer;
        transition: background .15s;
    }
    .btn-buat-akun:hover { background: #3f1773; }

    .field-error { color: #dc2626; font-size: 12px; margin: -14px 0 10px; }
</style>

<h1 class="account-page-title">Manajemen Pengguna</h1>

{{-- Search --}}
<form method="GET" action="{{ route('admin.accounts.index') }}" class="account-search">
    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau email...">
    <button type="submit">Cari</button>
    @if ($search)
        <a href="{{ route('admin.accounts.index') }}" class="reset-link">Reset</a>
    @endif
</form>

{{-- ===== Menunggu Verifikasi ===== --}}
<h2 class="section-heading">Menunggu Verifikasi ({{ $pendingUsers->count() }})</h2>

@if ($pendingUsers->isEmpty())
    <div class="acc-table-wrap">
        <div class="empty-text">Tidak ada akun yang menunggu verifikasi.</div>
    </div>
@else
    <div class="acc-table-wrap">
        <table class="acc-table">
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
                        <td>{{ ucfirst($user->user_type ?? '-') }}</td>
                        <td>
                            <div class="pending-actions">
                                <a href="{{ route('admin.accounts.show', $user) }}" class="btn btn-detail">Detail</a>
                                <form method="POST" action="{{ route('admin.accounts.verify', $user) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-verify">Verifikasi</button>
                                </form>
                                <form method="POST" action="{{ route('admin.accounts.reject', $user) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-reject">Tolak</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if ($pendingUsers->count() > 5)
        <div class="tbl-pager" id="pagerPending">
            <button type="button" class="pg-prev">&larr; Sebelumnya</button>
            <span class="tbl-pager-info pg-info"></span>
            <button type="button" class="pg-next">Berikutnya &rarr;</button>
        </div>
    @endif
@endif

{{-- ===== Sudah Terverifikasi (desain baru dengan Peran + Status badge) ===== --}}
<h2 class="section-heading">Sudah Terverifikasi ({{ $verifiedUsers->count() }})</h2>

@if ($verifiedUsers->isEmpty())
    <div class="acc-table-wrap">
        <div class="empty-text">Belum ada akun yang terverifikasi.</div>
    </div>
@else
    <div class="acc-table-wrap">
        <table class="acc-table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Peran</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($verifiedUsers as $user)
                    <tr class="paged-row" data-table="verified">
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="peran-text">
                                {{ ucfirst($user->role) }}@if ($user->user_type)-<span class="peran-type">{{ ucfirst($user->user_type) }}</span>@endif
                            </span>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('admin.accounts.toggle-active', $user) }}" style="display:inline;">
                                @csrf
                                <button type="submit" class="status-badge {{ $user->account_status === 'aktif' ? 'status-aktif' : 'status-nonaktif' }}"
                                    title="Klik untuk {{ $user->account_status === 'aktif' ? 'nonaktifkan' : 'aktifkan' }}">
                                    {{ $user->account_status === 'aktif' ? 'Aktif' : 'Nonaktif' }}
                                    <span style="font-size:11px;">&lsaquo;</span>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if ($verifiedUsers->count() > 5)
        <div class="tbl-pager" id="pagerVerified">
            <button type="button" class="pg-prev">&larr; Sebelumnya</button>
            <span class="tbl-pager-info pg-info"></span>
            <button type="button" class="pg-next">Berikutnya &rarr;</button>
        </div>
    @endif
@endif

{{-- ===== Buat Akun Petugas/Pengguna Langsung (style ala edit profil) ===== --}}
<div class="create-section">
    <h2 class="section-heading">Buat Akun Petugas/Pengguna Langsung</h2>

    <div class="create-card">
        <form method="POST" action="{{ route('admin.accounts.store') }}">
            @csrf

            <label class="field-label">Nama</label>
            <input type="text" name="name" class="field-input" value="{{ old('name') }}" required>
            @error('name')<div class="field-error">{{ $message }}</div>@enderror

            <label class="field-label">Email</label>
            <input type="email" name="email" class="field-input" value="{{ old('email') }}" required>
            @error('email')<div class="field-error">{{ $message }}</div>@enderror

            <label class="field-label">Password</label>
            <input type="password" name="password" class="field-input" required minlength="8">
            @error('password')<div class="field-error">{{ $message }}</div>@enderror

            <label class="field-label">Role</label>
            <select name="role" class="field-select">
                <option value="petugas">Petugas</option>
                <option value="pengguna">Pengguna</option>
            </select>

            <label class="field-label">Jenis (khusus role pengguna)</label>
            <select name="user_type" class="field-select">
                <option value="mahasiswa">Mahasiswa</option>
                <option value="dosen">Dosen</option>
                <option value="staf">Staf</option>
            </select>

            <button type="submit" class="btn-buat-akun">Buat Akun</button>
        </form>
    </div>
</div>

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

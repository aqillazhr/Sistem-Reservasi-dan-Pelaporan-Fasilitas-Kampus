@extends('layouts.dashboard')

@section('title', 'Detail Pengajuan Akun')

@section('content')
    <h1>Detail Pengajuan Akun</h1>

    <div style="background:#fff; padding:20px; border-radius:6px; max-width:420px; margin-bottom:20px;">
        <p><strong>Nama</strong>: {{ $user->name }}</p>
        <p><strong>Email</strong>: {{ $user->email }}</p>
        <p><strong>Jenis</strong>: {{ ucfirst($user->user_type) }}</p>
        <p><strong>Status</strong>: {{ $user->status }}</p>
    </div>

    <form method="POST" action="{{ route('admin.accounts.verify', $user) }}" style="display:inline;">
        @csrf
        <button type="submit" style="padding:8px 16px; background:#16a34a; color:#fff; border:none; border-radius:6px; cursor:pointer;">Terima</button>
    </form>

    <form method="POST" action="{{ route('admin.accounts.reject', $user) }}" style="display:inline;">
        @csrf
        <button type="submit" style="padding:8px 16px; background:#dc2626; color:#fff; border:none; border-radius:6px; cursor:pointer;">Tolak</button>
    </form>

    <p style="margin-top:16px;"><a href="{{ route('admin.accounts.index') }}">&larr; Kembali ke daftar</a></p>
@endsection
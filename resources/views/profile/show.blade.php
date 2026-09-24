@extends('layouts.dashboard')

@section('title', 'Profil Saya')

@section('content')
    <h1>Profil Saya</h1>

    <div style="background:#fff; padding:20px; border-radius:6px; max-width:420px; margin-bottom:24px;">
        <p><strong>Nama</strong>: {{ $user->name }}</p>
        <p><strong>Email</strong>: {{ $user->email }}</p>
        <p><strong>Role</strong>: {{ $user->role }}</p>
        @if ($user->user_type)
            <p><strong>Jenis</strong>: {{ $user->user_type }}</p>
        @endif
    </div>

    <h2 style="font-size:16px;">Edit Profil</h2>
    <form method="POST" action="{{ route('profile.update') }}" style="background:#fff; padding:20px; border-radius:6px; max-width:420px;">
        @csrf
        @method('PUT')

        <div style="margin-bottom:12px;">
            <label>Nama</label><br>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required style="width:100%; padding:8px;">
            @error('name')
                <div style="color:#dc2626; font-size:13px;">{{ $message }}</div>
            @enderror
        </div>

        <div style="margin-bottom:12px;">
            <label>Email</label><br>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required style="width:100%; padding:8px;">
            @error('email')
                <div style="color:#dc2626; font-size:13px;">{{ $message }}</div>
            @enderror
        </div>

        <div style="margin-bottom:12px;">
            <label>Password Baru (kosongkan kalau tidak ingin ganti)</label><br>
            <input type="password" name="password" minlength="8" style="width:100%; padding:8px;">
            @error('password')
                <div style="color:#dc2626; font-size:13px;">{{ $message }}</div>
            @enderror
        </div>

        <div style="margin-bottom:12px;">
            <label>Konfirmasi Password Baru</label><br>
            <input type="password" name="password_confirmation" minlength="8" style="width:100%; padding:8px;">
        </div>

        <button type="submit" style="padding:8px 16px; background:#2563eb; color:#fff; border:none; border-radius:6px; cursor:pointer;">Simpan</button>
    </form>
@endsection
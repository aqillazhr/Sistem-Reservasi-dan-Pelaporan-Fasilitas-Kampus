@extends('layouts.dashboard')

@section('title', 'Edit Pengguna')

@section('content')
    <h1>Edit Pengguna</h1>

    <form method="POST" action="{{ route('admin.accounts.update', $user) }}" style="background:#fff; padding:20px; border-radius:6px; max-width:420px;">
        @csrf
        @method('PUT')

        <div style="margin-bottom:12px;">
            <label>Nama</label><br>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required style="width:100%; padding:8px;">
            @error('name')<div style="color:#dc2626; font-size:13px;">{{ $message }}</div>@enderror
        </div>

        <div style="margin-bottom:12px;">
            <label>Email</label><br>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required style="width:100%; padding:8px;">
            @error('email')<div style="color:#dc2626; font-size:13px;">{{ $message }}</div>@enderror
        </div>

        <div style="margin-bottom:12px;">
            <label>Role</label><br>
            <select name="role" style="width:100%; padding:8px;">
                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="petugas" {{ $user->role === 'petugas' ? 'selected' : '' }}>Petugas</option>
                <option value="pengguna" {{ $user->role === 'pengguna' ? 'selected' : '' }}>Pengguna</option>
            </select>
        </div>

        <div style="margin-bottom:12px;">
            <label>Jenis (khusus role pengguna)</label><br>
            <select name="user_type" style="width:100%; padding:8px;">
                <option value="mahasiswa" {{ $user->user_type === 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                <option value="dosen" {{ $user->user_type === 'dosen' ? 'selected' : '' }}>Dosen</option>
                <option value="staf" {{ $user->user_type === 'staf' ? 'selected' : '' }}>Staf</option>
            </select>
        </div>

        <button type="submit" style="padding:8px 16px; background:#2563eb; color:#fff; border:none; border-radius:6px; cursor:pointer;">Simpan</button>
        <a href="{{ route('admin.accounts.index') }}" style="margin-left:8px;">Batal</a>
    </form>
@endsection
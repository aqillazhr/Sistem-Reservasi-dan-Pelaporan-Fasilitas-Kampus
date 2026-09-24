@extends('layouts.guest')

@section('title', 'Daftar Akun')

@section('content')
    <h1>Daftar Akun Pengguna</h1>

    {{-- Loading state saat submit dihandle simple lewat disable button di JS bawah,
         cukup buat kebutuhan tugas ini, boleh diganti spinner sesuai desain Figma. --}}

    <form method="POST" action="{{ route('register') }}" id="registerForm">
        @csrf

        {{-- Langkah: "Memasukkan data diri" --}}
        <div class="form-group">
            <label for="name">Nama Lengkap</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required>
            @error('name')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        {{-- Langkah: "Memasukkan email khusus pengguna/petugas".
             Field-nya sama untuk semua, cuma pengguna yang boleh isi form ini sendiri
             (petugas dibuat langsung oleh admin, bukan lewat form publik). --}}
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required>
            @error('email')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="user_type">Jenis Pengguna</label>
            <select id="user_type" name="user_type" required>
                <option value="" disabled {{ old('user_type') ? '' : 'selected' }}>Pilih jenis</option>
                <option value="mahasiswa" {{ old('user_type') === 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                <option value="dosen" {{ old('user_type') === 'dosen' ? 'selected' : '' }}>Dosen</option>
                <option value="staf" {{ old('user_type') === 'staf' ? 'selected' : '' }}>Staf</option>
            </select>
            @error('user_type')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        {{-- Langkah: "Memasukkan password" --}}
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required minlength="8">
            @error('password')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        {{-- Langkah: "Memasukkan ulang password" (konfirmasi) --}}
        <div class="form-group">
            <label for="password_confirmation">Konfirmasi Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8">
        </div>

        {{-- Langkah: "Menekan tombol submit" --}}
        <button type="submit" class="btn-primary" id="submitBtn">Daftar</button>
    </form>

    <p class="auth-footer">
        Sudah punya akun? <a href="{{ route('login') }}">Login di sini</a>
    </p>

    <script>
        // Loading state sederhana: disable tombol pas submit biar nggak double-klik.
        document.getElementById('registerForm').addEventListener('submit', function () {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.textContent = 'Memproses...';
        });
    </script>
@endsection

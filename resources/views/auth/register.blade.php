@extends('layouts.guest')

@section('title', 'Daftar Akun')

@section('content')
    <h1 class="visually-hidden">Daftar Akun Pengguna</h1>

    <form method="POST" action="{{ route('register') }}" id="registerForm">
        @csrf

        <div class="form-group">
            <label for="name">Nama Lengkap</label>
            <input type="text" id="name" name="name" placeholder="Masukkan nama lengkap"
                   value="{{ old('name') }}" required>
            @error('name')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Masukkan email"
                   value="{{ old('email') }}" required>
            @error('email')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="user_type">Jenis Pengguna</label>
            <select id="user_type" name="user_type" required>
                <option value="" disabled {{ old('user_type') ? '' : 'selected' }}>Pilih Jenis</option>
                <option value="mahasiswa" {{ old('user_type') === 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                <option value="dosen" {{ old('user_type') === 'dosen' ? 'selected' : '' }}>Dosen</option>
                <option value="staf" {{ old('user_type') === 'staf' ? 'selected' : '' }}>Staf</option>
            </select>
            @error('user_type')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">Masukkan Kata Sandi</label>
            <input type="password" id="password" name="password" placeholder="Masukkan kata sandi"
                   required minlength="8">
            @error('password')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation">Konfirmasi Kata Sandi</label>
            <input type="password" id="password_confirmation" name="password_confirmation"
                   placeholder="Masukkan kembali kata sandi" required minlength="8">
        </div>

        <div class="form-actions">
            <a href="{{ route('login') }}" class="btn btn-secondary">
                Batalkan
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5"/>
                    <line x1="6.5" y1="17.5" x2="17.5" y2="6.5" stroke="currentColor" stroke-width="1.5"/>
                </svg>
            </a>
            <button type="submit" class="btn btn-primary" id="submitBtn">
                Daftar
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M10 17l5-5-5-5M4 12h11M19 5v14" stroke="currentColor" stroke-width="1.8"
                          stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </div>
    </form>

    <p class="auth-footer">
        Sudah punya akun? <a href="{{ route('login') }}">Login di sini</a>
    </p>

    <script>
        document.getElementById('registerForm').addEventListener('submit', function () {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.textContent = 'Memproses...';
        });
    </script>
@endsection
@extends('layouts.guest')

@section('title', 'Login')

@section('content')
    <h1>Login</h1>

    @if (session('status'))
        <div class="status-text">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>

        @error('email')
            <div class="error-text">{{ $message }}</div>
        @enderror

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                Login
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M10 17l5-5-5-5M4 12h11M19 5v14" stroke="currentColor" stroke-width="1.8"
                          stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </div>
    </form>

    <p class="auth-footer">
        Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a>
    </p>
    <p class="auth-footer">
        Hanya ingin melihat fasilitas? <a href="{{ route('facilities.index') }}">Lihat sebagai pengunjung</a>
    </p>
@endsection

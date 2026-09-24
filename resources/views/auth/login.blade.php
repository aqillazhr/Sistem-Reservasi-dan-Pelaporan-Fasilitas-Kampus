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

        <button type="submit" class="btn-primary">Login</button>
    </form>

    <p class="auth-footer">
        Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a>
    </p>
    <p class="auth-footer">
        Hanya ingin melihat fasilitas? <a href="{{ route('facilities.index') }}">Lihat sebagai pengunjung</a>
    </p>
@endsection

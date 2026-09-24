@extends('layouts.dashboard')

@section('title', 'Profil Saya')

@section('content')
<style>
    .profile-wrap { position: relative; padding: 40px 40px 140px; min-height: 800px; }

    .avatar-big {
        position: absolute;
        left: 82px; top: 80px;
        width: 282px; height: 282px;
        border-radius: 50%;
        background: #BD93F8;
        display: flex; align-items: center; justify-content: center;
        font-family: 'Inter', sans-serif; font-weight: 500; font-size: 64px;
        color: #FFFFFF;
    }

    .content-col { margin-left: 420px; max-width: 722px; }

    .section-heading {
        font-family: 'Sora', sans-serif;
        font-weight: 700; font-size: 36px;
        color: #511F91;
        margin: 0 0 15px;
    }

    .info-card {
        background: #FFFFFF;
        box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
        border-radius: 15px;
        padding: 17px 24px;
        margin-bottom: 30px;
    }

    .info-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: rgba(240, 230, 255, 0.7);
        border-radius: 10px;
        height: 46px;
        padding: 0 15px;
        margin-bottom: 14px;
        font-family: 'Sora', sans-serif;
        font-weight: 600; font-size: 20px;
    }
    .info-row:last-child { margin-bottom: 0; }
    .info-row .info-label { color: #511F91; }
    .info-row .info-value { color: #000000; }

    .edit-card {
        background: #FFFFFF;
        box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
        border-radius: 15px;
        padding: 17px 24px 24px;
    }

    .edit-input {
        display: block;
        width: 100%;
        height: 46px;
        background: rgba(240, 230, 255, 0.7);
        border: none;
        border-radius: 10px;
        padding: 0 15px;
        margin-bottom: 14px;
        font-family: 'Sora', sans-serif;
        font-weight: 600; font-size: 20px;
        color: #000000;
    }
    .edit-input::placeholder { color: #511F91; font-weight: 600; }
    .edit-input:last-of-type { margin-bottom: 0; }

    .btn-simpan {
        display: block;
        margin: 24px 0 0 auto;
        padding: 12px 32px;
        background: #511F91;
        border: none;
        border-radius: 13px;
        color: #FFFFFF;
        font-family: 'Sora', sans-serif;
        font-weight: 600; font-size: 20px;
        cursor: pointer;
    }

    .error-text { color: #dc2626; font-size: 13px; margin: -10px 0 10px; }
</style>

<div class="profile-wrap">
    <div class="avatar-big">{{ strtoupper($initials) }}</div>

    <div class="content-col">
        <h1 class="section-heading">Profil Saya</h1>
        <div class="info-card">
            <div class="info-row">
                <span class="info-label">Nama</span>
                <span class="info-value">{{ $user->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Email</span>
                <span class="info-value">{{ $user->email }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Role</span>
                <span class="info-value">{{ ucfirst($user->role) }}</span>
            </div>
            @if ($user->user_type)
                <div class="info-row">
                    <span class="info-label">Jenis</span>
                    <span class="info-value">{{ ucfirst($user->user_type) }}</span>
                </div>
            @endif
        </div>

        <h2 class="section-heading">Edit Profil</h2>
        <div class="edit-card">
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PUT')

                <input type="text" name="name" class="edit-input" placeholder="Nama" value="{{ old('name', $user->name) }}">
                @error('name')<div class="error-text">{{ $message }}</div>@enderror

                <input type="email" name="email" class="edit-input" placeholder="Email" value="{{ old('email', $user->email) }}">
                @error('email')<div class="error-text">{{ $message }}</div>@enderror

                <input type="password" name="password" class="edit-input" placeholder="Password Baru" minlength="8">
                @error('password')<div class="error-text">{{ $message }}</div>@enderror

                <input type="password" name="password_confirmation" class="edit-input" placeholder="Konfirmasi Password Baru" minlength="8">

                <button type="submit" class="btn-simpan">Simpan</button>
            </form>
        </div>
    </div>
</div>
@endsection
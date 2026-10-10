@extends('layouts.dashboard')

@section('title', 'Profil Saya')

@section('content')
<style>
    .profile-wrap { position: relative; padding: 40px 40px 140px; min-height: 800px; }

    .avatar-col {
        position: absolute;
        left: 82px; top: 80px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 16px;
    }

    .avatar-big {
        width: 282px; height: 282px;
        border-radius: 50%;
        background: #BD93F8;
        display: flex; align-items: center; justify-content: center;
        font-family: 'Inter', sans-serif; font-weight: 500; font-size: 64px;
        color: #FFFFFF;
        overflow: hidden;
        flex-shrink: 0;
    }
    .avatar-big img {
        width: 100%; height: 100%;
        object-fit: cover;
    }

    .btn-ubah-foto {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 206px;
        height: 61px;
        background: rgba(151, 71, 255, 0.6);
        border: none;
        border-radius: 15px;
        color: #FFFFFF;
        font-family: 'Sora', sans-serif;
        font-weight: 700;
        font-size: 18px;
        cursor: pointer;
        transition: background .15s;
    }
    .btn-ubah-foto:hover { background: rgba(151, 71, 255, 0.8); }

    .btn-hapus-foto {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: none;
        border: none;
        color: #dc2626;
        font-family: 'Sora', sans-serif;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        padding: 0;
    }
    .btn-hapus-foto:hover { text-decoration: underline; }

    .avatar-input { display: none; }

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
        box-sizing: border-box;
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

    {{-- ===== Avatar + tombol ubah foto ===== --}}
    <div class="avatar-col">
        <div class="avatar-big" id="avatarPreview">
            @if ($user->avatar)
                <img src="{{ asset('storage/' . $user->avatar) }}" alt="Foto profil {{ $user->name }}">
            @else
                {{ strtoupper($initials) }}
            @endif
        </div>

        {{-- Form upload foto (hidden input, trigger lewat tombol) --}}
        <form method="POST" action="{{ route('profile.avatar.update') }}" enctype="multipart/form-data" id="avatarForm">
            @csrf
            <input type="file" name="avatar" accept="image/jpeg,image/png,image/webp" class="avatar-input" id="avatarInput">
            <button type="button" class="btn-ubah-foto" id="btnUbahFoto">Ubah Foto</button>
        </form>
        @error('avatar')<div class="error-text" style="text-align:center;">{{ $message }}</div>@enderror

        @if ($user->avatar)
            <form method="POST" action="{{ route('profile.avatar.delete') }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-hapus-foto">Hapus Foto</button>
            </form>
        @endif
    </div>

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

<script>
(function () {
    var btnUbah = document.getElementById('btnUbahFoto');
    var input = document.getElementById('avatarInput');
    var form = document.getElementById('avatarForm');
    var preview = document.getElementById('avatarPreview');

    btnUbah.addEventListener('click', function () {
        input.click();
    });

    input.addEventListener('change', function () {
        if (input.files && input.files[0]) {
            // Preview langsung
            var reader = new FileReader();
            reader.onload = function (e) {
                preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview" style="width:100%;height:100%;object-fit:cover;">';
            };
            reader.readAsDataURL(input.files[0]);

            // Auto-submit form
            form.submit();
        }
    });
})();
</script>
@endsection

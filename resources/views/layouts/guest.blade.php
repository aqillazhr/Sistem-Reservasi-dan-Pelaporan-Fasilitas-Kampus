<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Sistem Reservasi & Pelaporan Fasilitas Kampus')</title>
    {{-- TODO: ganti/tambah link CSS di sini sesuai styling Figma kamu
         (Tailwind CDN, Bootstrap CDN, atau file CSS sendiri di public/css) --}}
    <style>
        body { font-family: system-ui, sans-serif; background: #f4f5f7; margin: 0; }
        .auth-wrapper { max-width: 420px; margin: 60px auto; background: #fff; padding: 32px; border-radius: 8px; box-shadow: 0 1px 4px rgba(0,0,0,.08); }
        .auth-wrapper h1 { font-size: 20px; margin-bottom: 20px; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; margin-bottom: 6px; font-size: 14px; font-weight: 600; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; box-sizing: border-box; }
        .btn-primary { width: 100%; padding: 10px; background: #2563eb; color: #fff; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; }
        .error-text { color: #dc2626; font-size: 13px; margin-top: 4px; }
        .status-text { color: #16a34a; font-size: 14px; margin-bottom: 16px; }
        .auth-footer { text-align: center; margin-top: 16px; font-size: 14px; }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        @yield('content')
    </div>
</body>
</html>

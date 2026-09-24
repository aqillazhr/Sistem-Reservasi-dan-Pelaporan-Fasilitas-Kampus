<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Sistem Reservasi & Pelaporan Fasilitas Kampus')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --color-primary: #9747FF;
            --color-primary-light: #BD93F8;
            --color-primary-dark: #511F91;
            --color-surface: #FBF7FF;
        }

        *, *::before, *::after { box-sizing: border-box; }
        html, body { margin: 0; min-height: 100%; }

        body {
            font-family: 'Sora', Helvetica, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            position: relative;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            z-index: -1;
            background: conic-gradient(from 68.23deg at 26.74% 58.94%,
                #F9F4FF -15.58deg, #D5BBFB 15.58deg, #FBF7FF 202.5deg,
                #F9F4FF 344.42deg, #D5BBFB 375.58deg);
        }

        .auth-decor {
            position: absolute;
            width: 353px;
            height: 707px;
            opacity: .8;
            pointer-events: none;
            object-fit: contain;
            z-index: 0;
        }
        .auth-decor--left  { left: -151px; top: -48px; }
        .auth-decor--right { right: -151px; top: 346px; transform: rotate(180deg); }

        .auth-page {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 640px;
            margin: 0 auto;
        }

        .brand {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            margin-bottom: 20px;
        }
        .brand-icon {
            width: 56px;
            height: 55px;
            border-radius: 6px;
            background: linear-gradient(180deg, var(--color-primary) 0%, var(--color-primary-light) 100%);
        }
        .brand-name {
            font-weight: 700;
            font-size: 21px;
            color: var(--color-primary-dark);
            letter-spacing: .02em;
        }

        .auth-wrapper {
            background: #fff;
            border: 1px solid var(--color-primary-light);
            border-radius: 20px;
            padding: 28px clamp(20px, 4vw, 36px) 24px;
        }

        .visually-hidden {
            position: absolute; width: 1px; height: 1px;
            padding: 0; margin: -1px; overflow: hidden;
            clip: rect(0,0,0,0); white-space: nowrap; border: 0;
        }

        .form-group { margin-bottom: 16px; }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            font-size: 17px;
            color: #000;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 11px 14px;
            font-family: inherit;
            font-weight: 300;
            font-size: 15px;
            color: #000;
            background: var(--color-surface);
            border: 1px solid var(--color-primary);
            border-radius: 13px;
            appearance: none;
        }
        .form-group input::placeholder { color: rgba(0,0,0,.3); }
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--color-primary-dark);
            box-shadow: 0 0 0 3px rgba(151,71,255,.15);
        }
        .form-group select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='8' viewBox='0 0 14 8'%3E%3Cpath d='M1 1l6 6 6-6' stroke='%23511F91' stroke-width='1.5' fill='none'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            padding-right: 36px;
        }

        .error-text { color: #dc2626; font-size: 13px; margin-top: 6px; font-weight: 400; }
        .status-text { color: #16a34a; font-size: 14px; margin-bottom: 16px; }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 4px;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 22px;
            border-radius: 13px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            border: 1px solid transparent;
            text-decoration: none;
        }
        .btn-secondary { background: #fff; border-color: var(--color-primary); color: #000; }
        .btn-secondary:hover { background: var(--color-surface); }
        .btn-primary { background: var(--color-primary-light); color: #fff; }
        .btn-primary:hover { background: var(--color-primary); }
        .btn-primary:disabled { opacity: .7; cursor: not-allowed; }

        .auth-footer { text-align: center; margin-top: 18px; font-size: 14px; color: #333; }
        .auth-footer a { color: var(--color-primary-dark); font-weight: 600; text-decoration: none; }
        .auth-footer a:hover { text-decoration: underline; }

        @media (max-width: 560px) {
            .auth-decor { display: none; }
            .form-actions { justify-content: stretch; }
            .btn { flex: 1; justify-content: center; }
        }
    </style>
</head>
<body>
    <img src="{{ asset('images/graphic.png') }}" alt="" class="auth-decor auth-decor--left">
    <img src="{{ asset('images/graphic.png') }}" alt="" class="auth-decor auth-decor--right">

    <div class="auth-page">
        <div class="brand">
            <div class="brand-icon"></div>
            <div class="brand-name">SISTEM</div>
        </div>

        <div class="auth-wrapper">
            @yield('content')
        </div>
    </div>
</body>
</html>
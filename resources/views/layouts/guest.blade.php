<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0f2744">
    <title>@yield('title', 'Sign In') — Capacity Connect</title>
    <link rel="stylesheet" href="/css/app.css?v=20260907-2">
</head>
<body style="background: linear-gradient(135deg, #0b1528 0%, #0f2744 50%, #1e3a8a 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px;">
    <div style="width: 100%; max-width: 480px;">
        <div style="text-align: center; margin-bottom: 28px;">
            <a href="{{ route('home') }}" style="display: inline-flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                <div class="brand-emblem" style="width: 44px; height: 44px; font-size: 20px;">CC</div>
                <h1 style="color: #ffffff; font-size: 22px; font-weight: 800; letter-spacing: -0.5px;">CAPACITY CONNECT</h1>
            </a>
            <p style="color: #94a3b8; font-size: 13px;">National Workforce Capacity & Competency System</p>
        </div>

        <div class="card" style="border: 1px solid rgba(255,255,255,0.15); box-shadow: var(--cc-shadow-lg);">
            <div class="card-body" style="padding: 32px;">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-error">{{ session('error') }}</div>
                @endif
                @if(session('info'))
                    <div class="alert alert-info">{{ session('info') }}</div>
                @endif

                @yield('content')
            </div>
        </div>

        <div style="text-align: center; margin-top: 20px;">
            <a href="{{ route('home') }}" style="color: #94a3b8; font-size: 13px;">← Return to Public Homepage</a>
        </div>
    </div>
</body>
</html>

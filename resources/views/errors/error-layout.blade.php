<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $code }} — Capacity Connect</title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body style="min-height:100vh;display:grid;place-items:center;padding:24px;background:#0a192f;color:#fff;">
    <main style="width:min(100%,620px);text-align:center;">
        <div style="display:inline-flex;align-items:center;gap:10px;color:#6ee7d8;font-size:12px;font-weight:800;letter-spacing:1.5px;text-transform:uppercase;">
            <span class="brand-emblem" style="width:34px;height:34px;font-size:15px;">CC</span>
            Capacity Connect
        </div>
        <p style="margin-top:42px;font-size:clamp(76px,16vw,148px);line-height:.9;font-weight:800;color:#ffad7a;">{{ $code }}</p>
        <h1 style="margin-top:20px;color:#fff;font-size:32px;">{{ $title }}</h1>
        <p style="margin:14px auto 30px;max-width:500px;color:#b9c7d6;font-size:16px;line-height:1.7;">{{ $message }}</p>
        <div style="display:flex;justify-content:center;gap:12px;flex-wrap:wrap;">
            <a href="{{ route('home') }}" class="btn btn-primary">Return Home</a>
            <a href="javascript:history.back()" class="btn btn-secondary" style="background:transparent;color:#fff;border-color:#476078;">Go Back</a>
        </div>
        <p style="margin-top:46px;color:#71869b;font-size:12px;">Technical details are recorded server-side for investigation.</p>
    </main>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Access Denied | CapacityConnect</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #0f172a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            position: relative;
            overflow: hidden;
        }
        body::before {
            content: '';
            position: absolute;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(220, 38, 38, 0.12) 0%, transparent 70%);
            top: -100px; right: -100px;
            border-radius: 50%;
        }
        body::after {
            content: '';
            position: absolute;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.1) 0%, transparent 70%);
            bottom: -80px; left: -80px;
            border-radius: 50%;
        }
        .container {
            text-align: center;
            max-width: 600px;
            position: relative;
            z-index: 1;
        }
        .error-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(220, 38, 38, 0.15);
            border: 1px solid rgba(220, 38, 38, 0.3);
            color: #f87171;
            padding: 6px 16px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 28px;
        }
        .error-badge::before {
            content: '🔒';
            font-size: 14px;
        }
        .error-code {
            font-size: 120px;
            font-weight: 900;
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 16px;
        }
        h1 {
            font-size: 32px;
            font-weight: 800;
            color: #f8fafc;
            margin-bottom: 12px;
        }
        .description {
            font-size: 16px;
            color: #94a3b8;
            line-height: 1.7;
            margin-bottom: 36px;
        }
        .message-box {
            background: #1e293b;
            border: 1px solid #334155;
            border-left: 4px solid #dc2626;
            border-radius: 12px;
            padding: 20px 24px;
            margin-bottom: 36px;
            text-align: left;
        }
        .message-box .label { font-size: 11px; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px; }
        .message-box .message-text { font-size: 14px; color: #e2e8f0; }
        .actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
            border: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #fff;
            box-shadow: 0 4px 12px rgba(37,99,235,0.3);
        }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(37,99,235,0.4); }
        .btn-secondary {
            background: #1e293b;
            color: #94a3b8;
            border: 1px solid #334155;
        }
        .btn-secondary:hover { color: #f8fafc; border-color: #475569; }
        .divider {
            height: 1px;
            background: #1e293b;
            margin: 36px 0;
        }
        .role-info {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            text-align: center;
        }
        .role-card {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 10px;
            padding: 16px 12px;
        }
        .role-card .icon { font-size: 24px; margin-bottom: 6px; }
        .role-card .name { font-size: 12px; font-weight: 700; color: #f8fafc; margin-bottom: 2px; }
        .role-card .portal { font-size: 11px; color: #64748b; }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-badge">Security Violation Detected</div>
        <div class="error-code">403</div>
        <h1>Access Denied</h1>
        <p class="description">
            You do not have permission to access this area. This attempt has been logged.<br>
            Please return to your authorised portal.
        </p>

        <div class="message-box">
            <div class="label">Security Notice</div>
            <div class="message-text">Your account does not have the required permission for this resource.</div>
        </div>

        <div class="actions">
            @auth
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">← Admin Dashboard</a>
                @elseif(Auth::user()->isTrainer())
                    <a href="{{ route('trainer.dashboard') }}" class="btn btn-primary">← Trainer Portal</a>
                @else
                    <a href="{{ route('trainee.dashboard') }}" class="btn btn-primary">← My Dashboard</a>
                @endif
            @else
                <a href="{{ route('login') }}" class="btn btn-primary">← Login</a>
            @endauth
            <a href="javascript:history.back()" class="btn btn-secondary">← Go Back</a>
        </div>

        <div class="divider"></div>

        <p style="font-size: 12px; color: #475569; margin-bottom: 16px;">Access is restricted to your authorised role area:</p>
        <div class="role-info">
            <div class="role-card">
                <div class="icon">🎓</div>
                <div class="name">Trainee</div>
                <div class="portal">/trainee/*</div>
            </div>
            <div class="role-card">
                <div class="icon">👩‍🏫</div>
                <div class="name">Trainer</div>
                <div class="portal">/trainer/*</div>
            </div>
            <div class="role-card">
                <div class="icon">⚙️</div>
                <div class="name">Admin</div>
                <div class="portal">/admin/*</div>
            </div>
        </div>
    </div>
</body>
</html>

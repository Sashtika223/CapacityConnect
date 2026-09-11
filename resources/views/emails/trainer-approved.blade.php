<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Capacity Connect — Trainer Registration Approved</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            padding: 40px 16px;
        }
        .wrapper { max-width: 600px; margin: 0 auto; }
        .card {
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }
        .header {
            background: linear-gradient(135deg, #1d4ed8 0%, #0ea5e9 100%);
            padding: 40px 48px 36px;
            text-align: center;
        }
        .header-logo {
            font-size: 13px;
            font-weight: 800;
            color: rgba(255,255,255,0.7);
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        .header h1 {
            font-size: 24px;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 8px;
        }
        .header p {
            font-size: 14px;
            color: rgba(255,255,255,0.8);
        }
        .status-badge {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.4);
            color: #ffffff;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1px;
            padding: 6px 18px;
            border-radius: 20px;
            margin-top: 16px;
            text-transform: uppercase;
        }
        .body { padding: 40px 48px; }
        .greeting {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 20px;
        }
        .para {
            font-size: 15px;
            color: #334155;
            line-height: 1.8;
            margin-bottom: 18px;
        }
        .highlight-box {
            background: #f0fdf4;
            border: 1px solid #86efac;
            border-left: 4px solid #22c55e;
            border-radius: 8px;
            padding: 18px 20px;
            margin: 24px 0;
        }
        .highlight-box p {
            font-size: 14px;
            color: #15803d;
            font-weight: 600;
            line-height: 1.6;
        }
        .cta-wrapper { text-align: center; margin: 32px 0; }
        .cta-btn {
            display: inline-block;
            background: linear-gradient(135deg, #1d4ed8 0%, #0ea5e9 100%);
            color: #ffffff !important;
            text-decoration: none;
            font-size: 15px;
            font-weight: 700;
            padding: 16px 40px;
            border-radius: 10px;
            letter-spacing: 0.3px;
        }
        .login-url {
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            margin-top: 10px;
        }
        .login-url a { color: #0ea5e9; }
        .divider {
            border: none;
            border-top: 1px solid #e2e8f0;
            margin: 28px 0;
        }
        .footer-note {
            font-size: 13px;
            color: #64748b;
            line-height: 1.7;
        }
        .footer {
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 24px 48px;
            text-align: center;
        }
        .footer .brand {
            font-size: 13px;
            font-weight: 800;
            color: #1d4ed8;
            margin-bottom: 4px;
        }
        .footer p {
            font-size: 12px;
            color: #94a3b8;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">

            {{-- Header --}}
            <div class="header">
                <div class="header-logo">Capacity Connect</div>
                <h1>Trainer Registration Approved</h1>
                <p>Disaster Management Training Platform</p>
                <div class="status-badge">✓ Approved</div>
            </div>

            {{-- Body --}}
            <div class="body">
                <p class="greeting">Dear {{ $trainer->name }},</p>

                <p class="para">
                    Your request to join the <strong>Capacity Connect Portal</strong> as a Trainer has been
                    reviewed and <strong style="color:#15803d;">approved</strong> by the administrator.
                </p>

                <div class="highlight-box">
                    <p>✅ Your trainer account is now active. You can log in to the Capacity Connect Portal using your registered email address and password.</p>
                </div>

                <p class="para">
                    As an approved trainer on Capacity Connect, you can now:
                </p>

                <ul style="color:#475569; font-size:14px; line-height:2.2; padding-left:22px; margin-bottom:24px;">
                    <li>Create and publish disaster management training courses</li>
                    <li>Build curriculum modules, lessons, and assessments</li>
                    <li>Manage trainee enrollments and monitor progress</li>
                    <li>Issue course completion certificates</li>
                </ul>

                <p class="para"><strong>Login Portal:</strong></p>

                <div class="cta-wrapper">
                    <a href="{{ route('login') }}" class="cta-btn">Login to Capacity Connect</a>
                    <div class="login-url">
                        Or visit: <a href="{{ route('login') }}">{{ route('login') }}</a>
                    </div>
                </div>

                <hr class="divider">

                <p class="footer-note">
                    Your registered email address is <strong>{{ $trainer->email }}</strong>.
                    Use it along with your password to sign in. If you have forgotten your password,
                    use the <em>Forgot Password</em> option on the login page.
                    <br><br>
                    <strong>Do not reply to this email.</strong> If you did not register for this platform
                    or believe this email was sent in error, please disregard it.
                </p>
            </div>

            {{-- Footer --}}
            <div class="footer">
                <p class="brand">Capacity Connect</p>
                <p>State Emergency Response &amp; Disaster Management Training System</p>
                <p style="margin-top:8px;">Thank you,<br><strong>Capacity Connect Team</strong></p>
                <p style="margin-top:12px; font-size:11px; color:#cbd5e1;">© {{ date('Y') }} Capacity Connect. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>

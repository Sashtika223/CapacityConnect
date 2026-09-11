<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0f2744">
    <link rel="manifest" href="/manifest.json">
    <title>@yield('title', 'Capacity Connect — Digital Capacity Building & Competency Platform')</title>
    <link rel="stylesheet" href="/css/app.css?v=20260907-2">
    @stack('styles')
</head>
<body>
    <!-- Top Institutional Banner -->
    <header class="public-header">
        <div class="public-nav-container">
            <a href="{{ route('home') }}" style="display: flex; align-items: center; gap: 12px;">
                <div class="brand-emblem">CC</div>
                <div class="brand-text">
                    <h2>CAPACITY CONNECT</h2>
                    <span>National Workforce LMS</span>
                </div>
            </a>

            <nav class="public-nav-links">
                <a href="{{ route('home') }}" class="public-nav-link">Home</a>
                <a href="{{ route('courses.index') }}" class="public-nav-link">Course Catalog</a>
                <a href="{{ route('search') }}" class="public-nav-link">Search</a>
                <a href="{{ route('certificate.verify') }}" class="public-nav-link">Verify Certificate</a>
                
                <div id="pwa-status-pill" class="status-pill">
                    <span class="status-indicator-dot dot-online"></span>
                    <span id="pwa-status-text">Online</span>
                </div>

                @auth
                    @if(Auth::user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-sm">Admin Dashboard</a>
                    @elseif(Auth::user()->isTrainer())
                        <a href="{{ route('trainer.dashboard') }}" class="btn btn-primary btn-sm">Trainer Portal</a>
                    @else
                        <a href="{{ route('trainee.dashboard') }}" class="btn btn-primary btn-sm">Trainee Dashboard</a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn btn-secondary btn-sm">Sign In</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Register</a>
                @endauth
            </nav>
        </div>
    </header>

    <main>
        @if(session('success'))
            <div class="app-container" style="padding-top: 16px; padding-bottom: 0;">
                <div class="alert alert-success">{{ session('success') }}</div>
            </div>
        @endif
        @if(session('error'))
            <div class="app-container" style="padding-top: 16px; padding-bottom: 0;">
                <div class="alert alert-error">{{ session('error') }}</div>
            </div>
        @endif
        @if(session('info'))
            <div class="app-container" style="padding-top: 16px; padding-bottom: 0;">
                <div class="alert alert-info">{{ session('info') }}</div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Institutional Footer -->
    <footer style="background: #080f1e; color: #94a3b8; padding: 60px 24px 30px; border-top: 1px solid rgba(255,255,255,0.08);">
        <div style="max-width: 1280px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 40px; margin-bottom: 40px;">
            <div>
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                    <div class="brand-emblem" style="width: 32px; height: 32px; font-size: 14px;">CC</div>
                    <h3 style="color: #fff; font-size: 16px;">CAPACITY CONNECT</h3>
                </div>
                <p style="font-size: 13px; line-height: 1.6;">Integrated national platform for digital capacity building, workforce competency tracking, emergency disaster preparedness, and verifiable digital certification.</p>
            </div>
            <div>
                <h4 style="color: #fff; font-size: 14px; margin-bottom: 16px; text-transform: uppercase; letter-spacing: 0.8px;">Quick Links</h4>
                <ul style="list-style: none; font-size: 13px; display: flex; flex-direction: column; gap: 8px;">
                    <li><a href="{{ route('courses.index') }}" style="color: #94a3b8;">All Courses</a></li>
                    <li><a href="{{ route('certificate.verify') }}" style="color: #94a3b8;">Verify Digital Certificate</a></li>
                    <li><a href="{{ route('login') }}" style="color: #94a3b8;">Personnel Sign In</a></li>
                    <li><a href="{{ route('register') }}" style="color: #94a3b8;">New Trainee Registration</a></li>
                </ul>
            </div>
            <div>
                <h4 style="color: #fff; font-size: 14px; margin-bottom: 16px; text-transform: uppercase; letter-spacing: 0.8px;">Governance & Security</h4>
                <p style="font-size: 13px; line-height: 1.6;">Encrypted records with SHA-256 verifiable signatures, strict role-based access control, and complete audit logging.</p>
            </div>
        </div>
        <div style="max-width: 1280px; margin: 0 auto; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.06); text-align: center; font-size: 12px;">
            &copy; {{ date('Y') }} CAPACITY CONNECT Platform. Institutional Enterprise Grade Edition. All rights reserved.
        </div>
    </footer>

    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js').catch(err => console.log('SW Registration error:', err));
        }
        function updateOnlineStatus() {
            const pill = document.getElementById('pwa-status-pill');
            const dot = pill?.querySelector('.status-indicator-dot');
            const text = document.getElementById('pwa-status-text');
            if (navigator.onLine) {
                dot.className = 'status-indicator-dot dot-online';
                if(text) text.innerText = 'Online';
            } else {
                dot.className = 'status-indicator-dot dot-offline';
                if(text) text.innerText = 'Offline';
            }
        }
        window.addEventListener('online', updateOnlineStatus);
        window.addEventListener('offline', updateOnlineStatus);
        updateOnlineStatus();
    </script>
    @stack('scripts')
</body>
</html>

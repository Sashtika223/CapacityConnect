<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0f2744">
    <title>@yield('title', 'Trainee Portal') — Capacity Connect</title>
    <link rel="stylesheet" href="/css/app.css?v=20260907-2">
    @stack('styles')
</head>
<body>
    <div class="app-shell">
        <!-- Trainee Sidebar -->
        <aside class="app-sidebar" id="app-sidebar">
            <div class="sidebar-brand">
                <div class="brand-emblem">CC</div>
                <div class="brand-text">
                    <h2>CAPACITY CONNECT</h2>
                    <span>Trainee Learning Portal</span>
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section-title">Core Learning</div>
                <a href="{{ route('trainee.dashboard') }}" class="nav-item {{ request()->routeIs('trainee.dashboard') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('trainee.learning.index') }}" class="nav-item {{ request()->routeIs('trainee.learning.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    My Learning
                </a>
                <a href="{{ route('courses.index') }}" class="nav-item {{ request()->routeIs('courses.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Course Catalog
                </a>
                <a href="{{ route('trainee.assessments.index') }}" class="nav-item {{ request()->routeIs('trainee.assessments.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Assessments
                </a>

                <div class="nav-section-title">Competency & Simulations</div>
                <a href="{{ route('trainee.digital-twin') }}" class="nav-item {{ request()->routeIs('trainee.digital-twin') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Digital Twin
                </a>
                <a href="{{ route('trainee.simulations.index') }}" class="nav-item {{ request()->routeIs('trainee.simulations.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Disaster Simulations
                </a>
                <a href="{{ route('trainee.certificates.index') }}" class="nav-item {{ request()->routeIs('trainee.certificates.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    My Certificates
                </a>

                <div class="nav-section-title">AI & Support</div>
                <a href="{{ route('trainee.ai.assistant') }}" class="nav-item {{ request()->routeIs('trainee.ai.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    AI Learning Assistant
                </a>
                <a href="{{ route('trainee.profile') }}" class="nav-item {{ request()->routeIs('trainee.profile') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    My Profile
                </a>
            </nav>

            <div class="sidebar-user">
                <div class="user-avatar-sm">{{ substr(Auth::user()->name, 0, 2) }}</div>
                <div class="user-meta-sm">
                    <p>{{ Auth::user()->name }}</p>
                    <span>{{ Auth::user()->role }}</span>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" style="background: none; border: none; color: #94a3b8; cursor: pointer;" title="Sign out">
                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Body -->
        <div class="app-main">
            <header class="top-navbar">
                <div class="topbar-left">
                    <button class="mobile-menu-btn" onclick="document.getElementById('app-sidebar').classList.toggle('open')">☰</button>
                    <span class="portal-badge trainee">Trainee</span>
                    <span style="font-size: 13px; color: var(--cc-text-muted);">{{ Auth::user()->department?->name ?? 'National Workforce Trainee' }}</span>
                </div>
                <div class="topbar-right">
                    <div id="pwa-status-pill" class="status-pill">
                        <span class="status-indicator-dot dot-online"></span>
                        <span id="pwa-status-text">Online</span>
                    </div>
                </div>
            </header>

            <div class="app-container">
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
    </div>

    <script>
        function updateOnlineStatus() {
            const pill = document.getElementById('pwa-status-pill');
            const dot = pill?.querySelector('.status-indicator-dot');
            const text = document.getElementById('pwa-status-text');
            if (navigator.onLine) {
                if(dot) dot.className = 'status-indicator-dot dot-online';
                if(text) text.innerText = 'Online';
            } else {
                if(dot) dot.className = 'status-indicator-dot dot-offline';
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Trainer Portal') — Capacity Connect</title>
    <link rel="stylesheet" href="/css/app.css?v=20260907-2">
    @stack('styles')
</head>
<body>
    <div class="app-shell">
        <!-- Trainer Sidebar -->
        <aside class="app-sidebar" id="app-sidebar">
            <div class="sidebar-brand">
                <div class="brand-emblem" style="background: linear-gradient(135deg, #4f46e5, #0d9488);">CC</div>
                <div class="brand-text">
                    <h2>CAPACITY CONNECT</h2>
                    <span>Trainer Authoring Portal</span>
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section-title">Instruction</div>
                <a href="{{ route('trainer.dashboard') }}" class="nav-item {{ request()->routeIs('trainer.dashboard') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('trainer.courses.index') }}" class="nav-item {{ request()->routeIs('trainer.courses.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    Course Management
                </a>
                <a href="{{ route('trainer.courses.create') }}" class="nav-item {{ request()->routeIs('trainer.courses.create') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"/></svg>
                    Create New Course
                </a>

                <div class="nav-section-title">Evaluation & Monitoring</div>
                <a href="{{ route('trainer.submissions.index') }}" class="nav-item {{ request()->routeIs('trainer.submissions.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    Practical Submissions
                </a>
                <a href="{{ route('trainer.analytics') }}" class="nav-item {{ request()->routeIs('trainer.analytics') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Trainer Analytics
                </a>
            </nav>

            <div class="sidebar-user">
                <div class="user-avatar-sm" style="background: #4338ca;">{{ substr(Auth::user()->name, 0, 2) }}</div>
                <div class="user-meta-sm">
                    <p>{{ Auth::user()->name }}</p>
                    <span>Trainer & Author</span>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" style="background: none; border: none; color: #94a3b8; cursor: pointer;">
                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="app-main">
            <header class="top-navbar">
                <div class="topbar-left">
                    <button class="mobile-menu-btn" onclick="document.getElementById('app-sidebar').classList.toggle('open')">☰</button>
                    <span class="portal-badge trainer">Instructor</span>
                    <span style="font-size: 13px; color: var(--cc-text-muted);">{{ Auth::user()->profile?->job_title ?? 'Master Instructor' }}</span>
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
    @stack('scripts')
</body>
</html>

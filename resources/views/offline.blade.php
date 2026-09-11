@extends('layouts.public')

@section('title', 'Offline Mode — Capacity Connect')

@section('content')
<div class="app-container" style="max-width: 650px; padding-top: 80px; padding-bottom: 80px; text-align: center;">
    <div class="card" style="padding: 48px 32px;">
        <div style="font-size: 50px; margin-bottom: 16px;">📡</div>
        <h1 style="font-size: 26px; font-weight: 800; color: var(--cc-primary-dark); margin-bottom: 12px;">You are Currently Offline</h1>
        <p style="color: var(--cc-text-muted); font-size: 15px; line-height: 1.6; margin-bottom: 28px;">Capacity Connect PWA has cached your active courses and core application assets. You can continue reading cached course materials without network connection.</p>
        <div style="display: flex; gap: 12px; justify-content: center;">
            <a href="/" class="btn btn-secondary">Retry Connection</a>
            <a href="/trainee/my-learning" class="btn btn-primary">Open Cached Learning</a>
        </div>
    </div>
</div>
@endsection

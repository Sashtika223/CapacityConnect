@extends('layouts.guest')

@section('title', 'Sign In — Capacity Connect')

@section('content')

{{-- Flash messages --}}
@if(session('success'))
    <div style="background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.4); color: #34d399; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 13px; font-weight: 600;">
        ✅ {{ session('success') }}
    </div>
@endif
@if(session('info'))
    <div style="background: rgba(56,189,248,0.12); border: 1px solid rgba(56,189,248,0.4); color: #38bdf8; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 13px; font-weight: 600;">
        ℹ️ {{ session('info') }}
    </div>
@endif
@if($errors->has('email') && !$errors->has('password'))
    <div style="background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.4); color: #f87171; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 13px; font-weight: 600;">
        ⚠️ {{ $errors->first('email') }}
    </div>
@endif

<div style="margin-bottom: 24px;">
    <h2 style="font-size: 20px; font-weight: 700; color: var(--cc-primary-dark); margin-bottom: 6px;">Personnel Sign In</h2>
    <p style="font-size: 13px; color: var(--cc-text-muted);">Access your authorized portal (Trainee, Trainer, or Admin).</p>
</div>

<form action="{{ route('login') }}" method="POST">
    @csrf

    <div class="form-group">
        <label for="email" class="form-label">Official Email Address</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus placeholder="name@agency.gov">
        @error('email')
            <p style="color: var(--cc-rose); font-size: 12px; margin-top: 4px;">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-group">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
            <label for="password" class="form-label" style="margin-bottom: 0;">Password</label>
            <a href="{{ route('password.request') }}" style="font-size: 12px; color: var(--cc-sky);">Forgot password?</a>
        </div>
        <input type="password" id="password" name="password" class="form-control" required placeholder="••••••••">
        @error('password')
            <p style="color: var(--cc-rose); font-size: 12px; margin-top: 4px;">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-group" style="display: flex; align-items: center; gap: 8px;">
        <input type="checkbox" id="remember" name="remember" value="1">
        <label for="remember" style="font-size: 13px; color: var(--cc-text-muted); cursor: pointer;">Keep me securely logged in on this terminal</label>
    </div>

    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px;">Sign In to Portal</button>
</form>

<div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--cc-border); text-align: center; font-size: 13px; color: var(--cc-text-muted);">
    Don't have an active account? <a href="{{ route('register') }}" style="font-weight: 600;">Register here</a>
</div>

<div style="margin-top: 20px; background: rgba(15,23,42,0.6); padding: 14px 16px; border-radius: 8px; border: 1px dashed #334155; font-size: 11px; color: #64748b;">
    <strong style="color: #94a3b8;">🔑 Admin Test Credentials:</strong><br>
    Email: <code style="color: #38bdf8;">admin@capacityconnect.com</code> &nbsp;|&nbsp; Password: <code style="color: #38bdf8;">Admin@1234</code>
</div>
@endsection

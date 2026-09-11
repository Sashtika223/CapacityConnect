@extends('layouts.public')

@section('title', 'Forgot Password — Capacity Connect')

@section('content')
<div class="auth-card" style="max-width: 440px; margin: 60px auto; padding: 36px; background: #1e293b; border-radius: 16px; border: 1px solid #334155; color: #fff;">
    <div style="text-align: center; margin-bottom: 24px;">
        <span style="font-size: 36px;">🔐</span>
        <h1 style="font-size: 24px; font-weight: 800; color: #f8fafc; margin: 8px 0 4px 0;">Reset Password</h1>
        <p style="font-size: 13px; color: #94a3b8; margin: 0;">Enter your registered government email to receive password reset instructions.</p>
    </div>

    @if (session('status'))
        <div style="background: rgba(16,185,129,0.15); border: 1px solid #10b981; color: #34d399; padding: 12px; border-radius: 8px; font-size: 13px; margin-bottom: 20px;">
            {{ session('status') }}
        </div>
    @endif

    <form action="{{ route('password.email') }}" method="POST">
        @csrf

        <div style="margin-bottom: 20px;">
            <label style="display: block; font-size: 13px; font-weight: 600; color: #cbd5e1; margin-bottom: 6px;">Email Address</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus class="form-control" style="width: 100%; padding: 10px 14px; background: #0f172a; border: 1px solid #334155; border-radius: 8px; color: #fff;" placeholder="name@capacityconnect.gov">
            @error('email')
                <span style="color: #f87171; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; background: #2563eb; color: #fff; font-weight: 700; border-radius: 8px; border: none; cursor: pointer;">
            Send Password Reset Link ➔
        </button>
    </form>

    <div style="text-align: center; margin-top: 20px; font-size: 13px;">
        <a href="{{ route('login') }}" style="color: #38bdf8; text-decoration: none;">← Back to Login</a>
    </div>
</div>
@endsection

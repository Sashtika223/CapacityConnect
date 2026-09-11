@extends('layouts.public')

@section('title', 'Two-Factor Authentication — Capacity Connect')

@section('content')
<div class="auth-card" style="max-width: 440px; margin: 60px auto; padding: 36px; background: #1e293b; border-radius: 16px; border: 1px solid #334155; color: #fff;">
    <div style="text-align: center; margin-bottom: 24px;">
        <span style="font-size: 36px;">📱</span>
        <h1 style="font-size: 24px; font-weight: 800; color: #f8fafc; margin: 8px 0 4px 0;">Two-Factor Verification</h1>
        <p style="font-size: 13px; color: #94a3b8; margin: 0;">Enter the 6-digit one-time verification passcode sent to your registered device/email.</p>
    </div>

    <div style="background: rgba(59,130,246,0.15); border: 1px solid #3b82f6; color: #93c5fd; padding: 12px; border-radius: 8px; font-size: 12px; margin-bottom: 20px; text-align: center;">
        💡 Demo Test Passcode: <strong style="color: #fff; font-family: monospace; font-size: 14px;">123456</strong>
    </div>

    <form action="{{ route('two-factor.verify') }}" method="POST">
        @csrf

        <div style="margin-bottom: 20px;">
            <label style="display: block; font-size: 13px; font-weight: 600; color: #cbd5e1; margin-bottom: 6px;">6-Digit OTP Passcode</label>
            <input type="text" name="code" required autofocus maxlength="6" class="form-control" style="width: 100%; padding: 12px 14px; background: #0f172a; border: 1px solid #334155; border-radius: 8px; color: #fff; font-size: 22px; text-align: center; letter-spacing: 8px; font-family: monospace;" placeholder="123456">
            @error('code')
                <span style="color: #f87171; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; background: #2563eb; color: #fff; font-weight: 700; border-radius: 8px; border: none; cursor: pointer;">
            Verify Passcode & Access Dashboard ➔
        </button>
    </form>
</div>
@endsection

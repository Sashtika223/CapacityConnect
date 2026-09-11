@extends('layouts.public')

@section('title', 'Set New Password — Capacity Connect')

@section('content')
<div class="auth-card" style="max-width: 440px; margin: 60px auto; padding: 36px; background: #1e293b; border-radius: 16px; border: 1px solid #334155; color: #fff;">
    <div style="text-align: center; margin-bottom: 24px;">
        <span style="font-size: 36px;">🔑</span>
        <h1 style="font-size: 24px; font-weight: 800; color: #f8fafc; margin: 8px 0 4px 0;">Create New Password</h1>
        <p style="font-size: 13px; color: #94a3b8; margin: 0;">Enter your new secure password below.</p>
    </div>

    <form action="{{ route('password.update') }}" method="POST">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div style="margin-bottom: 16px;">
            <label style="display: block; font-size: 13px; font-weight: 600; color: #cbd5e1; margin-bottom: 6px;">Email Address</label>
            <input type="email" name="email" value="{{ $email ?? old('email') }}" required class="form-control" style="width: 100%; padding: 10px 14px; background: #0f172a; border: 1px solid #334155; border-radius: 8px; color: #fff;">
            @error('email')
                <span style="color: #f87171; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
            @enderror
        </div>

        <div style="margin-bottom: 16px;">
            <label style="display: block; font-size: 13px; font-weight: 600; color: #cbd5e1; margin-bottom: 6px;">New Password</label>
            <input type="password" name="password" required class="form-control" style="width: 100%; padding: 10px 14px; background: #0f172a; border: 1px solid #334155; border-radius: 8px; color: #fff;" placeholder="••••••••">
            @error('password')
                <span style="color: #f87171; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
            @enderror
        </div>

        <div style="margin-bottom: 24px;">
            <label style="display: block; font-size: 13px; font-weight: 600; color: #cbd5e1; margin-bottom: 6px;">Confirm New Password</label>
            <input type="password" name="password_confirmation" required class="form-control" style="width: 100%; padding: 10px 14px; background: #0f172a; border: 1px solid #334155; border-radius: 8px; color: #fff;" placeholder="••••••••">
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; background: #2563eb; color: #fff; font-weight: 700; border-radius: 8px; border: none; cursor: pointer;">
            Update Password & Login ➔
        </button>
    </form>
</div>
@endsection

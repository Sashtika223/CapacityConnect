@extends('layouts.public')

@section('title', 'Notification Preferences')

@section('content')
<div class="app-container" style="max-width: 760px; padding-top: 48px; padding-bottom: 64px;">
    <h1>Notification Preferences</h1>
    <p style="color: var(--cc-text-muted);">Choose which platform updates you receive in the app and by email.</p>
    <form method="POST" action="{{ route('notifications.preferences.update') }}" class="card" style="padding: 24px; margin-top: 24px;">
        @csrf
        @method('PUT')
        @foreach([
            'in_app_enabled' => 'In-app notifications', 'email_enabled' => 'Email notifications',
            'course_updates' => 'Course updates and enrollment', 'assessment_results' => 'Assessment results',
            'certificate_updates' => 'Certificate issued and deadline alerts', 'announcements' => 'Announcements',
            'recommendations' => 'Skill-gap recommendations', 'admin_alerts' => 'Administrative alerts',
        ] as $key => $label)
            <label style="display:flex; gap:10px; align-items:center; padding:12px 0; border-bottom:1px solid var(--cc-border);">
                <input type="hidden" name="{{ $key }}" value="0">
                <input type="checkbox" name="{{ $key }}" value="1" @checked($preferences->{$key})>
                <span>{{ $label }}</span>
            </label>
        @endforeach
        <button type="submit" class="btn btn-primary" style="margin-top:20px;">Save Preferences</button>
    </form>
</div>
@endsection
@extends('layouts.guest')

@section('title', 'Register — Capacity Connect')

@section('content')
<div style="margin-bottom: 24px;">
    <h2 style="font-size: 20px; font-weight: 700; color: var(--cc-primary-dark); margin-bottom: 6px;">Workforce Registration</h2>
    <p style="font-size: 13px; color: var(--cc-text-muted);">Join the national capacity building and disaster competency network.</p>
</div>

<form action="{{ route('register') }}" method="POST">
    @csrf

    <div class="form-group">
        <label for="name" class="form-label">Full Name & Title</label>
        <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-control" required placeholder="e.g. Inspector Ramesh Verma">
        @error('name')
            <p style="color: var(--cc-rose); font-size: 12px; margin-top: 4px;">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-group">
        <label for="email" class="form-label">Official Email Address</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control" required placeholder="name@agency.gov">
        @error('email')
            <p style="color: var(--cc-rose); font-size: 12px; margin-top: 4px;">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-group">
        <label for="role" class="form-label">Platform Role</label>
        <select id="role" name="role" class="form-select" required onchange="toggleTrainerNotice(this.value)">
            <option value="trainee" {{ old('role') === 'trainee' ? 'selected' : '' }}>Trainee (Emergency Officer / Learner)</option>
            <option value="trainer" {{ old('role') === 'trainer' ? 'selected' : '' }}>Trainer (Instructor / Subject Expert — Requires Admin Approval)</option>
        </select>
        @error('role')
            <p style="color: var(--cc-rose); font-size: 12px; margin-top: 4px;">{{ $message }}</p>
        @enderror
        <div id="trainerNotice" style="display: {{ old('role') === 'trainer' ? 'flex' : 'none' }}; align-items: flex-start; gap: 8px; margin-top: 8px; background: rgba(251,191,36,0.12); border: 1px solid rgba(251,191,36,0.4); color: #fbbf24; padding: 10px 14px; border-radius: 8px; font-size: 12px; line-height: 1.5;">
            <span style="font-size: 14px;">ℹ️</span>
            <span><strong>Notice for Trainers:</strong> Trainer accounts require administrator approval before login access is enabled.</span>
        </div>
    </div>

    <div class="form-group">
        <label for="department_id" class="form-label">Department / Wing</label>
        <select id="department_id" name="department_id" class="form-select">
            <option value="">-- Select Department --</option>
            @foreach($departments as $dept)
                <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
            @endforeach
        </select>
        @error('department_id')
            <p style="color: var(--cc-rose); font-size: 12px; margin-top: 4px;">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-group">
        <label for="password" class="form-label">Password</label>
        <input type="password" id="password" name="password" class="form-control" required placeholder="Minimum 8 characters">
        @error('password')
            <p style="color: var(--cc-rose); font-size: 12px; margin-top: 4px;">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-group">
        <label for="password_confirmation" class="form-label">Confirm Password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required placeholder="Re-enter password">
    </div>

    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px;">Complete Registration</button>
</form>

<div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--cc-border); text-align: center; font-size: 13px; color: var(--cc-text-muted);">
    Already registered? <a href="{{ route('login') }}" style="font-weight: 600;">Sign in here</a>
</div>

<script>
function toggleTrainerNotice(role) {
    const notice = document.getElementById('trainerNotice');
    if (notice) {
        notice.style.display = (role === 'trainer') ? 'flex' : 'none';
    }
}
</script>
@endsection

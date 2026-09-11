@extends('layouts.trainee')

@section('title', 'My Profile & Work History')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">
    <!-- Top Header -->
    <div style="background: #1e293b; padding: 24px; border-radius: 14px; border: 1px solid #334155; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 24px; font-weight: 700; color: #f8fafc; margin: 0 0 4px 0;">My Profile & Personnel Records</h1>
            <p style="font-size: 14px; color: #94a3b8; margin: 0;">Manage your official disaster response credentials, qualifications, and emergency unit deployment assignment.</p>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <!-- Left: Profile Form & Qualifications -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            <!-- Profile Details Card -->
            <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 24px;">
                <h3 style="font-size: 18px; font-weight: 700; color: #f8fafc; margin: 0 0 20px 0;">
                    👤 Personal & Department Info
                </h3>

                <form action="{{ route('trainee.profile.update') }}" method="POST" style="display: flex; flex-direction: column; gap: 16px;">
                    @csrf
                    @method('PUT')

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div>
                            <label style="font-size: 13px; color: #cbd5e1; font-weight: 600; display: block; margin-bottom: 6px;">Full Name</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required style="width: 100%; background: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 10px 14px; color: #fff; font-size: 14px;">
                        </div>
                        <div>
                            <label style="font-size: 13px; color: #cbd5e1; font-weight: 600; display: block; margin-bottom: 6px;">Phone Number</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" style="width: 100%; background: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 10px 14px; color: #fff; font-size: 14px;">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div>
                            <label style="font-size: 13px; color: #cbd5e1; font-weight: 600; display: block; margin-bottom: 6px;">Department</label>
                            <select name="department_id" style="width: 100%; background: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 10px 14px; color: #fff; font-size: 14px;">
                                <option value="">Select Department...</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ $user->department_id == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="font-size: 13px; color: #cbd5e1; font-weight: 600; display: block; margin-bottom: 6px;">Job Title / Role</label>
                            <input type="text" name="job_title" value="{{ old('job_title', $user->profile?->job_title) }}" style="width: 100%; background: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 10px 14px; color: #fff; font-size: 14px;">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div>
                            <label style="font-size: 13px; color: #cbd5e1; font-weight: 600; display: block; margin-bottom: 6px;">Location / Base Station</label>
                            <input type="text" name="location" value="{{ old('location', $user->profile?->location) }}" style="width: 100%; background: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 10px 14px; color: #fff; font-size: 14px;">
                        </div>
                        <div>
                            <label style="font-size: 13px; color: #cbd5e1; font-weight: 600; display: block; margin-bottom: 6px;">Emergency Deployment Unit</label>
                            <input type="text" name="emergency_unit" value="{{ old('emergency_unit', $user->profile?->emergency_unit) }}" style="width: 100%; background: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 10px 14px; color: #fff; font-size: 14px;">
                        </div>
                    </div>

                    <div>
                        <label style="font-size: 13px; color: #cbd5e1; font-weight: 600; display: block; margin-bottom: 6px;">Bio & Professional Overview</label>
                        <textarea name="bio" rows="3" style="width: 100%; background: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 10px 14px; color: #fff; font-size: 14px;">{{ old('bio', $user->profile?->bio) }}</textarea>
                    </div>

                    <div>
                        <button type="submit" class="btn btn-primary" style="background: #2563eb; color: #fff; border: none; padding: 10px 24px; border-radius: 8px; font-weight: 700; font-size: 14px; cursor: pointer;">
                            Save Profile Changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- Skills & Competencies -->
            <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <h3 style="font-size: 18px; font-weight: 700; color: #f8fafc; margin: 0;">
                        ⚡ Skills & Technical Proficiencies
                    </h3>
                </div>

                <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 20px;">
                    @forelse($user->skills as $skill)
                        <span style="display: inline-flex; align-items: center; gap: 6px; background: rgba(59, 130, 246, 0.15); border: 1px solid rgba(59, 130, 246, 0.3); color: #60a5fa; padding: 6px 12px; border-radius: 9999px; font-size: 12px; font-weight: 600;">
                            <span>{{ $skill->name }}</span>
                            <span style="background: #2563eb; color: #fff; font-size: 10px; padding: 2px 6px; border-radius: 9999px; text-transform: uppercase;">
                                {{ $skill->pivot->proficiency ?? 'Standard' }}
                            </span>
                        </span>
                    @empty
                        <p style="font-size: 12px; color: #64748b; margin: 0;">No skills added yet. Select from below to link competencies.</p>
                    @endforelse
                </div>

                <form action="{{ route('trainee.profile.skills.add') }}" method="POST" style="background: #0f172a; padding: 16px; border-radius: 10px; border: 1px solid #334155; display: flex; gap: 10px; align-items: flex-end; flex-wrap: wrap;">
                    @csrf
                    <div style="flex: 2; min-width: 180px;">
                        <label style="font-size: 12px; color: #94a3b8; display: block; margin-bottom: 4px;">Select Skill</label>
                        <select name="skill_id" required style="width: 100%; background: #1e293b; border: 1px solid #334155; border-radius: 6px; padding: 8px 12px; color: #fff; font-size: 13px;">
                            <option value="">Choose a skill...</option>
                            @foreach($availableSkills as $s)
                                <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->category ?? 'General' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="flex: 1; min-width: 130px;">
                        <label style="font-size: 12px; color: #94a3b8; display: block; margin-bottom: 4px;">Proficiency</label>
                        <select name="proficiency" required style="width: 100%; background: #1e293b; border: 1px solid #334155; border-radius: 6px; padding: 8px 12px; color: #fff; font-size: 13px;">
                            <option value="beginner">Beginner</option>
                            <option value="intermediate" selected>Intermediate</option>
                            <option value="advanced">Advanced</option>
                            <option value="expert">Expert</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" style="background: #2563eb; color: #fff; border: none; padding: 9px 18px; border-radius: 6px; font-weight: 600; font-size: 13px; cursor: pointer;">
                        + Add Skill
                    </button>
                </form>
            </div>

            <!-- Qualifications List & Add Form -->
            <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 24px;">
                <h3 style="font-size: 18px; font-weight: 700; color: #f8fafc; margin: 0 0 16px 0;">
                    🎓 Academic & Professional Qualifications
                </h3>

                <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 24px;">
                    @forelse($user->qualifications as $qual)
                        <div style="background: #0f172a; padding: 14px 18px; border-radius: 10px; border: 1px solid #334155; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <h4 style="font-size: 14px; font-weight: 700; color: #f8fafc; margin: 0;">{{ $qual->degree }}</h4>
                                <div style="font-size: 12px; color: #94a3b8;">{{ $qual->institution }} • {{ $qual->field_of_study }}</div>
                            </div>
                            <span class="badge" style="background: #1e293b; color: #cbd5e1; font-size: 11px;">
                                Year: {{ $qual->graduation_year ?? 'N/A' }}
                            </span>
                        </div>
                    @empty
                        <p style="font-size: 12px; color: #64748b;">No qualifications registered yet.</p>
                    @endforelse
                </div>

                <form action="{{ route('trainee.profile.qualifications.add') }}" method="POST" style="background: #0f172a; padding: 18px; border-radius: 10px; border: 1px solid #334155; display: flex; flex-direction: column; gap: 12px;">
                    @csrf
                    <div style="font-size: 14px; font-weight: 700; color: #f8fafc;">+ Add New Qualification</div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <input type="text" name="degree" placeholder="Degree / Certification (e.g. B.Sc Safety)" required style="background: #1e293b; border: 1px solid #334155; border-radius: 6px; padding: 8px 12px; color: #fff; font-size: 13px;">
                        <input type="text" name="institution" placeholder="Institution / University" required style="background: #1e293b; border: 1px solid #334155; border-radius: 6px; padding: 8px 12px; color: #fff; font-size: 13px;">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <input type="text" name="field_of_study" placeholder="Field of Study" style="background: #1e293b; border: 1px solid #334155; border-radius: 6px; padding: 8px 12px; color: #fff; font-size: 13px;">
                        <input type="number" name="graduation_year" placeholder="Graduation Year (YYYY)" style="background: #1e293b; border: 1px solid #334155; border-radius: 6px; padding: 8px 12px; color: #fff; font-size: 13px;">
                    </div>
                    <button type="submit" class="btn btn-secondary" style="background: #334155; color: #fff; border: none; padding: 8px 16px; border-radius: 6px; font-weight: 600; font-size: 12px; width: fit-content;">
                        Add Qualification
                    </button>
                </form>
            </div>
        </div>

        <!-- Right: Work History, Interests & Security -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            <!-- Interests Card -->
            <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 24px;">
                <h3 style="font-size: 18px; font-weight: 700; color: #f8fafc; margin: 0 0 16px 0;">
                    🎯 Disaster Response Interests
                </h3>

                <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px;">
                    @php
                        $interests = $user->profile?->interests ?? ['Flood Response', 'Search & Rescue', 'Drone Recon'];
                    @endphp
                    @foreach($interests as $interest)
                        <span style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399; padding: 4px 10px; border-radius: 9999px; font-size: 12px; font-weight: 600;">
                            # {{ $interest }}
                        </span>
                    @endforeach
                </div>

                <form action="{{ route('trainee.profile.interests.add') }}" method="POST" style="display: flex; gap: 8px;">
                    @csrf
                    <input type="text" name="interest" placeholder="e.g. Hazardous Materials" required style="flex: 1; background: #0f172a; border: 1px solid #334155; border-radius: 6px; padding: 8px 12px; color: #fff; font-size: 12px;">
                    <button type="submit" style="background: #10b981; color: #fff; border: none; padding: 8px 14px; border-radius: 6px; font-weight: 600; font-size: 12px; cursor: pointer;">
                        + Add
                    </button>
                </form>
            </div>

            <!-- Two-Factor Authentication Card -->
            <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 24px;">
                <h3 style="font-size: 18px; font-weight: 700; color: #f8fafc; margin: 0 0 8px 0;">
                    🔒 Security & 2FA
                </h3>
                <p style="font-size: 12px; color: #94a3b8; margin: 0 0 16px 0;">
                    Require an OTP verification passcode during authentication.
                </p>

                <div style="display: flex; align-items: center; justify-content: space-between; background: #0f172a; padding: 14px; border-radius: 10px; border: 1px solid #334155;">
                    <div>
                        <div style="font-size: 13px; font-weight: 700; color: #f8fafc;">Two-Factor Auth</div>
                        <div style="font-size: 11px; color: {{ ($user->profile?->two_factor_enabled ?? false) ? '#34d399' : '#94a3b8' }};">
                            Status: {{ ($user->profile?->two_factor_enabled ?? false) ? 'Enabled (Active)' : 'Disabled' }}
                        </div>
                    </div>
                    <form action="{{ route('trainee.profile.two-factor.toggle') }}" method="POST">
                        @csrf
                        <button type="submit" style="background: {{ ($user->profile?->two_factor_enabled ?? false) ? '#dc2626' : '#2563eb' }}; color: #fff; border: none; padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer;">
                            {{ ($user->profile?->two_factor_enabled ?? false) ? 'Disable 2FA' : 'Enable 2FA' }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Work Experience -->
            <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 24px;">
                <h3 style="font-size: 18px; font-weight: 700; color: #f8fafc; margin: 0 0 16px 0;">
                    💼 Work Experience History
                </h3>

                <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 20px;">
                    @forelse($user->experiences as $exp)
                        <div style="background: #0f172a; padding: 14px; border-radius: 10px; border: 1px solid #334155;">
                            <h4 style="font-size: 14px; font-weight: 700; color: #f8fafc; margin: 0 0 2px 0;">{{ $exp->position }}</h4>
                            <div style="font-size: 12px; color: #38bdf8;">{{ $exp->organization }}</div>
                            <div style="font-size: 11px; color: #64748b; margin-top: 4px;">
                                {{ $exp->start_date ? \Carbon\Carbon::parse($exp->start_date)->format('M Y') : '' }} — {{ $exp->is_current ? 'Present' : ($exp->end_date ? \Carbon\Carbon::parse($exp->end_date)->format('M Y') : '') }}
                            </div>
                        </div>
                    @empty
                        <p style="font-size: 12px; color: #64748b;">No past work experience logged.</p>
                    @endforelse
                </div>

                <form action="{{ route('trainee.profile.experiences.add') }}" method="POST" style="background: #0f172a; padding: 16px; border-radius: 10px; border: 1px solid #334155; display: flex; flex-direction: column; gap: 10px;">
                    @csrf
                    <div style="font-size: 13px; font-weight: 700; color: #f8fafc;">+ Add Experience Record</div>
                    <input type="text" name="position" placeholder="Position / Title" required style="background: #1e293b; border: 1px solid #334155; border-radius: 6px; padding: 8px 12px; color: #fff; font-size: 12px;">
                    <input type="text" name="organization" placeholder="Organization / Agency" required style="background: #1e293b; border: 1px solid #334155; border-radius: 6px; padding: 8px 12px; color: #fff; font-size: 12px;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                        <input type="date" name="start_date" required style="background: #1e293b; border: 1px solid #334155; border-radius: 6px; padding: 6px 10px; color: #fff; font-size: 12px;">
                        <input type="date" name="end_date" style="background: #1e293b; border: 1px solid #334155; border-radius: 6px; padding: 6px 10px; color: #fff; font-size: 12px;">
                    </div>
                    <button type="submit" class="btn btn-secondary" style="background: #334155; color: #fff; border: none; padding: 8px 14px; border-radius: 6px; font-weight: 600; font-size: 12px;">
                        Save Experience Record
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

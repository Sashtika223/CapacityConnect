@extends('layouts.trainer')

@section('title', 'Trainer Dashboard')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">
    <!-- Welcome Header -->
    <div style="background: linear-gradient(135deg, #0f2744, #1e3a8a); color: #fff; padding: 28px; border-radius: 16px; border: 1px solid rgba(255,255,255,0.1); display: flex; justify-content: space-between; align-items: center;">
        <div>
            <span class="badge" style="background: rgba(59,130,246,0.25); color: #60a5fa; margin-bottom: 8px; display: inline-block;">
                🎓 Master Trainer Studio
            </span>
            <h1 style="font-size: 26px; font-weight: 800; color: #ffffff; margin: 0 0 6px 0;">
                Welcome back, Instructor {{ $trainer->name }}!
            </h1>
            <p style="color: #cbd5e1; font-size: 14px; margin: 0;">
                Manage emergency capacity courses, evaluate practical submissions, and review trainee performance metrics.
            </p>
        </div>

        <a href="{{ route('trainer.courses.create') }}" class="btn btn-primary" style="background: #2563eb; color: #fff; text-decoration: none; padding: 12px 20px; border-radius: 10px; font-size: 14px; font-weight: 700; box-shadow: 0 4px 15px rgba(37,99,235,0.4);">
            + Author New Course
        </a>
    </div>

    <!-- Stats Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <div style="background: #1e293b; padding: 20px; border-radius: 12px; border: 1px solid #334155;">
            <span style="font-size: 12px; color: #94a3b8;">Authored Courses</span>
            <div style="font-size: 28px; font-weight: 700; color: #f8fafc; margin-top: 6px;">{{ $courses->count() }}</div>
            <span style="font-size: 11px; color: #64748b;">Active & pending courses</span>
        </div>
        <div style="background: #1e293b; padding: 20px; border-radius: 12px; border: 1px solid #334155;">
            <span style="font-size: 12px; color: #94a3b8;">Total Enrolled Trainees</span>
            <div style="font-size: 28px; font-weight: 700; color: #38bdf8; margin-top: 6px;">{{ $totalLearners }}</div>
            <span style="font-size: 11px; color: #64748b;">Distinct personnel</span>
        </div>
        <div style="background: #1e293b; padding: 20px; border-radius: 12px; border: 1px solid #334155;">
            <span style="font-size: 12px; color: #94a3b8;">Curriculum Completion Rate</span>
            <div style="font-size: 28px; font-weight: 700; color: #34d399; margin-top: 6px;">{{ $completionRate }}%</div>
            <span style="font-size: 11px; color: #64748b;">Learner success index</span>
        </div>
        <div style="background: #1e293b; padding: 20px; border-radius: 12px; border: 1px solid #334155;">
            <span style="font-size: 12px; color: #94a3b8;">Pending Submissions</span>
            <div style="font-size: 28px; font-weight: 700; color: #f59e0b; margin-top: 6px;">{{ $pendingSubmissions->count() }}</div>
            <span style="font-size: 11px; color: #64748b;">Awaiting grading</span>
        </div>
    </div>

    <!-- Main Grid Content -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <!-- Left: Course List & Pending Grading -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            <!-- Courses Overview Card -->
            <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <h3 style="font-size: 18px; font-weight: 700; color: #f8fafc; margin: 0;">📚 My Capacity Courses</h3>
                    <a href="{{ route('trainer.courses.index') }}" style="font-size: 13px; color: #38bdf8; text-decoration: none;">Manage All →</a>
                </div>

                <div style="display: flex; flex-direction: column; gap: 12px;">
                    @forelse($courses as $course)
                        <div style="background: #0f172a; padding: 16px; border-radius: 10px; border: 1px solid #334155; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                                    <span class="badge" style="background: {{ $course->status === 'published' ? 'rgba(16,185,129,0.2)' : 'rgba(245,158,11,0.2)' }}; color: {{ $course->status === 'published' ? '#34d399' : '#f59e0b' }}; font-size: 10px;">
                                        {{ strtoupper($course->status) }}
                                    </span>
                                    <span style="font-size: 12px; color: #94a3b8;">{{ $course->department?->name }}</span>
                                </div>
                                <h4 style="font-size: 15px; font-weight: 700; color: #f8fafc; margin: 0 0 4px 0;">{{ $course->title }}</h4>
                                <span style="font-size: 12px; color: #64748b;">👥 {{ $course->enrollments->count() }} Trainees Enrolled</span>
                            </div>

                            <div style="display: flex; gap: 8px;">
                                <a href="{{ route('trainer.courses.builder', $course->id) }}" class="btn btn-secondary" style="background: #334155; color: #fff; text-decoration: none; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                                    Curriculum Builder
                                </a>
                                <a href="{{ route('trainer.courses.edit', $course->id) }}" class="btn btn-secondary" style="background: rgba(59,130,246,0.15); color: #60a5fa; text-decoration: none; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                                    Edit Details
                                </a>
                            </div>
                        </div>
                    @empty
                        <p style="font-size: 12px; color: #64748b;">No authored courses found.</p>
                    @endforelse
                </div>
            </div>

            <!-- Pending Submissions -->
            <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 24px;">
                <h3 style="font-size: 18px; font-weight: 700; color: #f8fafc; margin: 0 0 16px 0;">
                    📥 Practical Submissions Awaiting Review
                </h3>

                <div style="display: flex; flex-direction: column; gap: 12px;">
                    @forelse($pendingSubmissions as $sub)
                        <div style="background: #0f172a; padding: 14px 18px; border-radius: 10px; border: 1px solid #334155; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <h4 style="font-size: 14px; font-weight: 700; color: #f8fafc; margin: 0 0 2px 0;">
                                    {{ $sub->practicalAssessment?->title }}
                                </h4>
                                <div style="font-size: 12px; color: #94a3b8;">
                                    Submitted by: <strong style="color: #cbd5e1;">{{ $sub->user?->name }}</strong> • {{ $sub->created_at->diffForHumans() }}
                                </div>
                            </div>

                            <a href="{{ route('trainer.submissions.show', $sub->id) }}" class="btn btn-primary" style="background: #2563eb; color: #fff; text-decoration: none; padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: 700;">
                                Review & Grade ➔
                            </a>
                        </div>
                    @empty
                        <p style="font-size: 12px; color: #64748b; margin: 0;">No practical responses pending review.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right: Certification Status & Recent Enrollees -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            <!-- Trainer Credential Card -->
            <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 24px;">
                <h3 style="font-size: 16px; font-weight: 700; color: #f8fafc; margin: 0 0 14px 0; display: flex; align-items: center; gap: 8px;">
                    🛡️ Instructional Accreditation
                </h3>

                @forelse($trainerCertificates as $tCert)
                    @php
                        $tValid = $tCert->isValid();
                    @endphp
                    <div style="background: #0f172a; padding: 16px; border-radius: 10px; border: 1px solid {{ $tValid ? '#10b981' : '#f43f5e' }}; margin-bottom: 10px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                            <span style="font-family: var(--font-mono); font-size: 11px; font-weight: 700; color: {{ $tValid ? '#34d399' : '#f87171' }};">
                                {{ $tCert->certificate_number }}
                            </span>
                            <span class="badge" style="background: {{ $tValid ? 'rgba(16,185,129,0.2)' : 'rgba(239,68,68,0.2)' }}; color: {{ $tValid ? '#34d399' : '#f87171' }}; font-size: 10px; font-weight: 800;">
                                {{ $tValid ? 'VALID' : 'EXPIRED' }}
                            </span>
                        </div>
                        <div style="font-size: 12px; font-weight: 600; color: #f8fafc; margin-bottom: 4px;">{{ $tCert->course?->title }}</div>
                        <div style="font-size: 11px; color: #94a3b8; margin-bottom: 10px;">
                            Valid Expiry: {{ $tCert->expiry_date ? $tCert->expiry_date->format('d M Y') : 'Lifetime' }}
                        </div>

                        @if(!$tValid)
                            <a href="{{ route('trainer.recertify', ['certificate_number' => $tCert->certificate_number]) }}" class="btn" style="background: #f43f5e; color: #fff; font-size: 11px; font-weight: 700; padding: 6px 12px; border-radius: 6px; text-decoration: none; display: block; text-align: center;">
                                📝 Attend Recertification Exam ➔
                            </a>
                        @else
                            <a href="{{ route('certificate.verify', ['code' => $tCert->certificate_number]) }}" style="font-size: 11px; color: #38bdf8; text-decoration: none; font-weight: 600; display: inline-block;">
                                Verify Public Credential ➔
                            </a>
                        @endif
                    </div>
                @empty
                    <div style="background: #0f172a; padding: 14px; border-radius: 8px; border: 1px solid #334155; font-size: 12px; color: #94a3b8;">
                        No master certificate assigned yet.
                    </div>
                @endforelse
            </div>

            <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 24px;">
                <h3 style="font-size: 16px; font-weight: 700; color: #f8fafc; margin: 0 0 16px 0;">
                    👥 Recent Enrollees
                </h3>

                <div style="display: flex; flex-direction: column; gap: 12px;">
                    @forelse($recentEnrollments as $enr)
                        <div style="background: #0f172a; padding: 12px; border-radius: 8px; border: 1px solid #334155; font-size: 13px;">
                            <div style="font-weight: 700; color: #f8fafc;">{{ $enr->user?->name }}</div>
                            <div style="font-size: 11px; color: #38bdf8;">Enrolled in {{ $enr->course?->title }}</div>
                            <div style="font-size: 10px; color: #64748b; margin-top: 4px;">{{ $enr->enrolled_at ? $enr->enrolled_at->format('M d, Y') : 'Active' }}</div>
                        </div>
                    @empty
                        <p style="font-size: 12px; color: #64748b; margin: 0;">No recent enrollments recorded.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

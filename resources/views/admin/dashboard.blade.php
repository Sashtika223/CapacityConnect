@extends('layouts.admin')

@section('title', 'Admin Governance Dashboard')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">
    <!-- Welcome Header Banner -->
    <div style="background: linear-gradient(135deg, #0f2744, #1e3a8a); color: #fff; padding: 28px; border-radius: 16px; border: 1px solid rgba(255,255,255,0.1); display: flex; justify-content: space-between; align-items: center;">
        <div>
            <span class="badge" style="background: rgba(59,130,246,0.25); color: #60a5fa; margin-bottom: 8px; display: inline-block;">
                🛡 Admin Governance & Operations Command
            </span>
            <h1 style="font-size: 26px; font-weight: 800; color: #ffffff; margin: 0 0 6px 0;">
                Enterprise Capacity Command Dashboard
            </h1>
            <p style="color: #cbd5e1; font-size: 14px; margin: 0;">
                System-wide oversight: user management, trainer approvals, risk radar alerts, AI content governance, and audit trails.
            </p>
        </div>

        <div style="display: flex; gap: 12px;">
            <a href="{{ route('admin.risk-radar') }}" class="btn btn-secondary" style="background: rgba(239,68,68,0.2); color: #f87171; text-decoration: none; padding: 10px 18px; border-radius: 8px; font-weight: 700; font-size: 13px; border: 1px solid rgba(239,68,68,0.4);">
                🚨 Risk Radar
            </a>
            <a href="{{ route('admin.capacity-simulator') }}" class="btn btn-primary" style="background: #2563eb; color: #fff; text-decoration: none; padding: 10px 18px; border-radius: 8px; font-weight: 700; font-size: 13px;">
                📊 Workforce Simulator
            </a>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:16px;">
        <div class="card" style="padding:18px;"><span style="font-size:12px;color:#64748b;">Average Assessment Score</span><strong style="display:block;font-size:25px;margin-top:7px;">{{ $averageAssessmentScore }}%</strong></div>
        <div class="card" style="padding:18px;"><span style="font-size:12px;color:#64748b;">Training Demand</span><strong style="display:block;font-size:25px;margin-top:7px;">{{ $trainingDemand }}</strong><small>active enrollments</small></div>
        <div class="card" style="padding:18px;"><span style="font-size:12px;color:#64748b;">Certification Coverage</span><strong style="display:block;font-size:25px;margin-top:7px;">{{ $validCertificatesCount + $expiringCertificatesCount }}</strong><small>issued certificates</small></div>
    </div>

    <!-- Core Metrics Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <div style="background: #1e293b; padding: 20px; border-radius: 12px; border: 1px solid #334155;">
            <span style="font-size: 12px; color: #94a3b8;">Total Registered Users</span>
            <div style="font-size: 28px; font-weight: 700; color: #f8fafc; margin-top: 6px;">{{ $totalUsers }}</div>
            <span style="font-size: 11px; color: #60a5fa;">{{ $traineesCount }} Trainees • {{ $trainersCount }} Active Trainers</span>
        </div>

        <div style="background: #1e293b; padding: 20px; border-radius: 12px; border: 1px solid #334155;">
            <span style="font-size: 12px; color: #94a3b8;">Pending Approvals</span>
            <div style="font-size: 28px; font-weight: 700; color: #f59e0b; margin-top: 6px;">{{ $pendingTrainersCount + $pendingCoursesCount }}</div>
            <span style="font-size: 11px; color: #64748b;">{{ $pendingCoursesCount }} Courses • {{ $pendingTrainersCount }} Trainers</span>
        </div>

        <div style="background: #1e293b; padding: 20px; border-radius: 12px; border: 1px solid #334155;">
            <span style="font-size: 12px; color: #94a3b8;">Published Courses</span>
            <div style="font-size: 28px; font-weight: 700; color: #34d399; margin-top: 6px;">{{ $publishedCoursesCount }}</div>
            <span style="font-size: 11px; color: #64748b;">Overall Completion: {{ $overallCompletionRate }}%</span>
        </div>

        <div style="background: #1e293b; padding: 20px; border-radius: 12px; border: 1px solid #334155;">
            <span style="font-size: 12px; color: #94a3b8;">Organizational Risk Status</span>
            <div style="font-size: 28px; font-weight: 700; color: {{ $riskAnalysis['overall_status'] === 'High Risk' ? '#f87171' : '#34d399' }}; margin-top: 6px;">
                {{ $riskAnalysis['overall_status'] }}
            </div>
            <span style="font-size: 11px; color: #64748b;">{{ $riskAnalysis['high_risk_department_count'] }} High Risk Department(s)</span>
        </div>
    </div>

    <!-- Main Grid Content -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <!-- Left: Pending Approvals & Risk Radar -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            <!-- Pending Trainer Approvals Card (§26) -->
            <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <h3 style="font-size: 18px; font-weight: 700; color: #f8fafc; margin: 0;">
                            🎓 Pending Trainer Approvals
                        </h3>
                        @if($pendingTrainersCount > 0)
                            <span style="background: rgba(251,191,36,0.2); color: #fbbf24; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 20px;">
                                {{ $pendingTrainersCount }} Pending
                            </span>
                        @endif
                    </div>
                    <a href="{{ route('admin.trainers.index') }}" style="font-size: 13px; color: #38bdf8; text-decoration: none;">View All Trainers →</a>
                </div>

                <div style="display: flex; flex-direction: column; gap: 12px;">
                    @forelse($pendingTrainersList as $trainer)
                        <div style="background: #0f172a; padding: 16px; border-radius: 10px; border: 1px solid #334155; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                            <div>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <h4 style="font-size: 15px; font-weight: 700; color: #f8fafc; margin: 0;">{{ $trainer->name }}</h4>
                                    <span style="background: rgba(251,191,36,0.15); color: #fbbf24; font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 12px;">Pending</span>
                                </div>
                                <div style="font-size: 12px; color: #94a3b8; margin-top: 4px;">
                                    {{ $trainer->email }} • Dept: {{ $trainer->department?->name ?? 'General' }} • Registered: {{ $trainer->created_at->diffForHumans() }}
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <a href="{{ route('admin.trainers.show', $trainer->id) }}"
                                   style="background: rgba(56,189,248,0.15); color: #38bdf8; border: 1px solid rgba(56,189,248,0.3); padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 700; text-decoration: none;">
                                    👁 View
                                </a>
                                <form action="{{ route('admin.trainers.approve', $trainer->id) }}" method="POST" style="display: inline;"
                                      onsubmit="return confirm('Are you sure you want to approve this trainer? An approval email will be sent.')">
                                    @csrf
                                    <button type="submit"
                                            style="background: rgba(16,185,129,0.15); color: #34d399; border: 1px solid rgba(16,185,129,0.4); padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 700; cursor: pointer;">
                                        ✓ Approve
                                    </button>
                                </form>
                                <form action="{{ route('admin.trainers.reject', $trainer->id) }}" method="POST" style="display: inline;"
                                      onsubmit="return confirm('Are you sure you want to reject this trainer application?')">
                                    @csrf
                                    <button type="submit"
                                            style="background: rgba(239,68,68,0.12); color: #f87171; border: 1px solid rgba(239,68,68,0.35); padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 700; cursor: pointer;">
                                        ✕ Reject
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p style="font-size: 12px; color: #64748b; margin: 0;">No trainer registrations currently awaiting approval.</p>
                    @endforelse
                </div>
            </div>

            <!-- Pending Courses Review Card -->
            <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <h3 style="font-size: 18px; font-weight: 700; color: #f8fafc; margin: 0;">
                        ⏳ Courses Awaiting Admin Approval
                    </h3>
                    <a href="{{ route('admin.courses.index', ['status' => 'pending']) }}" style="font-size: 13px; color: #38bdf8; text-decoration: none;">View All Pending →</a>
                </div>

                <div style="display: flex; flex-direction: column; gap: 12px;">
                    @forelse($pendingCourses as $course)
                        <div style="background: #0f172a; padding: 16px; border-radius: 10px; border: 1px solid #334155; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <h4 style="font-size: 15px; font-weight: 700; color: #f8fafc; margin: 0 0 4px 0;">{{ $course->title }}</h4>
                                <div style="font-size: 12px; color: #94a3b8;">
                                    Trainer: <strong style="color: #cbd5e1;">{{ $course->trainer?->name }}</strong> • Dept: {{ $course->department?->name }}
                                </div>
                            </div>
                            <a href="{{ route('admin.courses.show', $course->id) }}" class="btn btn-primary" style="background: #2563eb; color: #fff; text-decoration: none; padding: 8px 16px; border-radius: 6px; font-size: 12px; font-weight: 700;">
                                Review Course ➔
                            </a>
                        </div>
                    @empty
                        <p style="font-size: 12px; color: #64748b; margin: 0;">No courses currently awaiting approval.</p>
                    @endforelse
                </div>
            </div>

            <!-- Audit Log Stream -->
            <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <h3 style="font-size: 18px; font-weight: 700; color: #f8fafc; margin: 0;">
                        📜 System Activity Audit Log
                    </h3>
                    <a href="{{ route('admin.audit-logs') }}" style="font-size: 13px; color: #38bdf8; text-decoration: none;">Full Logs →</a>
                </div>

                <div style="display: flex; flex-direction: column; gap: 10px;">
                    @foreach($recentAuditLogs as $log)
                        <div style="background: #0f172a; padding: 12px 16px; border-radius: 8px; border: 1px solid #334155; font-size: 12px; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <span style="font-weight: 700; color: #f8fafc;">{{ $log->user?->name ?? 'System' }}</span>
                                <span style="color: #38bdf8; font-weight: 600; margin: 0 6px;">[{{ $log->action }}]</span>
                                <span style="color: #94a3b8;">IP: {{ $log->ip_address }}</span>
                            </div>
                            <span style="color: #64748b; font-size: 11px;">{{ $log->created_at->diffForHumans() }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right: Governance Shortcuts & High Risk Radar -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 20px;">
                <h3 style="font-size: 16px; font-weight: 700; color: #f8fafc; margin: 0 0 16px 0;">
                    ⚡ Enterprise Governance Navigation
                </h3>

                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <a href="{{ route('admin.users.index') }}" style="background: #0f172a; padding: 12px 16px; border-radius: 8px; border: 1px solid #334155; color: #f8fafc; text-decoration: none; font-size: 13px; font-weight: 600; display: flex; justify-content: space-between; align-items: center;">
                        <span>👥 User & Role Governance</span>
                        <span>➔</span>
                    </a>
                    <a href="{{ route('admin.trainers.index') }}" style="background: #0f172a; padding: 12px 16px; border-radius: 8px; border: 1px solid #334155; color: #f8fafc; text-decoration: none; font-size: 13px; font-weight: 600; display: flex; justify-content: space-between; align-items: center;">
                        <span>🎓 Trainer Approvals</span>
                        <span>➔</span>
                    </a>
                    <a href="{{ route('admin.competencies.index') }}" style="background: #0f172a; padding: 12px 16px; border-radius: 8px; border: 1px solid #334155; color: #f8fafc; text-decoration: none; font-size: 13px; font-weight: 600; display: flex; justify-content: space-between; align-items: center;">
                        <span>⚡ Competency & Skill Catalog</span>
                        <span>➔</span>
                    </a>
                    <a href="{{ route('admin.knowledge-graph') }}" style="background: #0f172a; padding: 12px 16px; border-radius: 8px; border: 1px solid #334155; color: #f8fafc; text-decoration: none; font-size: 13px; font-weight: 600; display: flex; justify-content: space-between; align-items: center;">
                        <span>🕸 Knowledge Graph & Expert Search</span>
                        <span>➔</span>
                    </a>
                    <a href="{{ route('admin.ai-content.index') }}" style="background: #0f172a; padding: 12px 16px; border-radius: 8px; border: 1px solid #334155; color: #f8fafc; text-decoration: none; font-size: 13px; font-weight: 600; display: flex; justify-content: space-between; align-items: center;">
                        <span>🤖 AI Generation & Editorial Board</span>
                        <span>➔</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

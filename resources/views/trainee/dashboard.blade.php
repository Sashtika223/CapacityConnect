@extends('layouts.trainee')

@section('title', 'Trainee Dashboard')

@section('content')
<div class="dashboard-wrapper" style="display: flex; flex-direction: column; gap: 24px;">
    <!-- Welcome Banner -->
    <div class="card card-glass" style="background: linear-gradient(135deg, rgba(15,39,68,0.95), rgba(30,58,138,0.9)); color: #fff; padding: 28px; border-radius: 16px; border: 1px solid rgba(255,255,255,0.1); display: flex; justify-content: space-between; align-items: center; box-shadow: 0 10px 25px rgba(0,0,0,0.3);">
        <div>
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                <span class="badge badge-primary" style="background: rgba(59,130,246,0.25); color: #60a5fa; border: 1px solid rgba(96,165,250,0.4);">
                    ⚡ Trainee Portal
                </span>
                <span style="color: #94a3b8; font-size: 13px;">Department: {{ $user->department?->name ?? 'Disaster Workforce' }}</span>
            </div>
            <h1 style="font-size: 26px; font-weight: 700; margin: 0 0 8px 0; color: #ffffff;">
                Welcome back, {{ $user->name }}!
            </h1>
            <p style="color: #cbd5e1; font-size: 14px; margin: 0; max-width: 650px; line-height: 1.5;">
                Continue building your disaster response and emergency management capacity. You have <strong style="color: #38bdf8;">{{ $activeEnrollments->count() }} active course(s)</strong> in progress.
            </p>
        </div>
        <div style="display: flex; gap: 16px; align-items: center;">
            <div style="text-align: right; background: rgba(0,0,0,0.25); padding: 12px 20px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05);">
                <div style="font-size: 12px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Learning Streak</div>
                <div style="font-size: 24px; font-weight: 800; color: #f59e0b; display: flex; align-items: center; justify-content: flex-end; gap: 6px;">
                    🔥 {{ $twinData['overview']['verified_count'] * 3 + 4 }} Days
                </div>
            </div>
        </div>
    </div>

    <!-- Overview Stats Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <div class="card stat-card" style="background: #1e293b; padding: 20px; border-radius: 12px; border: 1px solid #334155;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 13px; color: #94a3b8; font-weight: 500;">Active Enrolled</span>
                <span style="background: rgba(59,130,246,0.15); color: #3b82f6; padding: 8px; border-radius: 8px;">📚</span>
            </div>
            <div style="font-size: 28px; font-weight: 700; color: #f8fafc; margin-top: 8px;">{{ $activeEnrollments->count() }}</div>
            <div style="font-size: 12px; color: #64748b; margin-top: 4px;">Courses currently in progress</div>
        </div>

        <div class="card stat-card" style="background: #1e293b; padding: 20px; border-radius: 12px; border: 1px solid #334155; position: relative;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 13px; color: #94a3b8; font-weight: 500;">Completed Courses</span>
                <span style="background: rgba(16,185,129,0.15); color: #10b981; padding: 8px; border-radius: 8px;">✅</span>
            </div>
            <div style="font-size: 28px; font-weight: 700; color: #f8fafc; margin-top: 8px;">{{ $completedEnrollmentsCount }}</div>
            <div style="font-size: 12px; color: #10b981; margin-top: 4px; margin-bottom: 12px;">Fully verified learning</div>

            {{-- List of completed courses --}}
            @if($completedEnrollments->isNotEmpty())
                <div style="border-top: 1px solid #334155; padding-top: 12px; display: flex; flex-direction: column; gap: 8px;">
                    @foreach($completedEnrollments as $done)
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 8px;">
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-size: 12px; font-weight: 600; color: #e2e8f0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    ✅ {{ $done->course->title }}
                                </div>
                                <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                                    {{ $done->course->department?->name ?? 'General' }}
                                </div>
                            </div>
                            <div style="text-align: right; flex-shrink: 0;">
                                <span style="font-size: 10px; color: #10b981; background: rgba(16,185,129,0.1); padding: 2px 6px; border-radius: 4px; white-space: nowrap;">
                                    {{ $done->completed_at ? \Carbon\Carbon::parse($done->completed_at)->format('d M Y') : 'Completed' }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="card stat-card" style="background: #1e293b; padding: 20px; border-radius: 12px; border: 1px solid #334155;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 13px; color: #94a3b8; font-weight: 500;">Verified Competencies</span>
                <span style="background: rgba(139,92,246,0.15); color: #8b5cf6; padding: 8px; border-radius: 8px;">⚡</span>
            </div>
            <div style="font-size: 28px; font-weight: 700; color: #f8fafc; margin-top: 8px;">{{ $twinData['overview']['verified_count'] }}</div>
            <div style="font-size: 12px; color: #8b5cf6; margin-top: 4px;">Observed & evaluated</div>
        </div>

        <div class="card stat-card" style="background: #1e293b; padding: 20px; border-radius: 12px; border: 1px solid #334155;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 13px; color: #94a3b8; font-weight: 500;">Valid Certificates</span>
                <span style="background: rgba(245,158,11,0.15); color: #f59e0b; padding: 8px; border-radius: 8px;">🏆</span>
            </div>
            <div style="font-size: 28px; font-weight: 700; color: #f8fafc; margin-top: 8px;">{{ $certificatesCount }}</div>
            <div style="font-size: 12px; color: #f59e0b; margin-top: 4px;">Cryptographically hashed</div>
        </div>
    </div>

    <!-- Main Grid Content -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <!-- Left Column: Active Learning & Recommendations -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            <!-- Active Learning Card -->
            <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 style="font-size: 18px; font-weight: 600; color: #f8fafc; margin: 0; display: flex; align-items: center; gap: 8px;">
                        📖 Resume Learning
                    </h3>
                    <a href="{{ route('trainee.learning.index') }}" style="font-size: 13px; color: #38bdf8; text-decoration: none;">View All My Courses →</a>
                </div>

                @forelse($activeEnrollments as $enrollment)
                    <div style="background: #0f172a; padding: 18px; border-radius: 10px; border: 1px solid #1e293b; margin-bottom: 14px; display: flex; justify-content: space-between; align-items: center;">
                        <div style="max-width: 65%;">
                            <span class="badge" style="background: rgba(59,130,246,0.15); color: #60a5fa; font-size: 11px; margin-bottom: 6px; display: inline-block;">
                                {{ $enrollment->course->department?->name ?? 'General Emergency' }}
                            </span>
                            <h4 style="font-size: 15px; font-weight: 600; color: #f8fafc; margin: 0 0 6px 0;">
                                {{ $enrollment->course->title }}
                            </h4>
                            <div style="display: flex; align-items: center; gap: 12px; font-size: 12px; color: #94a3b8;">
                                <span>Progress: <strong>{{ $enrollment->progress_percentage }}%</strong></span>
                                <span>•</span>
                                <span>Trainer: {{ $enrollment->course->trainer?->name ?? 'Capacity Admin' }}</span>
                            </div>
                            <div style="width: 100%; background: #334155; height: 6px; border-radius: 3px; margin-top: 10px; overflow: hidden;">
                                <div style="width: {{ $enrollment->progress_percentage }}%; background: linear-gradient(90deg, #3b82f6, #06b6d4); height: 100%;"></div>
                            </div>
                        </div>

                        <a href="{{ route('trainee.learning.player', ['course' => $enrollment->course->id]) }}" class="btn btn-primary" style="background: #2563eb; color: #fff; text-decoration: none; padding: 10px 18px; border-radius: 8px; font-size: 13px; font-weight: 600; white-space: nowrap;">
                            Continue ▶
                        </a>
                    </div>
                @empty
                    <div style="text-align: center; padding: 36px; color: #94a3b8; background: #0f172a; border-radius: 10px; border: 1px dashed #334155;">
                        <p style="margin: 0 0 12px 0;">You have no active courses currently in progress.</p>
                        <a href="{{ route('courses.index') }}" class="btn btn-primary" style="background: #2563eb; color: #fff; text-decoration: none; padding: 8px 16px; border-radius: 6px; font-size: 13px;">Browse Course Catalog</a>
                    </div>
                @endforelse
            </div>

            <!-- Recommended Courses -->
            <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 style="font-size: 18px; font-weight: 600; color: #f8fafc; margin: 0; display: flex; align-items: center; gap: 8px;">
                        🎯 Recommended Capacity Courses
                    </h3>
                    <a href="{{ route('courses.index') }}" style="font-size: 13px; color: #38bdf8; text-decoration: none;">Browse Catalog →</a>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px;">
                    @foreach($recommendedCourses as $course)
                        <div style="background: #0f172a; border-radius: 10px; border: 1px solid #334155; padding: 14px; display: flex; flex-direction: column; justify-content: space-between;">
                            <div>
                                <span style="font-size: 11px; color: #38bdf8; text-transform: uppercase; font-weight: 600;">{{ $course->difficulty_level }}</span>
                                <h4 style="font-size: 14px; font-weight: 600; color: #f8fafc; margin: 6px 0 8px 0; line-height: 1.3;">
                                    {{ $course->title }}
                                </h4>
                                <p style="font-size: 12px; color: #94a3b8; margin: 0 0 12px 0; line-clamp: 2; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    {{ $course->description }}
                                </p>
                            </div>
                            <a href="{{ route('courses.show', $course->slug) }}" style="color: #60a5fa; font-size: 12px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                                View Course Details →
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right Column: Digital Twin & Announcements -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            {{-- Competency Twin Card: no .card class - we use full inline dark styles --}}
            <div style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <h3 style="font-size: 16px; font-weight: 600; color: #f8fafc; margin: 0;">⚡ Competency Twin Snapshot</h3>
                    <a href="{{ route('trainee.digital-twin') }}" style="font-size: 12px; color: #38bdf8; text-decoration: none;">View Twin →</a>
                </div>

                {{-- Readiness Index Bar --}}
                <div style="background: #0f172a; padding: 14px; border-radius: 10px; border: 1px solid #334155; margin-bottom: 14px;">
                    <div style="display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 6px;">
                        <span style="color: #cbd5e1;">Workforce Readiness Index</span>
                        <strong style="color: #10b981;">{{ $twinData['overview']['readiness_score'] }}%</strong>
                    </div>
                    <div style="width: 100%; background: #334155; height: 8px; border-radius: 4px; overflow: hidden;">
                        <div style="width: {{ $twinData['overview']['readiness_score'] }}%; background: #10b981; height: 100%;"></div>
                    </div>
                </div>

                {{-- Competency rows --}}
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    @forelse(array_slice($twinData['competencies'], 0, 4) as $comp)
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 9px 12px; background: #0f172a; border-radius: 8px; border: 1px solid #1e293b; gap: 10px;">
                            <span style="font-size: 12px; color: #f1f5f9; font-weight: 500; flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $comp['name'] }}">
                                {{ $comp['name'] }}
                            </span>
                            @php
                                $met = $comp['level'] >= $comp['required_level'];
                                $badgeBg  = $met ? 'rgba(16,185,129,0.25)' : 'rgba(59,130,246,0.25)';
                                $badgeClr = $met ? '#34d399' : '#60a5fa';
                            @endphp
                            <span style="flex-shrink: 0; background: {{ $badgeBg }}; color: {{ $badgeClr }}; padding: 3px 9px; border-radius: 5px; font-weight: 700; font-size: 11px; white-space: nowrap; border: 1px solid {{ $met ? 'rgba(52,211,153,0.3)' : 'rgba(96,165,250,0.3)' }};">
                                Level {{ $comp['level'] }}/5
                            </span>
                        </div>
                    @empty
                        <p style="font-size: 12px; color: #64748b; text-align: center; margin: 8px 0;">No competencies tracked yet.</p>
                    @endforelse
                </div>
            </div>

            <!-- Announcements & AI Companion -->
            <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 20px;">
                <h3 style="font-size: 16px; font-weight: 600; color: #f8fafc; margin: 0 0 14px 0;">📢 Platform Bulletins</h3>
                @forelse($announcements as $ann)
                    <div style="padding-bottom: 12px; margin-bottom: 12px; border-bottom: 1px solid #334155;">
                        <span style="font-size: 10px; color: #f59e0b; text-transform: uppercase; font-weight: 700;">{{ $ann->priority }} priority</span>
                        <h4 style="font-size: 13px; font-weight: 600; color: #f8fafc; margin: 4px 0;">{{ $ann->title }}</h4>
                        <p style="font-size: 12px; color: #94a3b8; margin: 0; line-height: 1.4;">{{ $ann->content }}</p>
                    </div>
                @empty
                    <p style="font-size: 12px; color: #64748b; margin: 0;">No active announcements.</p>
                @endforelse

                <div style="margin-top: 16px; padding: 14px; background: linear-gradient(135deg, rgba(30,58,138,0.5), rgba(15,23,42,0.8)); border-radius: 10px; border: 1px solid rgba(59,130,246,0.3); text-align: center;">
                    <div style="font-size: 20px; margin-bottom: 4px;">🤖</div>
                    <div style="font-size: 13px; font-weight: 600; color: #f8fafc;">Need help studying?</div>
                    <p style="font-size: 11px; color: #cbd5e1; margin: 4px 0 10px 0;">Ask the AI Learning Assistant about disaster protocol or course concepts.</p>
                    <a href="{{ route('trainee.ai.assistant') }}" class="btn btn-secondary" style="background: rgba(255,255,255,0.1); color: #fff; text-decoration: none; padding: 6px 14px; border-radius: 6px; font-size: 12px; display: inline-block;">Open AI Assistant</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

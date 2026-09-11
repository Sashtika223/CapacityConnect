@extends('layouts.trainee')

@section('title', 'Competency Digital Twin')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">
    <!-- Top Hero Banner -->
    <div class="card card-glass" style="background: linear-gradient(135deg, rgba(15,39,68,0.95), rgba(30,58,138,0.9)); color: #fff; padding: 28px; border-radius: 16px; border: 1px solid rgba(255,255,255,0.1); display: flex; justify-content: space-between; align-items: center;">
        <div>
            <span class="badge" style="background: rgba(59,130,246,0.25); color: #60a5fa; border: 1px solid rgba(96,165,250,0.4); margin-bottom: 8px; display: inline-block;">
                ⚡ Real-time Competency Twin
            </span>
            <h1 style="font-size: 26px; font-weight: 800; color: #ffffff; margin: 0 0 6px 0;">
                Digital Twin Profile — {{ $user->name }}
            </h1>
            <p style="color: #cbd5e1; font-size: 14px; margin: 0; max-width: 650px; line-height: 1.5;">
                Unified digital representation of your emergency management skills, course evaluations, field certifications, and AI-projected operational capacity.
            </p>
        </div>

        <div style="background: rgba(0,0,0,0.3); padding: 18px 28px; border-radius: 14px; border: 1px solid rgba(255,255,255,0.1); text-align: center;">
            <div style="font-size: 12px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Readiness Index</div>
            <div style="font-size: 36px; font-weight: 800; color: #34d399;">
                {{ $twin['overview']['readiness_score'] }}%
            </div>
            <span style="font-size: 11px; color: #60a5fa;">Verified Operational Capacity</span>
        </div>
    </div>

    <!-- Twin Metrics Bar -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <div style="background: #1e293b; padding: 18px; border-radius: 12px; border: 1px solid #334155;">
            <span style="font-size: 12px; color: #94a3b8;">Verified Competencies</span>
            <div style="font-size: 24px; font-weight: 700; color: #34d399; margin-top: 4px;">{{ $twin['overview']['verified_count'] }}</div>
            <span style="font-size: 11px; color: #64748b;">Evaluated & certified</span>
        </div>
        <div style="background: #1e293b; padding: 18px; border-radius: 12px; border: 1px solid #334155;">
            <span style="font-size: 12px; color: #94a3b8;">In-Progress Competencies</span>
            <div style="font-size: 24px; font-weight: 700; color: #60a5fa; margin-top: 4px;">{{ $twin['overview']['in_progress_count'] }}</div>
            <span style="font-size: 11px; color: #64748b;">Active course training</span>
        </div>
        <div style="background: #1e293b; padding: 18px; border-radius: 12px; border: 1px solid #334155;">
            <span style="font-size: 12px; color: #94a3b8;">Target Competency Gaps</span>
            <div style="font-size: 24px; font-weight: 700; color: #f59e0b; margin-top: 4px;">{{ count($twin['recommendations']) }}</div>
            <span style="font-size: 11px; color: #64748b;">Actionable gap items</span>
        </div>
    </div>

    <!-- Competencies Grid & Recommendations -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <!-- Left: Competencies & Skill Ratings -->
        <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 24px;">
            <h3 style="font-size: 18px; font-weight: 700; color: #f8fafc; margin: 0 0 20px 0; display: flex; align-items: center; gap: 8px;">
                🧬 Skill & Competency Breakdown
            </h3>

            <div style="display: flex; flex-direction: column; gap: 16px;">
                @foreach($twin['competencies'] as $comp)
                    <div style="background: #0f172a; padding: 18px; border-radius: 12px; border: 1px solid #334155;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <div>
                                <h4 style="font-size: 15px; font-weight: 600; color: #f8fafc; margin: 0 0 4px 0;">
                                    {{ $comp['name'] }}
                                </h4>
                                <span style="font-size: 12px; color: #94a3b8;">Category: {{ $comp['category'] }}</span>
                            </div>
                            <div style="text-align: right;">
                                <span class="badge" style="background: rgba(59,130,246,0.2); color: #60a5fa; font-weight: 700; font-size: 12px;">
                                    Level {{ $comp['level'] }} / 5
                                </span>
                            </div>
                        </div>

                        <!-- Level Bar -->
                        <div style="width: 100%; background: #1e293b; height: 10px; border-radius: 5px; overflow: hidden; margin-top: 10px; border: 1px solid #334155;">
                            <div style="width: {{ ($comp['level'] / 5) * 100 }}%; background: linear-gradient(90deg, #3b82f6, #10b981); height: 100%;"></div>
                        </div>

                        @if(!empty($comp['evidence']))
                            <div style="margin-top: 12px; font-size: 11px; color: #94a3b8;">
                                📜 Evidence: <span style="color: #cbd5e1;">{{ $comp['evidence'] }}</span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Right: AI Recommendations & Gap Mitigations -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 24px;">
                <h3 style="font-size: 16px; font-weight: 700; color: #f8fafc; margin: 0 0 16px 0;">
                    🎯 Competency Target Recommendations
                </h3>

                <div style="display: flex; flex-direction: column; gap: 12px;">
                    @forelse($twin['recommendations'] as $rec)
                        <div style="background: #0f172a; padding: 14px; border-radius: 10px; border: 1px solid #334155;">
                            <span class="badge" style="background: rgba(245,158,11,0.15); color: #f59e0b; font-size: 10px; margin-bottom: 4px; display: inline-block;">
                                Target Gap: {{ $rec['competency'] }}
                            </span>
                            <div style="font-size: 13px; font-weight: 600; color: #f8fafc; margin-bottom: 6px;">{{ $rec['action'] }}</div>
                            <a href="{{ route('courses.index') }}" style="font-size: 11px; color: #38bdf8; text-decoration: none; font-weight: 600;">Enrol in Recommended Module →</a>
                        </div>
                    @empty
                        <p style="font-size: 12px; color: #64748b; margin: 0;">No active competency gaps detected.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

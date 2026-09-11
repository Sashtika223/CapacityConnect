@extends('layouts.public')

@section('title', 'Trainer Recertification & Real-Time SOP Assessment — Capacity Connect')

@section('content')
<div class="app-container" style="max-width: 960px; padding-top: 40px; padding-bottom: 70px;">
    <!-- Breadcrumb & Top Tag -->
    <div style="margin-bottom: 20px;">
        <a href="{{ route('certificate.verify', ['code' => $certificate->certificate_number]) }}" style="color: #38bdf8; text-decoration: none; font-size: 13px; font-weight: 600;">
            ← Return to Verification: {{ $certificate->certificate_number }}
        </a>
    </div>

    <!-- Header Banner -->
    <div style="background: linear-gradient(135deg, #0f172a, #1e3a8a); color: #fff; padding: 30px; border-radius: 16px; border: 1px solid rgba(255,255,255,0.1); margin-bottom: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
            <div>
                <span class="badge" style="background: rgba(245,158,11,0.2); color: #f59e0b; border: 1px solid rgba(245,158,11,0.4); margin-bottom: 10px; display: inline-block;">
                    ⚡ State Disaster Management Authority • Trainer Recertification Gateway
                </span>
                <h1 style="font-size: 26px; font-weight: 800; color: #ffffff; margin: 0 0 8px 0;">
                    Instructor Competency Recertification & Assessment
                </h1>
                <p style="color: #cbd5e1; font-size: 14px; margin: 0; max-width: 680px; line-height: 1.5;">
                    To maintain instructional accreditation and conduct state-certified disaster mitigation courses, trainers must master real-time emergency updates and achieve a score of <strong style="color: #38bdf8;">80% or higher</strong> on this mandatory assessment.
                </p>
            </div>

            <div style="background: rgba(0,0,0,0.35); padding: 14px 20px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); text-align: right;">
                <div style="font-size: 11px; color: #94a3b8; text-transform: uppercase;">Candidate Instructor</div>
                <div style="font-size: 16px; font-weight: 700; color: #f8fafc; margin-top: 2px;">{{ $certificate->user->name }}</div>
                <div style="font-size: 12px; color: #38bdf8; margin-top: 4px;">{{ $certificate->certificate_number }}</div>
            </div>
        </div>
    </div>

    @if($result)
        <!-- Result Display Banner -->
        @if($result['passed'])
            <div class="card" style="background: #064e3b; border: 2px solid #10b981; padding: 28px; border-radius: 16px; margin-bottom: 32px; color: #ecfdf5;">
                <div style="display: flex; align-items: center; gap: 18px; margin-bottom: 16px;">
                    <span style="font-size: 42px;">🎉</span>
                    <div>
                        <span class="badge" style="background: #10b981; color: #ffffff; font-weight: 800; font-size: 12px; margin-bottom: 4px;">
                            EXAM PASSED • SCORE: {{ $result['score'] }}% ({{ $result['correct_count'] }}/{{ $result['total_questions'] }})
                        </span>
                        <h2 style="font-size: 22px; font-weight: 800; color: #ffffff; margin: 4px 0;">
                            Trainer Certification Successfully Renewed!
                        </h2>
                        <p style="font-size: 14px; color: #a7f3d0; margin: 0;">
                            You have demonstrated compliance with the 2026 Real-Time Emergency SOPs. Your instructional credentials are now active until <strong style="color: #ffffff;">{{ $result['new_expiry'] }}</strong>.
                        </p>
                    </div>
                </div>

                <div style="display: flex; gap: 12px; margin-top: 20px; flex-wrap: wrap;">
                    <a href="{{ route('certificate.verify', ['code' => $certificate->certificate_number]) }}" class="btn btn-primary" style="background: #10b981; color: #fff; font-weight: 700; padding: 10px 24px; text-decoration: none; border-radius: 8px;">
                        🛡 View Verified Certificate Status ➔
                    </a>
                    <a href="{{ route('trainer.dashboard') }}" class="btn" style="background: rgba(255,255,255,0.15); color: #fff; font-weight: 700; padding: 10px 20px; text-decoration: none; border-radius: 8px;">
                        Return to Trainer Dashboard
                    </a>
                </div>
            </div>
        @else
            <div class="card" style="background: #4c0519; border: 2px solid #f43f5e; padding: 28px; border-radius: 16px; margin-bottom: 32px; color: #ffe4e6;">
                <div style="display: flex; align-items: center; gap: 18px; margin-bottom: 16px;">
                    <span style="font-size: 42px;">⚠️</span>
                    <div>
                        <span class="badge" style="background: #f43f5e; color: #ffffff; font-weight: 800; font-size: 12px; margin-bottom: 4px;">
                            SCORE: {{ $result['score'] }}% ({{ $result['correct_count'] }}/{{ $result['total_questions'] }}) • PASSING REQUIRED: 80%
                        </span>
                        <h2 style="font-size: 22px; font-weight: 800; color: #ffffff; margin: 4px 0;">
                            Recertification Threshold Not Met
                        </h2>
                        <p style="font-size: 14px; color: #fecdd3; margin: 0;">
                            Please carefully review the 2026 Emergency SOP Updates below and retake the assessment to reactivate your instructional license.
                        </p>
                    </div>
                </div>

                <div style="margin-top: 16px;">
                    <a href="#assessment-section" class="btn" style="background: #f43f5e; color: #fff; font-weight: 700; padding: 8px 18px; text-decoration: none; border-radius: 6px; font-size: 13px;">
                        Retake Assessment Now ↓
                    </a>
                </div>
            </div>
        @endif
    @endif

    <!-- SECTION 1: 2026 Real-Time Emergency SOP Updates -->
    <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 26px; margin-bottom: 30px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
            <div>
                <span style="font-size: 11px; text-transform: uppercase; color: #38bdf8; font-weight: 700; letter-spacing: 0.5px;">Mandatory Study Material</span>
                <h3 style="font-size: 18px; font-weight: 700; color: #f8fafc; margin: 2px 0 0 0;">
                    📡 2026 Real-Time Disaster SOP & Protocol Updates
                </h3>
            </div>
            <span class="badge" style="background: rgba(56,189,248,0.15); color: #38bdf8; font-weight: 600;">Latest Version 2.4</span>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
            @foreach($updates as $update)
                <div style="background: #0f172a; padding: 18px; border-radius: 10px; border: 1px solid #334155;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <span style="font-size: 11px; color: #94a3b8;">{{ $update['category'] }}</span>
                        <span class="badge" style="background: rgba(245,158,11,0.2); color: #f59e0b; font-size: 10px; font-weight: 700;">{{ $update['badge'] }}</span>
                    </div>
                    <h4 style="font-size: 14px; font-weight: 700; color: #f8fafc; margin: 0 0 6px 0;">{{ $update['title'] }}</h4>
                    <p style="font-size: 12px; color: #cbd5e1; margin: 0; line-height: 1.5;">{{ $update['content'] }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <!-- SECTION 2: Recertification Assessment Form -->
    <div id="assessment-section" class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 28px;">
        <div style="margin-bottom: 24px;">
            <span style="font-size: 11px; text-transform: uppercase; color: #f59e0b; font-weight: 700; letter-spacing: 0.5px;">Evaluation Step</span>
            <h3 style="font-size: 20px; font-weight: 700; color: #f8fafc; margin: 4px 0 6px 0;">
                📝 Recertification Assessment Questions (5 Questions • 80% to Pass)
            </h3>
            <p style="font-size: 13px; color: #94a3b8; margin: 0;">
                Answer all questions based on the updated standard operating procedures above.
            </p>
        </div>

        <form action="{{ route('trainer.recertify.submit', ['certificate_number' => $certificate->certificate_number]) }}" method="POST">
            @csrf

            <div style="display: flex; flex-direction: column; gap: 24px;">
                @foreach($questions as $index => $q)
                    <div style="background: #0f172a; padding: 20px; border-radius: 12px; border: 1px solid #334155;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                            <span style="background: #2563eb; color: #ffffff; padding: 2px 8px; border-radius: 4px; font-weight: 700; font-size: 12px;">
                                Question {{ $index + 1 }}
                            </span>
                        </div>

                        <p style="font-size: 15px; font-weight: 600; color: #f8fafc; margin: 0 0 16px 0; line-height: 1.5;">
                            {{ $q['question'] }}
                        </p>

                        <div style="display: flex; flex-direction: column; gap: 10px;">
                            @foreach($q['options'] as $key => $option)
                                <label style="display: flex; align-items: flex-start; gap: 12px; background: #1e293b; padding: 12px 16px; border-radius: 8px; border: 1px solid #334155; cursor: pointer; color: #cbd5e1; font-size: 13px; transition: all 0.2s;">
                                    <input type="radio" name="answers[{{ $q['id'] }}]" value="{{ $key }}" required style="margin-top: 3px;">
                                    <span><strong>({{ strtoupper($key) }})</strong> {{ $option }}</span>
                                </label>
                            @endforeach
                        </div>

                        @if($result && isset($result['question_results'][$index]))
                            @php $qRes = $result['question_results'][$index]; @endphp
                            <div style="margin-top: 14px; padding: 12px; border-radius: 8px; font-size: 12px; background: {{ $qRes['is_correct'] ? 'rgba(16,185,129,0.15)' : 'rgba(239,68,68,0.15)' }}; border: 1px solid {{ $qRes['is_correct'] ? '#10b981' : '#ef4444' }}; color: {{ $qRes['is_correct'] ? '#34d399' : '#f87171' }};">
                                <div><strong>{{ $qRes['is_correct'] ? '✓ Correct Answer' : '✗ Incorrect Answer' }}</strong></div>
                                <div style="margin-top: 4px; color: #cbd5e1;">{{ $qRes['explanation'] }}</div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div style="margin-top: 28px; display: flex; justify-content: flex-end; gap: 14px;">
                <a href="{{ route('certificate.verify', ['code' => $certificate->certificate_number]) }}" class="btn" style="background: transparent; color: #94a3b8; border: 1px solid #334155; padding: 12px 20px; border-radius: 8px; text-decoration: none; font-size: 14px;">
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary" style="background: #2563eb; color: #fff; padding: 12px 28px; border-radius: 8px; font-weight: 700; font-size: 14px; border: none; cursor: pointer; box-shadow: 0 4px 15px rgba(37,99,235,0.4);">
                    Submit Recertification Exam ➔
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

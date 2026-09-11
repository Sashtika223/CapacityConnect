@extends('layouts.trainee')

@section('title', 'Assessments & Evaluations')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">
    <!-- Banner Header -->
    <div style="background: #1e293b; padding: 24px; border-radius: 14px; border: 1px solid #334155; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 24px; font-weight: 700; color: #f8fafc; margin: 0 0 6px 0;">Capacity Assessments & Evaluators</h1>
            <p style="font-size: 14px; color: #94a3b8; margin: 0;">Validate your disaster preparedness knowledge, complete MCQ quizzes, and submit field responses.</p>
        </div>
    </div>

    <!-- Active Assessments -->
    <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 24px;">
        <h3 style="font-size: 18px; font-weight: 700; color: #f8fafc; margin: 0 0 16px 0; display: flex; align-items: center; gap: 8px;">
            📝 Available MCQ Knowledge Quizzes
        </h3>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 16px;">
            @forelse($assessments as $assessment)
                <div style="background: #0f172a; padding: 18px; border-radius: 10px; border: 1px solid #334155; display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                            <span class="badge" style="background: rgba(59,130,246,0.15); color: #60a5fa; font-size: 11px;">
                                {{ $assessment->course?->title ?? 'General Capacity' }}
                            </span>
                            <span style="font-size: 11px; color: #f59e0b; font-weight: 600;">Pass Score: {{ $assessment->passing_percentage }}%</span>
                        </div>

                        <h4 style="font-size: 16px; font-weight: 700; color: #f8fafc; margin: 0 0 8px 0;">
                            {{ $assessment->title }}
                        </h4>
                        <p style="font-size: 12px; color: #94a3b8; margin: 0 0 14px 0; line-height: 1.4;">
                            {{ $assessment->instructions ?? 'Timed evaluation assessing emergency operational protocols.' }}
                        </p>

                        <div style="display: flex; gap: 16px; font-size: 12px; color: #64748b; margin-bottom: 16px;">
                            <span>⏱ Time Limit: {{ $assessment->time_limit_minutes }} min</span>
                            <span>❓ {{ $assessment->questions->count() }} Questions</span>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 12px; border-top: 1px solid #1e293b;">
                        <span style="font-size: 11px; color: #94a3b8;">Max Attempts: {{ $assessment->max_attempts }}</span>
                        <a href="{{ route('trainee.assessments.take', $assessment->id) }}" class="btn btn-primary" style="background: #2563eb; color: #fff; text-decoration: none; padding: 8px 16px; border-radius: 6px; font-size: 13px; font-weight: 600;">
                            Start Assessment ➔
                        </a>
                    </div>
                </div>
            @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 36px; background: #0f172a; border-radius: 10px; border: 1px dashed #334155; color: #94a3b8;">
                    No active MCQ assessments available for your enrolled courses right now.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Past Assessment Attempts History -->
    <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 24px;">
        <h3 style="font-size: 18px; font-weight: 700; color: #f8fafc; margin: 0 0 16px 0;">
            📊 Past Assessment Attempts
        </h3>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
                <thead>
                    <tr style="border-bottom: 1px solid #334155; color: #94a3b8;">
                        <th style="padding: 12px;">Assessment Title</th>
                        <th style="padding: 12px;">Attempt #</th>
                        <th style="padding: 12px;">Score</th>
                        <th style="padding: 12px;">Percentage</th>
                        <th style="padding: 12px;">Status</th>
                        <th style="padding: 12px;">Submitted At</th>
                        <th style="padding: 12px; text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attempts as $att)
                        <tr style="border-bottom: 1px solid #0f172a; color: #e2e8f0;">
                            <td style="padding: 12px; font-weight: 600;">{{ $att->assessment?->title }}</td>
                            <td style="padding: 12px; color: #94a3b8;">Attempt {{ $att->attempt_number }}</td>
                            <td style="padding: 12px;">{{ $att->score }} / {{ $att->total_possible_marks }}</td>
                            <td style="padding: 12px; font-weight: 700; color: {{ $att->passed ? '#34d399' : '#f87171' }};">
                                {{ $att->percentage }}%
                            </td>
                            <td style="padding: 12px;">
                                @if($att->passed)
                                    <span class="badge" style="background: rgba(16,185,129,0.2); color: #34d399;">PASSED</span>
                                @else
                                    <span class="badge" style="background: rgba(239,68,68,0.2); color: #f87171;">FAILED</span>
                                @endif
                            </td>
                            <td style="padding: 12px; color: #94a3b8;">{{ $att->submitted_at ? $att->submitted_at->format('M d, Y H:i') : '-' }}</td>
                            <td style="padding: 12px; text-align: right;">
                                <a href="{{ route('trainee.assessments.result', $att->id) }}" style="color: #38bdf8; font-weight: 600; text-decoration: none;">View Breakdown →</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding: 24px; text-align: center; color: #64748b;">No assessment attempts recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 12px;">
            {{ $attempts->links() }}
        </div>
    </div>
</div>
@endsection

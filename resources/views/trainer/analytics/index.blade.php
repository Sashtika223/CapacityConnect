@extends('layouts.trainer')

@section('title', 'Trainer Analytics & Metrics')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">
    <!-- Top Header -->
    <div style="background: #1e293b; padding: 24px; border-radius: 14px; border: 1px solid #334155; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 24px; font-weight: 700; color: #f8fafc; margin: 0 0 4px 0;">Curriculum Performance Analytics</h1>
            <p style="font-size: 14px; color: #94a3b8; margin: 0;">Quantitative engagement, assessment pass rates, and completion dynamics across authored courses.</p>
        </div>
    </div>

    <!-- Metrics Cards Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <div style="background: #1e293b; padding: 20px; border-radius: 12px; border: 1px solid #334155;">
            <span style="font-size: 12px; color: #94a3b8;">Completed Learning</span>
            <div style="font-size: 28px; font-weight: 700; color: #34d399; margin-top: 6px;">{{ $completedCount }}</div>
            <span style="font-size: 11px; color: #64748b;">Fully certified trainees</span>
        </div>

        <div style="background: #1e293b; padding: 20px; border-radius: 12px; border: 1px solid #334155;">
            <span style="font-size: 12px; color: #94a3b8;">In-Progress Active</span>
            <div style="font-size: 28px; font-weight: 700; color: #38bdf8; margin-top: 6px;">{{ $inProgressCount }}</div>
            <span style="font-size: 11px; color: #64748b;">Studying active lessons</span>
        </div>

        <div style="background: #1e293b; padding: 20px; border-radius: 12px; border: 1px solid #334155;">
            <span style="font-size: 12px; color: #94a3b8;">Quiz Pass Rate</span>
            <div style="font-size: 28px; font-weight: 700; color: #60a5fa; margin-top: 6px;">{{ $passRate }}%</div>
            <span style="font-size: 11px; color: #64748b;">Evaluation pass ratio</span>
        </div>

        <div style="background: #1e293b; padding: 20px; border-radius: 12px; border: 1px solid #334155;">
            <span style="font-size: 12px; color: #94a3b8;">Average Score</span>
            <div style="font-size: 28px; font-weight: 700; color: #f59e0b; margin-top: 6px;">{{ $avgScore }}%</div>
            <span style="font-size: 11px; color: #64748b;">Across {{ $assessmentAttempts->count() }} attempts</span>
        </div>
    </div>

    <!-- Course Breakdown Table -->
    <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 24px;">
        <h3 style="font-size: 18px; font-weight: 700; color: #f8fafc; margin: 0 0 16px 0;">
            📊 Course-by-Course Metrics
        </h3>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
                <thead>
                    <tr style="border-bottom: 1px solid #334155; color: #94a3b8;">
                        <th style="padding: 12px;">Course Title</th>
                        <th style="padding: 12px;">Category</th>
                        <th style="padding: 12px;">Total Enrolled</th>
                        <th style="padding: 12px;">Completion Count</th>
                        <th style="padding: 12px;">Assigned Quizzes</th>
                        <th style="padding: 12px; text-align: right;">Learner Interventions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courses as $c)
                        <tr style="border-bottom: 1px solid #0f172a; color: #e2e8f0;">
                            <td style="padding: 12px; font-weight: 700;">{{ $c->title }}</td>
                            <td style="padding: 12px; color: #94a3b8;">{{ $c->category }}</td>
                            <td style="padding: 12px; color: #38bdf8; font-weight: 700;">{{ $c->enrollments->count() }}</td>
                            <td style="padding: 12px; color: #34d399; font-weight: 700;">{{ $c->enrollments->where('status', 'completed')->count() }}</td>
                            <td style="padding: 12px; color: #cbd5e1;">{{ $c->assessments->count() }} MCQ Quizzes</td>
                            <td style="padding: 12px; text-align: right;">
                                <a href="{{ route('trainer.courses.enrollments', $c->id) }}" class="btn btn-secondary" style="background: rgba(59,130,246,0.15); color: #60a5fa; text-decoration: none; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                                    👥 Manage Learners & Interventions
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 24px; text-align: center; color: #64748b;">No authored courses.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

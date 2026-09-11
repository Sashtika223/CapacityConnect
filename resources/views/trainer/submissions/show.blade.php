@extends('layouts.trainer')

@section('title', 'Evaluate Submission — ' . $submission->practicalAssessment?->title)

@section('content')
<div style="max-width: 900px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px;">
    <!-- Top Header -->
    <div style="background: #1e293b; padding: 20px 24px; border-radius: 14px; border: 1px solid #334155; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <a href="{{ route('trainer.submissions.index') }}" style="color: #94a3b8; text-decoration: none; font-size: 13px; font-weight: 600;">← Back to All Submissions</a>
            <h1 style="font-size: 20px; font-weight: 700; color: #f8fafc; margin: 4px 0 0 0;">
                Grading: {{ $submission->practicalAssessment?->title }}
            </h1>
        </div>

        <span class="badge" style="background: rgba(59,130,246,0.15); color: #60a5fa; font-size: 12px; font-weight: 700;">
            Trainee: {{ $submission->user?->name }}
        </span>
    </div>

    <!-- Scenario & Trainee Response Grid -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        <!-- Left: Practical Scenario & Criteria -->
        <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 24px;">
            <h3 style="font-size: 16px; font-weight: 700; color: #f8fafc; margin: 0 0 12px 0;">
                📍 Scenario Task & Grading Criteria
            </h3>

            <div style="margin-bottom: 16px;">
                <div style="font-size: 12px; color: #94a3b8; font-weight: 600; text-transform: uppercase;">Scenario Prompt</div>
                <div style="background: #0f172a; padding: 14px; border-radius: 8px; border: 1px solid #334155; color: #cbd5e1; font-size: 13px; line-height: 1.5; margin-top: 6px;">
                    {{ $submission->practicalAssessment?->scenario_prompt }}
                </div>
            </div>

            <div>
                <div style="font-size: 12px; color: #94a3b8; font-weight: 600; text-transform: uppercase;">Evaluation Rubric</div>
                <div style="background: #0f172a; padding: 14px; border-radius: 8px; border: 1px solid #334155; color: #cbd5e1; font-size: 13px; line-height: 1.5; margin-top: 6px;">
                    {{ $submission->practicalAssessment?->evaluation_criteria }}
                </div>
            </div>

            <div style="margin-top: 16px; font-size: 12px; color: #f59e0b; font-weight: 700;">
                Max Score: {{ $submission->practicalAssessment?->max_score }} pts • Passing Threshold: {{ $submission->practicalAssessment?->passing_score }} pts
            </div>
        </div>

        <!-- Right: Trainee Work Response -->
        <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 24px;">
            <h3 style="font-size: 16px; font-weight: 700; color: #f8fafc; margin: 0 0 12px 0;">
                📄 Trainee Response Submission
            </h3>

            <div style="background: #0f172a; padding: 16px; border-radius: 8px; border: 1px solid #334155; color: #f8fafc; font-size: 14px; line-height: 1.6; min-height: 180px; whitespace: pre-wrap; margin-bottom: 16px;">
                {{ $submission->submission_text }}
            </div>

            @if($submission->file_path)
                <div style="background: #0f172a; padding: 10px 14px; border-radius: 8px; border: 1px solid #334155; display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 12px; color: #cbd5e1;">📎 Attached Document File</span>
                    <a href="{{ Storage::url($submission->file_path) }}" target="_blank" style="color: #38bdf8; font-size: 12px; font-weight: 600; text-decoration: none;">Download Attachment ⬇</a>
                </div>
            @endif
        </div>
    </div>

    <!-- Instructor Grading Form Card -->
    <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 28px;">
        <h3 style="font-size: 18px; font-weight: 700; color: #f8fafc; margin: 0 0 20px 0;">
            🎓 Master Evaluator Grade & Feedback
        </h3>

        <form action="{{ route('trainer.submissions.grade', $submission->id) }}" method="POST" style="display: flex; flex-direction: column; gap: 18px;">
            @csrf

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div>
                    <label style="font-size: 13px; font-weight: 600; color: #cbd5e1; display: block; margin-bottom: 6px;">Evaluation Verdict</label>
                    <select name="status" required style="width: 100%; background: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 10px 14px; color: #fff; font-size: 14px;">
                        <option value="passed" {{ $submission->status === 'passed' ? 'selected' : '' }}>PASS (Competency Verified)</option>
                        <option value="needs_revision" {{ $submission->status === 'needs_revision' ? 'selected' : '' }}>NEEDS REVISION</option>
                        <option value="failed" {{ $submission->status === 'failed' ? 'selected' : '' }}>FAIL (Unsatisfactory)</option>
                    </select>
                </div>

                <div>
                    <label style="font-size: 13px; font-weight: 600; color: #cbd5e1; display: block; margin-bottom: 6px;">Score (Max: {{ $submission->practicalAssessment?->max_score }})</label>
                    <input type="number" name="score" value="{{ old('score', $submission->score ?? $submission->practicalAssessment?->passing_score) }}" min="0" max="{{ $submission->practicalAssessment?->max_score }}" required style="width: 100%; background: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 10px 14px; color: #fff; font-size: 14px;">
                </div>
            </div>

            <div>
                <label style="font-size: 13px; font-weight: 600; color: #cbd5e1; display: block; margin-bottom: 6px;">Qualitative Feedback & Guidance Notes</label>
                <textarea name="feedback" rows="4" required style="width: 100%; background: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 12px 16px; color: #fff; font-size: 14px;" placeholder="Detail operational strengths, tactical mistakes, and recommendations for improvement...">{{ old('feedback', $submission->feedback) }}</textarea>
            </div>

            <div style="display: flex; justify-content: flex-end;">
                <button type="submit" class="btn btn-primary" style="background: #2563eb; color: #fff; border: none; padding: 12px 28px; border-radius: 8px; font-weight: 700; font-size: 14px; cursor: pointer;">
                    Save Evaluation & Update Trainee Record 🚀
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

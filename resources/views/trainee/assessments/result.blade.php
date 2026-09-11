@extends('layouts.trainee')

@section('title', 'Assessment Result — ' . $attempt->assessment->title)

@section('content')
<div style="max-width: 900px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px;">
    <!-- Score Summary Card -->
    <div class="card card-glass" style="background: {{ $attempt->passed ? 'linear-gradient(135deg, rgba(6,78,59,0.9), rgba(15,23,42,0.95))' : 'linear-gradient(135deg, rgba(127,29,29,0.9), rgba(15,23,42,0.95))' }}; padding: 32px; border-radius: 16px; border: 1px solid {{ $attempt->passed ? '#059669' : '#dc2626' }}; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.4);">
        <div style="font-size: 56px; margin-bottom: 8px;">
            {{ $attempt->passed ? '🎉' : '❌' }}
        </div>
        <h1 style="font-size: 28px; font-weight: 800; color: #ffffff; margin: 0 0 6px 0;">
            {{ $attempt->passed ? 'Assessment Passed Successfully!' : 'Assessment Attempt Unsuccessful' }}
        </h1>
        <p style="color: #cbd5e1; font-size: 15px; margin: 0 0 24px 0;">
            {{ $attempt->assessment->title }} ({{ $attempt->assessment->course?->title }})
        </p>

        <div style="display: flex; justify-content: center; gap: 32px; flex-wrap: wrap;">
            <div style="background: rgba(0,0,0,0.3); padding: 16px 28px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1);">
                <div style="font-size: 12px; color: #94a3b8; text-transform: uppercase;">Your Score</div>
                <div style="font-size: 32px; font-weight: 800; color: {{ $attempt->passed ? '#34d399' : '#f87171' }};">
                    {{ $attempt->score }} / {{ $attempt->total_possible_marks }}
                </div>
            </div>

            <div style="background: rgba(0,0,0,0.3); padding: 16px 28px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1);">
                <div style="font-size: 12px; color: #94a3b8; text-transform: uppercase;">Percentage</div>
                <div style="font-size: 32px; font-weight: 800; color: {{ $attempt->passed ? '#34d399' : '#f87171' }};">
                    {{ $attempt->percentage }}%
                </div>
            </div>

            <div style="background: rgba(0,0,0,0.3); padding: 16px 28px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1);">
                <div style="font-size: 12px; color: #94a3b8; text-transform: uppercase;">Required Pass Mark</div>
                <div style="font-size: 32px; font-weight: 800; color: #f8fafc;">
                    {{ $attempt->assessment->passing_percentage }}%
                </div>
            </div>
        </div>

        <div style="margin-top: 24px; display: flex; justify-content: center; gap: 14px;">
            <a href="{{ route('trainee.assessments.index') }}" class="btn btn-secondary" style="background: rgba(255,255,255,0.1); color: #fff; text-decoration: none; padding: 10px 20px; border-radius: 8px; font-weight: 600;">
                ← Back to Assessments
            </a>
            @if($attempt->passed && $attempt->assessment->course)
                <a href="{{ route('trainee.certificates.index') }}" class="btn btn-primary" style="background: #2563eb; color: #fff; text-decoration: none; padding: 10px 20px; border-radius: 8px; font-weight: 600;">
                    View Certificates 🏆
                </a>
            @endif
        </div>
    </div>

    <!-- Detailed Answer Breakdown -->
    <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 24px;">
        <h3 style="font-size: 18px; font-weight: 700; color: #f8fafc; margin: 0 0 20px 0;">
            🔍 Detailed Question & Answer Breakdown
        </h3>

        <div style="display: flex; flex-direction: column; gap: 16px;">
            @foreach($attempt->answers as $aIndex => $ans)
                <div style="background: #0f172a; padding: 18px; border-radius: 10px; border: 1px solid {{ $ans->is_correct ? '#059669' : '#dc2626' }};">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                        <h4 style="font-size: 15px; font-weight: 600; color: #f8fafc; margin: 0;">
                            Q{{ $aIndex + 1 }}. {{ $ans->question?->question_text }}
                        </h4>
                        <span class="badge" style="background: {{ $ans->is_correct ? 'rgba(16,185,129,0.2)' : 'rgba(239,68,68,0.2)' }}; color: {{ $ans->is_correct ? '#34d399' : '#f87171' }};">
                            {{ $ans->is_correct ? '+'.$ans->marks_awarded.' Marks' : '0 / '.$ans->question?->marks.' Marks' }}
                        </span>
                    </div>

                    <div style="margin-top: 10px; font-size: 13px;">
                        <div style="color: #94a3b8; margin-bottom: 4px;">
                            Your Answer: <strong style="color: {{ $ans->is_correct ? '#34d399' : '#f87171' }};">{{ $ans->selectedOption?->option_text ?? 'Not Answered' }}</strong>
                        </div>

                        @if(!$ans->is_correct)
                            @php $correctOpt = $ans->question?->options->firstWhere('is_correct', true); @endphp
                            <div style="color: #34d399;">
                                Correct Answer: <strong>{{ $correctOpt?->option_text }}</strong>
                            </div>
                        @endif

                        @if($ans->question?->explanation)
                            <div style="margin-top: 8px; background: #1e293b; padding: 10px; border-radius: 6px; color: #cbd5e1; font-size: 12px; border: 1px solid #334155;">
                                💡 <strong>Explanation:</strong> {{ $ans->question->explanation }}
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

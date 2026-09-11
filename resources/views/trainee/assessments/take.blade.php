@extends('layouts.trainee')

@section('title', 'Take Assessment — ' . $assessment->title)

@section('content')
<div style="max-width: 900px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px;">
    <!-- Timer & Header Banner -->
    <div style="background: #1e293b; padding: 24px; border-radius: 14px; border: 1px solid #334155; display: flex; justify-content: space-between; align-items: center; sticky: top;">
        <div>
            <span class="badge" style="background: rgba(59,130,246,0.15); color: #60a5fa; font-size: 11px; margin-bottom: 4px; display: inline-block;">
                {{ $assessment->course?->title }}
            </span>
            <h1 style="font-size: 22px; font-weight: 700; color: #f8fafc; margin: 0;">{{ $assessment->title }}</h1>
            <div style="font-size: 12px; color: #94a3b8; margin-top: 4px;">
                Attempt #{{ $attemptCount + 1 }} of {{ $assessment->max_attempts }} | Passing threshold: {{ $assessment->passing_percentage }}%
            </div>
        </div>

        <div style="background: #0f172a; border: 2px solid #3b82f6; padding: 12px 20px; border-radius: 12px; text-align: center;">
            <div style="font-size: 10px; color: #94a3b8; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Remaining Time</div>
            <div id="quiz-timer" style="font-size: 22px; font-weight: 800; color: #38bdf8; font-family: monospace;">
                {{ sprintf('%02d:00', $assessment->time_limit_minutes) }}
            </div>
        </div>
    </div>

    <!-- Questions Form -->
    <form action="{{ route('trainee.assessments.submit', $assessment->id) }}" method="POST" id="assessment-form">
        @csrf
        <div style="display: flex; flex-direction: column; gap: 20px;">
            @foreach($questions as $qIndex => $question)
                <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 24px;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px;">
                        <h3 style="font-size: 16px; font-weight: 600; color: #f8fafc; margin: 0; line-height: 1.5;">
                            <span style="color: #38bdf8; font-weight: 800;">Q{{ $qIndex + 1 }}.</span> {{ $question->question_text }}
                        </h3>
                        <span class="badge" style="background: #0f172a; color: #94a3b8; font-size: 11px; flex-shrink: 0; margin-left: 12px;">
                            {{ $question->marks }} Mark(s)
                        </span>
                    </div>

                    <!-- Options List -->
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        @foreach($question->options as $option)
                            <label style="display: flex; align-items: center; gap: 12px; background: #0f172a; padding: 14px 18px; border-radius: 10px; border: 1px solid #334155; cursor: pointer; transition: all 0.2s ease;">
                                <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}" style="width: 18px; height: 18px; accent-color: #2563eb;">
                                <span style="font-size: 14px; color: #cbd5e1;">{{ $option->option_text }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div style="margin-top: 28px; text-align: right;">
            <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #10b981, #059669); color: #fff; border: none; padding: 14px 32px; border-radius: 10px; font-size: 16px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 15px rgba(16,185,129,0.3);">
                Submit Final Assessment 🚀
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    let duration = {{ $assessment->time_limit_minutes * 60 }};
    const timerDisplay = document.getElementById('quiz-timer');
    const form = document.getElementById('assessment-form');

    const timer = setInterval(() => {
        let minutes = Math.floor(duration / 60);
        let seconds = duration % 60;
        minutes = minutes < 10 ? '0' + minutes : minutes;
        seconds = seconds < 10 ? '0' + seconds : seconds;

        timerDisplay.textContent = minutes + ':' + seconds;

        if (--duration < 0) {
            clearInterval(timer);
            alert('Time limit reached! Auto-submitting your responses now.');
            form.submit();
        }
    }, 1000);
</script>
@endpush
@endsection

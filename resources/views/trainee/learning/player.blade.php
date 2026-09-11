@extends('layouts.trainee')

@section('title', $course->title . ' — Course Player')

@section('content')
<div style="display: flex; flex-direction: column; gap: 20px;">
    <!-- Course Player Top Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; background: #1e293b; padding: 16px 24px; border-radius: 12px; border: 1px solid #334155;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <a href="{{ route('trainee.learning.index') }}" style="color: #94a3b8; text-decoration: none; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 4px;">
                ← Back to My Courses
            </a>
            <span style="color: #475569;">|</span>
            <h2 style="font-size: 16px; font-weight: 700; color: #f8fafc; margin: 0;">{{ $course->title }}</h2>
        </div>

        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="text-align: right; min-width: 140px;">
                <div style="font-size: 11px; color: #94a3b8; font-weight: 600;">Overall Progress</div>
                <div style="font-size: 13px; font-weight: 700; color: #38bdf8;">{{ $enrollment->progress_percentage }}% Completed</div>
            </div>
            <div style="width: 120px; background: #0f172a; height: 8px; border-radius: 4px; overflow: hidden; border: 1px solid #334155;">
                <div style="width: {{ $enrollment->progress_percentage }}%; background: #10b981; height: 100%;"></div>
            </div>
        </div>
    </div>

    <!-- Main Player Layout -->
    <div style="display: grid; grid-template-columns: 320px 1fr; gap: 24px; min-height: 650px;">
        <!-- Left: Curriculum Navigation Sidebar -->
        <div style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 20px; display: flex; flex-direction: column; gap: 16px; height: fit-content;">
            <h3 style="font-size: 15px; font-weight: 700; color: #f8fafc; margin: 0; padding-bottom: 12px; border-bottom: 1px solid #334155;">
                Course Syllabus
            </h3>

            <div style="display: flex; flex-direction: column; gap: 16px; max-height: 600px; overflow-y: auto; padding-right: 4px;">
                @foreach($course->modules as $modIndex => $module)
                    <div style="background: #0f172a; border-radius: 8px; border: 1px solid #334155; overflow: hidden;">
                        <div style="padding: 12px; background: #182234; border-bottom: 1px solid #334155; font-size: 13px; font-weight: 700; color: #cbd5e1;">
                            Module {{ $modIndex + 1 }}: {{ $module->title }}
                        </div>

                        <div style="display: flex; flex-direction: column;">
                            @foreach($module->lessons as $les)
                                @php
                                    $isCurrent = $lesson && $lesson->id === $les->id;
                                    $isDone = in_array($les->id, $completedLessonIds);
                                @endphp
                                <a href="{{ route('trainee.learning.player', ['course' => $course->id, 'lesson' => $les->id]) }}"
                                   style="display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; font-size: 13px; text-decoration: none; border-bottom: 1px solid #1e293b; color: {{ $isCurrent ? '#38bdf8' : ($isDone ? '#94a3b8' : '#e2e8f0') }}; background: {{ $isCurrent ? 'rgba(56,189,248,0.1)' : 'transparent' }}; border-left: {{ $isCurrent ? '3px solid #38bdf8' : 'none' }};">
                                    <div style="display: flex; align-items: center; gap: 8px; overflow: hidden;">
                                        <span style="font-size: 11px; flex-shrink: 0;">
                                            @if($isDone) ✅ @elseif($les->content_type === 'video') 🎥 @else 📄 @endif
                                        </span>
                                        <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $les->title }}</span>
                                    </div>
                                    <span style="font-size: 10px; color: #64748b; flex-shrink: 0;">{{ $les->duration_minutes }}m</span>
                                </a>
                            @endforeach

                            @foreach($module->assessments as $assessment)
                                <a href="{{ route('trainee.assessments.take', $assessment->id) }}"
                                   style="display: flex; align-items: center; gap: 8px; padding: 10px 14px; font-size: 12px; text-decoration: none; color: #f59e0b; background: rgba(245,158,11,0.08); font-weight: 600;">
                                    📝 Module Quiz: {{ $assessment->title }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Right: Lesson Display Content Player -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            @if($lesson)
                <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 28px;">
                    <!-- Lesson Header -->
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid #334155;">
                        <div>
                            <span class="badge" style="background: rgba(59,130,246,0.15); color: #60a5fa; font-size: 11px; margin-bottom: 6px; display: inline-block;">
                                {{ $lesson->module?->title }}
                            </span>
                            <h1 style="font-size: 22px; font-weight: 700; color: #f8fafc; margin: 0 0 6px 0;">
                                {{ $lesson->title }}
                            </h1>
                            <span style="font-size: 13px; color: #94a3b8;">Estimated Duration: {{ $lesson->duration_minutes }} minutes</span>
                        </div>

                        @if(in_array($lesson->id, $completedLessonIds))
                            <span class="badge" style="background: rgba(16,185,129,0.2); color: #34d399; padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 13px;">
                                ✓ Lesson Completed
                            </span>
                        @endif
                    </div>

                    <!-- Video Container (if video type) -->
                    @if($lesson->content_type === 'video' || $lesson->video_url)
                        <div style="background: #000; border-radius: 12px; overflow: hidden; margin-bottom: 24px; position: relative; aspect-ratio: 16/9; display: flex; align-items: center; justify-content: center; border: 1px solid #334155;">
                            @if(str_contains($lesson->video_url ?? '', 'youtube') || str_contains($lesson->video_url ?? '', 'vimeo'))
                                <iframe src="{{ $lesson->video_url }}" style="width: 100%; height: 100%; border: none;" allowfullscreen></iframe>
                            @else
                                <div style="text-align: center; color: #fff; padding: 40px;">
                                    <div style="font-size: 48px; margin-bottom: 12px; color: #38bdf8;">🎥</div>
                                    <h4 style="font-size: 18px; margin: 0 0 8px 0;">Interactive Capacity Training Video</h4>
                                    <p style="font-size: 13px; color: #94a3b8; max-width: 500px;">Simulated stream: {{ $lesson->title }}. Review instructions and video transcript below.</p>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Lesson Text / Body Content -->
                    <div style="color: #cbd5e1; font-size: 15px; line-height: 1.7; margin-bottom: 28px;" class="prose">
                        {!! nl2br(e($lesson->body)) !!}
                    </div>

                    <!-- Downloadable Resources -->
                    @if($lesson->resources && $lesson->resources->count() > 0)
                        <div style="background: #0f172a; padding: 20px; border-radius: 12px; border: 1px solid #334155; margin-bottom: 28px;">
                            <h4 style="font-size: 14px; font-weight: 700; color: #f8fafc; margin: 0 0 12px 0; display: flex; align-items: center; gap: 8px;">
                                📎 Attached Capacity Resources
                            </h4>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                @foreach($lesson->resources as $res)
                                    <div style="display: flex; justify-content: space-between; align-items: center; background: #1e293b; padding: 10px 14px; border-radius: 8px; border: 1px solid #334155;">
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <span style="font-size: 16px;">📄</span>
                                            <div>
                                                <div style="font-size: 13px; font-weight: 600; color: #f8fafc;">{{ $res->title }}</div>
                                                <div style="font-size: 11px; color: #64748b;">{{ strtoupper($res->file_type) }} • {{ round($res->file_size_bytes / 1024) }} KB</div>
                                            </div>
                                        </div>
                                        <a href="{{ route('trainee.resources.download', $res->id) }}" class="btn btn-secondary" style="background: rgba(59,130,246,0.15); color: #60a5fa; text-decoration: none; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                                            Download ⬇
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Completion Action Button -->
                    <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 20px; border-top: 1px solid #334155;">
                        <div>
                            @if(in_array($lesson->id, $completedLessonIds))
                                <span style="color: #34d399; font-size: 13px; font-weight: 600;">✓ Completed</span>
                            @else
                                <span style="color: #94a3b8; font-size: 13px;">Complete this lesson to advance your course progress.</span>
                            @endif
                        </div>

                        <form action="{{ route('trainee.learning.lesson.complete', ['course' => $course->id, 'lesson' => $lesson->id]) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #2563eb, #1d4ed8); color: #fff; border: none; padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(37,99,235,0.3);">
                                {{ in_array($lesson->id, $completedLessonIds) ? 'Mark & Go to Next Lesson ➔' : 'Complete & Continue ➔' }}
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Course Feedback & Rating Section -->
                <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 24px;">
                    <h3 style="font-size: 16px; font-weight: 700; color: #f8fafc; margin: 0 0 12px 0;">
                        ⭐ Course Feedback & Quality Review
                    </h3>
                    <p style="font-size: 13px; color: #94a3b8; margin: 0 0 16px 0;">
                        Provide real-time feedback to help instructors improve emergency readiness curriculum.
                    </p>

                    <form action="{{ route('trainee.courses.feedback', $course->id) }}" method="POST" style="display: flex; flex-direction: column; gap: 14px;">
                        @csrf
                        <div style="display: flex; align-items: center; gap: 16px;">
                            <label style="font-size: 13px; color: #cbd5e1; font-weight: 600;">Course Rating:</label>
                            <select name="rating" required style="background: #0f172a; border: 1px solid #334155; border-radius: 6px; padding: 6px 12px; color: #f59e0b; font-weight: 700; font-size: 14px;">
                                <option value="5">⭐⭐⭐⭐⭐ (5/5 - Outstanding)</option>
                                <option value="4">⭐⭐⭐⭐ (4/5 - Very Good)</option>
                                <option value="3">⭐⭐⭐ (3/5 - Average)</option>
                                <option value="2">⭐⭐ (2/5 - Needs Work)</option>
                                <option value="1">⭐ (1/5 - Unsatisfactory)</option>
                            </select>
                        </div>
                        <textarea name="comment" rows="2" placeholder="Share specific insights, practical simulation takeaways, or suggestions for the trainer..." style="width: 100%; background: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 10px 14px; color: #fff; font-size: 13px;"></textarea>
                        <div>
                            <button type="submit" class="btn btn-secondary" style="background: #334155; color: #fff; border: none; padding: 8px 18px; border-radius: 6px; font-weight: 600; font-size: 13px; cursor: pointer;">
                                Submit Feedback
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div style="text-align: center; padding: 60px; background: #1e293b; border-radius: 14px; border: 1px dashed #334155; color: #94a3b8;">
                    Select a lesson from the left syllabus to begin studying.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

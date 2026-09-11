@extends('layouts.public')

@section('title', $course->title . ' — Capacity Connect')

@section('content')
<!-- Course Header -->
<div style="background: linear-gradient(135deg, #0a192f 0%, #0f2744 50%, #1e3a8a 100%); color: #ffffff; padding: 60px 24px;">
    <div style="max-width: 1280px; margin: 0 auto; display: grid; grid-template-columns: 1.3fr 0.7fr; gap: 40px; align-items: center;">
        <div>
            <div style="display: flex; gap: 8px; margin-bottom: 14px; flex-wrap: wrap;">
                <span class="badge badge-blue">{{ $course->department?->name ?? $course->category }}</span>
                <span class="badge badge-emerald">{{ $course->difficulty }}</span>
                <span class="badge badge-slate" style="color: #fff; background: rgba(255,255,255,0.15);">{{ $course->duration_hours }} Hours Duration</span>
            </div>
            <h1 style="font-size: 34px; color: #ffffff; margin-bottom: 16px; line-height: 1.25;">{{ $course->title }}</h1>
            <p style="font-size: 16px; color: #cbd5e1; line-height: 1.6; margin-bottom: 24px;">{{ $course->short_description }}</p>

            <div style="display: flex; align-items: center; gap: 16px; font-size: 13px; color: #94a3b8;">
                <div>Lead Instructor: <strong style="color: #fff;">{{ $course->trainer->name }}</strong></div>
                <div>•</div>
                <div>Updated: <strong style="color: #fff;">{{ $course->updated_at->format('M Y') }}</strong></div>
            </div>
        </div>

        <!-- Enrollment CTA Card -->
        <div class="card" style="margin-bottom: 0; box-shadow: var(--cc-shadow-lg); border: 1px solid rgba(255,255,255,0.2);">
            <div style="height: 180px; overflow: hidden; background: #000;">
                <img src="{{ $course->thumbnail ?? 'https://images.unsplash.com/photo-1527482797697-8795b05a13fe?auto=format&fit=crop&w=800&q=80' }}" alt="{{ $course->title }}" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            <div style="padding: 24px;">
                @auth
                    @if(Auth::user()->isTrainee())
                        @php
                            $userEnrollment = Auth::user()->enrollments()->where('course_id', $course->id)->first();
                        @endphp

                        @if($userEnrollment)
                            <div style="margin-bottom: 16px;">
                                <div style="display: flex; justify-content: space-between; font-size: 13px; font-weight: 600; margin-bottom: 6px;">
                                    <span>Learning Progress</span>
                                    <span>{{ $userEnrollment->progress_percentage }}%</span>
                                </div>
                                <div class="progress-bar-container">
                                    <div class="progress-bar-fill" style="width: {{ $userEnrollment->progress_percentage }}%;"></div>
                                </div>
                            </div>
                            <a href="{{ route('trainee.learning.player', $course->id) }}" class="btn btn-primary" style="width: 100%; padding: 14px; font-size: 15px;">Continue Learning →</a>
                        @else
                            <form action="{{ route('trainee.courses.enroll', $course->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">Enroll in Certification Course</button>
                            </form>
                        @endif
                    @else
                        <div style="text-align: center; color: var(--cc-text-muted); font-size: 13px;">
                            Logged in as <strong>{{ Auth::user()->role }}</strong>.
                        </div>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg" style="width: 100%;">Sign In to Enroll</a>
                    <p style="text-align: center; font-size: 12px; color: var(--cc-text-muted); margin-top: 10px;">Free institutional access for verified personnel.</p>
                @endauth
            </div>
        </div>
    </div>
</div>

<!-- Course Content & Syllabus -->
<div class="app-container" style="padding-top: 40px; padding-bottom: 60px;">
    <div style="display: grid; grid-template-columns: 1.4fr 0.6fr; gap: 40px;">
        <div>
            <!-- Overview -->
            <div class="card" style="padding: 28px; margin-bottom: 28px;">
                <h2 style="font-size: 20px; margin-bottom: 16px;">Course Overview & Mandate</h2>
                <div style="font-size: 14px; color: #334155; line-height: 1.8; white-space: pre-line;">
                    {{ $course->full_description }}
                </div>
            </div>

            <!-- Learning Objectives -->
            @if(!empty($course->learning_objectives))
                <div class="card" style="padding: 28px; margin-bottom: 28px;">
                    <h2 style="font-size: 20px; margin-bottom: 16px;">Key Learning Objectives</h2>
                    <ul style="list-style: none; display: flex; flex-direction: column; gap: 12px;">
                        @foreach($course->learning_objectives as $objective)
                            <li style="display: flex; align-items: flex-start; gap: 10px; font-size: 14px; color: #334155;">
                                <span style="color: var(--cc-emerald); font-weight: 800;">✓</span>
                                <span>{{ $objective }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Curriculum Modules -->
            <div class="card" style="padding: 28px; margin-bottom: 28px;">
                <h2 style="font-size: 20px; margin-bottom: 16px;">Curriculum & Lessons</h2>
                
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    @forelse($course->modules as $index => $module)
                        <div style="border: 1px solid var(--cc-border); border-radius: 8px; overflow: hidden;">
                            <div style="background: var(--cc-bg-subtle); padding: 14px 18px; font-weight: 700; font-size: 15px; color: var(--cc-primary-dark); display: flex; justify-content: space-between;">
                                <span>{{ $module->title }}</span>
                                <span style="font-size: 12px; color: var(--cc-text-muted); font-weight: 500;">{{ $module->lessons->count() }} Lessons</span>
                            </div>
                            <div style="padding: 8px 18px;">
                                @foreach($module->lessons as $lIndex => $lesson)
                                    <div style="padding: 10px 0; border-bottom: 1px solid var(--cc-border); display: flex; align-items: center; justify-content: space-between; font-size: 13px;">
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <span style="color: var(--cc-text-muted); font-size: 12px;">{{ $index + 1 }}.{{ $lIndex + 1 }}</span>
                                            <span style="color: var(--cc-text-main); font-weight: 500;">{{ $lesson->title }}</span>
                                            @if($lesson->is_free_preview)
                                                <span class="badge badge-emerald" style="font-size: 10px;">Preview</span>
                                            @endif
                                        </div>
                                        <span style="color: var(--cc-text-muted);">{{ $lesson->duration_minutes }}m</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p style="color: var(--cc-text-muted); font-size: 14px;">Curriculum details being scheduled.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar Details -->
        <div>
            <!-- Competencies Covered -->
            <div class="card" style="padding: 24px; margin-bottom: 24px;">
                <h3 style="font-size: 16px; margin-bottom: 16px;">Competencies Built</h3>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    @foreach($course->competencies as $comp)
                        <div style="background: var(--cc-bg-subtle); padding: 12px; border-radius: 6px; border: 1px solid var(--cc-border);">
                            <p style="font-weight: 600; font-size: 13px; color: var(--cc-primary-dark);">{{ $comp->title }}</p>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 6px; font-size: 11px; color: var(--cc-text-muted);">
                                <span>Code: {{ $comp->code }}</span>
                                <span class="badge badge-emerald">Target Tier {{ $comp->pivot->target_level ?? 3 }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Instructor Profile Card -->
            <div class="card" style="padding: 24px;">
                <h3 style="font-size: 16px; margin-bottom: 16px;">Lead Instructor</h3>
                <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 14px;">
                    <div class="user-avatar-sm" style="width: 46px; height: 46px; font-size: 16px; background: #1e3a8a;">
                        {{ substr($course->trainer->name, 0, 2) }}
                    </div>
                    <div>
                        <h4 style="font-size: 15px; color: var(--cc-text-main);">{{ $course->trainer->name }}</h4>
                        <p style="font-size: 12px; color: var(--cc-text-muted);">{{ $course->trainer->profile?->job_title ?? 'Master Instructor' }}</p>
                    </div>
                </div>
                <p style="font-size: 13px; color: var(--cc-text-muted); line-height: 1.6;">{{ $course->trainer->profile?->bio ?? 'Senior disaster trainer and tactical emergency response specialist.' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

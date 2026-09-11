@extends('layouts.trainee')

@section('title', 'My Learning & Enrolled Courses')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">
    <!-- Top Header & Filter Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; background: #1e293b; padding: 24px; border-radius: 14px; border: 1px solid #334155;">
        <div>
            <h1 style="font-size: 24px; font-weight: 700; color: #f8fafc; margin: 0 0 4px 0;">My Learning Dashboard</h1>
            <p style="font-size: 14px; color: #94a3b8; margin: 0;">Track your enrolled capacity building courses, completion progress, and lessons.</p>
        </div>

        <!-- Filter tabs -->
        <div style="display: flex; background: #0f172a; padding: 4px; border-radius: 8px; border: 1px solid #334155;">
            <a href="{{ route('trainee.learning.index', ['status' => 'all']) }}" style="padding: 8px 16px; border-radius: 6px; font-size: 13px; font-weight: 600; text-decoration: none; color: {{ $status === 'all' ? '#ffffff' : '#94a3b8' }}; background: {{ $status === 'all' ? '#2563eb' : 'transparent' }};">
                All Courses
            </a>
            <a href="{{ route('trainee.learning.index', ['status' => 'in_progress']) }}" style="padding: 8px 16px; border-radius: 6px; font-size: 13px; font-weight: 600; text-decoration: none; color: {{ $status === 'in_progress' ? '#ffffff' : '#94a3b8' }}; background: {{ $status === 'in_progress' ? '#2563eb' : 'transparent' }};">
                In Progress
            </a>
            <a href="{{ route('trainee.learning.index', ['status' => 'completed']) }}" style="padding: 8px 16px; border-radius: 6px; font-size: 13px; font-weight: 600; text-decoration: none; color: {{ $status === 'completed' ? '#ffffff' : '#94a3b8' }}; background: {{ $status === 'completed' ? '#2563eb' : 'transparent' }};">
                Completed
            </a>
        </div>
    </div>

    <!-- Course Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
        @forelse($enrollments as $enrollment)
            @php $course = $enrollment->course; @endphp
            <div class="card" style="background: #1e293b; border-radius: 12px; border: 1px solid #334155; overflow: hidden; display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="height: 140px; background: linear-gradient(135deg, #0f2744, #1e3a8a); position: relative; padding: 16px; display: flex; flex-direction: column; justify-content: space-between;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span class="badge" style="background: rgba(15,23,42,0.7); color: #38bdf8; font-size: 11px; backdrop-filter: blur(4px);">
                                {{ $course->department?->name ?? 'Disaster Management' }}
                            </span>
                            @if($enrollment->status === 'completed')
                                <span class="badge" style="background: rgba(16,185,129,0.2); color: #34d399; font-size: 11px; font-weight: 700;">
                                    ✓ Completed
                                </span>
                            @else
                                <span class="badge" style="background: rgba(59,130,246,0.2); color: #60a5fa; font-size: 11px;">
                                    In Progress
                                </span>
                            @endif
                        </div>
                        <h3 style="font-size: 16px; font-weight: 700; color: #ffffff; margin: 0; line-height: 1.3; text-shadow: 0 2px 4px rgba(0,0,0,0.5);">
                            {{ $course->title }}
                        </h3>
                    </div>

                    <div style="padding: 18px;">
                        <p style="font-size: 13px; color: #94a3b8; margin: 0 0 16px 0; line-clamp: 2; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4;">
                            {{ $course->description }}
                        </p>

                        <!-- Progress indicator -->
                        <div style="margin-bottom: 16px;">
                            <div style="display: flex; justify-content: space-between; font-size: 12px; color: #cbd5e1; margin-bottom: 6px; font-weight: 600;">
                                <span>Curriculum Progress</span>
                                <span>{{ $enrollment->progress_percentage }}%</span>
                            </div>
                            <div style="width: 100%; background: #0f172a; height: 8px; border-radius: 4px; overflow: hidden; border: 1px solid #334155;">
                                <div style="width: {{ $enrollment->progress_percentage }}%; background: {{ $enrollment->status === 'completed' ? '#10b981' : 'linear-gradient(90deg, #3b82f6, #06b6d4)' }}; height: 100%;"></div>
                            </div>
                        </div>

                        <div style="display: flex; justify-content: space-between; font-size: 12px; color: #64748b;">
                            <span>Trainer: {{ $course->trainer?->name ?? 'Capacity Connect' }}</span>
                            <span>Enrolled: {{ $enrollment->enrolled_at ? $enrollment->enrolled_at->format('M d, Y') : 'Active' }}</span>
                        </div>
                    </div>
                </div>

                <div style="padding: 16px; background: #0f172a; border-top: 1px solid #334155; display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 12px; color: #94a3b8;">
                        {{ $course->modules->sum(fn($m) => $m->lessons->count()) }} Lessons
                    </span>

                    <a href="{{ route('trainee.learning.player', ['course' => $course->id]) }}" class="btn btn-primary" style="background: #2563eb; color: #fff; text-decoration: none; padding: 8px 16px; border-radius: 6px; font-size: 13px; font-weight: 600;">
                        {{ $enrollment->status === 'completed' ? 'Review Lessons ➔' : 'Resume Course ➔' }}
                    </a>
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; background: #1e293b; border-radius: 12px; border: 1px dashed #334155; color: #94a3b8;">
                <div style="font-size: 40px; margin-bottom: 12px;">📚</div>
                <h3 style="font-size: 18px; color: #f8fafc; margin: 0 0 8px 0;">No enrolled courses found</h3>
                <p style="font-size: 14px; max-width: 450px; margin: 0 auto 20px auto;">You haven't enrolled in any courses under this filter yet. Explore our disaster workforce curriculum catalog.</p>
                <a href="{{ route('courses.index') }}" class="btn btn-primary" style="background: #2563eb; color: #fff; text-decoration: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; font-size: 14px;">
                    Explore Course Catalog
                </a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div style="margin-top: 12px;">
        {{ $enrollments->withQueryString()->links() }}
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('title', 'Review Course — ' . $course->title)

@section('content')
<div style="max-width: 950px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px;">
    <!-- Header Banner -->
    <div style="background: #1e293b; padding: 24px; border-radius: 14px; border: 1px solid #334155; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <a href="{{ route('admin.courses.index') }}" style="color: #94a3b8; text-decoration: none; font-size: 13px; font-weight: 600;">← Back to Courses Governance</a>
            <h1 style="font-size: 22px; font-weight: 700; color: #f8fafc; margin: 6px 0 0 0;">{{ $course->title }}</h1>
        </div>

        <div style="display: flex; gap: 12px; align-items: center;">
            <span class="badge" style="background: {{ $course->status === 'published' ? 'rgba(16,185,129,0.2)' : 'rgba(245,158,11,0.2)' }}; color: {{ $course->status === 'published' ? '#34d399' : '#f59e0b' }}; font-size: 12px; font-weight: 700;">
                {{ strtoupper($course->status) }}
            </span>
        </div>
    </div>

    <!-- Course Overview & Approval Actions -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <!-- Left: Course Details & Curriculum Breakdown -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 24px;">
                <h3 style="font-size: 16px; font-weight: 700; color: #f8fafc; margin: 0 0 12px 0;">Syllabus Overview</h3>
                <p style="font-size: 14px; color: #cbd5e1; line-height: 1.6; margin: 0 0 16px 0;">{{ $course->full_description }}</p>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; font-size: 13px; color: #94a3b8;">
                    <div>Authoring Trainer: <strong style="color: #f8fafc;">{{ $course->trainer?->name }}</strong></div>
                    <div>Department: <strong style="color: #f8fafc;">{{ $course->department?->name }}</strong></div>
                    <div>Difficulty: <strong style="color: #f8fafc;">{{ $course->difficulty }}</strong></div>
                    <div>Duration: <strong style="color: #f8fafc;">{{ $course->duration_hours }} Hours</strong></div>
                </div>
            </div>

            <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 24px;">
                <h3 style="font-size: 16px; font-weight: 700; color: #f8fafc; margin: 0 0 16px 0;">📖 Modules & Lessons Review</h3>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    @foreach($course->modules as $modIndex => $module)
                        <div style="background: #0f172a; padding: 14px; border-radius: 10px; border: 1px solid #334155;">
                            <h4 style="font-size: 14px; font-weight: 700; color: #f8fafc; margin: 0 0 8px 0;">
                                Module {{ $modIndex + 1 }}: {{ $module->title }}
                            </h4>
                            <div style="display: flex; flex-direction: column; gap: 6px;">
                                @foreach($module->lessons as $les)
                                    <div style="font-size: 12px; color: #cbd5e1; background: #1e293b; padding: 6px 10px; border-radius: 4px;">
                                        📄 {{ $les->title }} ({{ $les->duration_minutes }} min)
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right: Approval / Rejection Controls -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 24px;">
                <h3 style="font-size: 16px; font-weight: 700; color: #f8fafc; margin: 0 0 16px 0;">
                    ⚖️ Governance Decision
                </h3>

                <form action="{{ route('admin.courses.approve', $course->id) }}" method="POST" style="margin-bottom: 16px;">
                    @csrf
                    <button type="submit" class="btn btn-primary" style="width: 100%; background: #10b981; color: #fff; border: none; padding: 12px; border-radius: 8px; font-weight: 700; font-size: 14px; cursor: pointer;">
                        Approve & Publish to Catalog ✓
                    </button>
                </form>

                <form action="{{ route('admin.courses.reject', $course->id) }}" method="POST" style="display: flex; flex-direction: column; gap: 10px;">
                    @csrf
                    <textarea name="rejection_reason" rows="3" placeholder="Provide rejection feedback for trainer..." required style="background: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 10px; color: #fff; font-size: 13px;"></textarea>
                    <button type="submit" class="btn btn-secondary" style="background: rgba(239,68,68,0.2); color: #f87171; border: 1px solid #ef4444; padding: 10px; border-radius: 8px; font-weight: 700; font-size: 13px; cursor: pointer;">
                        Reject Course ✕
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

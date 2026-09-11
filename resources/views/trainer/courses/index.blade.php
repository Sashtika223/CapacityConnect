@extends('layouts.trainer')

@section('title', 'Manage Courses')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">
    <!-- Top Header -->
    <div style="background: #1e293b; padding: 24px; border-radius: 14px; border: 1px solid #334155; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 24px; font-weight: 700; color: #f8fafc; margin: 0 0 4px 0;">Capacity Course Studio</h1>
            <p style="font-size: 14px; color: #94a3b8; margin: 0;">Author, edit, and publish emergency management training courses.</p>
        </div>

        <a href="{{ route('trainer.courses.create') }}" class="btn btn-primary" style="background: #2563eb; color: #fff; text-decoration: none; padding: 10px 20px; border-radius: 8px; font-weight: 700; font-size: 14px;">
            + Create New Course
        </a>
    </div>

    <!-- Courses Table -->
    <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 24px;">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
                <thead>
                    <tr style="border-bottom: 1px solid #334155; color: #94a3b8;">
                        <th style="padding: 12px;">Course Title</th>
                        <th style="padding: 12px;">Department</th>
                        <th style="padding: 12px;">Modules / Lessons</th>
                        <th style="padding: 12px;">Enrolled</th>
                        <th style="padding: 12px;">Status</th>
                        <th style="padding: 12px; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courses as $course)
                        <tr style="border-bottom: 1px solid #0f172a; color: #e2e8f0;">
                            <td style="padding: 12px; font-weight: 700;">
                                <div style="color: #f8fafc;">{{ $course->title }}</div>
                                <div style="font-size: 11px; color: #64748b;">{{ $course->difficulty }} • {{ $course->duration_hours }} hrs</div>
                            </td>
                            <td style="padding: 12px; color: #cbd5e1;">{{ $course->department?->name ?? 'General' }}</td>
                            <td style="padding: 12px; color: #38bdf8;">
                                {{ $course->modules->count() }} Modules ({{ $course->modules->sum(fn($m)=>$m->lessons->count()) }} Lessons)
                            </td>
                            <td style="padding: 12px; color: #10b981; font-weight: 700;">
                                {{ $course->enrollments->count() }} Trainees
                            </td>
                            <td style="padding: 12px;">
                                @if($course->status === 'published')
                                    <span class="badge" style="background: rgba(16,185,129,0.2); color: #34d399;">PUBLISHED</span>
                                @elseif($course->status === 'pending')
                                    <span class="badge" style="background: rgba(245,158,11,0.2); color: #f59e0b;">PENDING APPROVAL</span>
                                @else
                                    <span class="badge" style="background: rgba(148,163,184,0.2); color: #94a3b8;">DRAFT</span>
                                @endif
                            </td>
                            <td style="padding: 12px; text-align: right;">
                                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                    <a href="{{ route('trainer.courses.builder', $course->id) }}" style="color: #38bdf8; font-weight: 600; text-decoration: none;">
                                        Syllabus Builder 🛠
                                    </a>
                                    <a href="{{ route('trainer.courses.edit', $course->id) }}" style="color: #60a5fa; font-weight: 600; text-decoration: none;">
                                        Edit ✏️
                                    </a>
                                    @if($course->status === 'draft')
                                        <form action="{{ route('trainer.courses.submit', $course->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" style="background: none; border: none; color: #f59e0b; font-weight: 700; cursor: pointer; font-size: 13px;">
                                                Submit for Approval 🚀
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 24px; text-align: center; color: #64748b;">No courses authored yet. Click "Create New Course" to start.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 12px;">
            {{ $courses->links() }}
        </div>
    </div>
</div>
@endsection

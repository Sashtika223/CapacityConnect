@extends('layouts.trainer')

@section('title', 'Manage Course Enrollments — ' . $course->title)

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">
    <!-- Top Header -->
    <div style="background: #1e293b; padding: 24px; border-radius: 14px; border: 1px solid #334155; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <a href="{{ route('trainer.courses.index') }}" style="font-size: 12px; color: #38bdf8; text-decoration: none; font-weight: 600;">← Back to Courses</a>
            <h1 style="font-size: 22px; font-weight: 800; color: #f8fafc; margin: 6px 0 2px 0;">{{ $course->title }}</h1>
            <p style="font-size: 13px; color: #94a3b8; margin: 0;">Learner roster, progress tracking, and intervention messaging.</p>
        </div>
        <span class="badge" style="background: rgba(59,130,246,0.2); color: #60a5fa; font-size: 13px; padding: 6px 14px;">
            Total Enrollees: {{ $enrollments->total() }}
        </span>
    </div>

    <!-- Enrollees Table -->
    <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 24px; overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
            <thead>
                <tr style="border-bottom: 1px solid #334155; color: #94a3b8; text-align: left;">
                    <th style="padding: 12px;">Learner Name</th>
                    <th style="padding: 12px;">Department</th>
                    <th style="padding: 12px;">Progress</th>
                    <th style="padding: 12px;">Status</th>
                    <th style="padding: 12px;">Last Active</th>
                    <th style="padding: 12px; text-align: right;">Intervention / Message</th>
                </tr>
            </thead>
            <tbody>
                @forelse($enrollments as $enr)
                    <tr style="border-bottom: 1px solid #334155; color: #e2e8f0;">
                        <td style="padding: 12px; font-weight: 700; color: #f8fafc;">
                            {{ $enr->user?->name }}
                        </td>
                        <td style="padding: 12px; color: #94a3b8;">{{ $enr->user?->department?->name ?? 'General Workforce' }}</td>
                        <td style="padding: 12px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <div style="flex: 1; min-width: 80px; background: #0f172a; height: 6px; border-radius: 3px; overflow: hidden;">
                                    <div style="width: {{ $enr->progress_percentage }}%; background: {{ $enr->progress_percentage >= 70 ? '#10b981' : ($enr->progress_percentage < 30 ? '#ef4444' : '#f59e0b') }}; height: 100%;"></div>
                                </div>
                                <span style="font-size: 12px; font-weight: 700;">{{ $enr->progress_percentage }}%</span>
                            </div>
                        </td>
                        <td style="padding: 12px;">
                            <span class="badge" style="background: {{ $enr->status === 'completed' ? 'rgba(16,185,129,0.2)' : ($enr->progress_percentage < 30 ? 'rgba(239,68,68,0.2)' : 'rgba(59,130,246,0.2)') }}; color: {{ $enr->status === 'completed' ? '#34d399' : ($enr->progress_percentage < 30 ? '#f87171' : '#60a5fa') }}; font-weight: 700;">
                                {{ $enr->progress_percentage < 30 && $enr->status !== 'completed' ? 'STRUGGLING' : strtoupper($enr->status) }}
                            </span>
                        </td>
                        <td style="padding: 12px; color: #94a3b8;">{{ $enr->last_accessed_at ? $enr->last_accessed_at->diffForHumans() : 'Never' }}</td>
                        <td style="padding: 12px; text-align: right;">
                            <form action="{{ route('trainer.courses.message-learner', ['course' => $course->id, 'user' => $enr->user_id]) }}" method="POST" style="display: inline-flex; gap: 6px;">
                                @csrf
                                <input type="text" name="message" required placeholder="Quick message/tip..." style="padding: 4px 8px; background: #0f172a; border: 1px solid #334155; border-radius: 4px; color: #fff; font-size: 11px; width: 150px;">
                                <button type="submit" class="btn btn-primary" style="background: #2563eb; color: #fff; font-size: 11px; padding: 4px 10px; border-radius: 4px; border: none; font-weight: 700; cursor: pointer;">Send</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding: 24px; text-align: center; color: #64748b;">No trainees currently enrolled.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 16px;">
            {{ $enrollments->links() }}
        </div>
    </div>
</div>
@endsection

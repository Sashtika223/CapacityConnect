@extends('layouts.admin')

@section('title', 'Admin Course Governance')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">
    <!-- Top Header -->
    <div style="background: #1e293b; padding: 24px; border-radius: 14px; border: 1px solid #334155; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 24px; font-weight: 700; color: #f8fafc; margin: 0 0 4px 0;">Course Catalog Governance & Approvals</h1>
            <p style="font-size: 14px; color: #94a3b8; margin: 0;">Review submitted courses, verify curriculum quality, and approve public catalog publication.</p>
        </div>

        <!-- Filter tabs -->
        <div style="display: flex; background: #0f172a; padding: 4px; border-radius: 8px; border: 1px solid #334155;">
            <a href="{{ route('admin.courses.index', ['status' => 'all']) }}" style="padding: 8px 14px; border-radius: 6px; font-size: 12px; font-weight: 600; text-decoration: none; color: {{ $status === 'all' ? '#fff' : '#94a3b8' }}; background: {{ $status === 'all' ? '#2563eb' : 'transparent' }};">
                All
            </a>
            <a href="{{ route('admin.courses.index', ['status' => 'pending']) }}" style="padding: 8px 14px; border-radius: 6px; font-size: 12px; font-weight: 600; text-decoration: none; color: {{ $status === 'pending' ? '#fff' : '#94a3b8' }}; background: {{ $status === 'pending' ? '#2563eb' : 'transparent' }};">
                Pending
            </a>
            <a href="{{ route('admin.courses.index', ['status' => 'published']) }}" style="padding: 8px 14px; border-radius: 6px; font-size: 12px; font-weight: 600; text-decoration: none; color: {{ $status === 'published' ? '#fff' : '#94a3b8' }}; background: {{ $status === 'published' ? '#2563eb' : 'transparent' }};">
                Published
            </a>
        </div>
    </div>

    <!-- Courses Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px;">
        @forelse($courses as $course)
            <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 20px; display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <span class="badge" style="background: {{ $course->status === 'published' ? 'rgba(16,185,129,0.2)' : ($course->status === 'pending' ? 'rgba(245,158,11,0.2)' : 'rgba(148,163,184,0.2)') }}; color: {{ $course->status === 'published' ? '#34d399' : ($course->status === 'pending' ? '#f59e0b' : '#cbd5e1') }}; font-size: 11px;">
                            {{ strtoupper($course->status) }}
                        </span>
                        <span style="font-size: 11px; color: #94a3b8;">Dept: {{ $course->department?->name }}</span>
                    </div>

                    <h3 style="font-size: 16px; font-weight: 700; color: #f8fafc; margin: 0 0 6px 0;">{{ $course->title }}</h3>
                    <div style="font-size: 12px; color: #38bdf8; margin-bottom: 12px;">Trainer: {{ $course->trainer?->name }}</div>
                    <p style="font-size: 12px; color: #cbd5e1; margin: 0 0 14px 0; line-clamp: 2; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                        {{ $course->short_description }}
                    </p>
                </div>

                <div style="padding-top: 14px; border-top: 1px solid #334155; display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 12px; color: #64748b;">{{ $course->modules->count() }} Modules</span>
                    <a href="{{ route('admin.courses.show', $course->id) }}" class="btn btn-primary" style="background: #2563eb; color: #fff; text-decoration: none; padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: 700;">
                        Inspect Course ➔
                    </a>
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #64748b; background: #1e293b; border-radius: 14px;">
                No courses found under this filter status.
            </div>
        @endforelse
    </div>
    <div style="margin-top: 12px;">
        {{ $courses->links() }}
    </div>
</div>
@endsection

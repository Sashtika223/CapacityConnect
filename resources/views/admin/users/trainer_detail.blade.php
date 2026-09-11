@extends('layouts.admin')

@section('title', 'Trainer Profile — {{ $user->name }}')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    {{-- Flash Messages --}}
    @if(session('success'))
        <div style="background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.5); color: #34d399; padding: 14px 20px; border-radius: 10px; font-size: 14px; font-weight: 600; display:flex; align-items:center; gap:10px;">
            <span>✅</span> {{ session('success') }}
        </div>
    @endif
    @if(session('info'))
        <div style="background: rgba(56,189,248,0.12); border: 1px solid rgba(56,189,248,0.5); color: #38bdf8; padding: 14px 20px; border-radius: 10px; font-size: 14px; font-weight: 600; display:flex; align-items:center; gap:10px;">
            <span>ℹ️</span> {{ session('info') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.5); color: #f87171; padding: 14px 20px; border-radius: 10px; font-size: 14px; font-weight: 600; display:flex; align-items:center; gap:10px;">
            <span>⚠️</span> {{ session('error') }}
        </div>
    @endif

    {{-- Back + Header --}}
    <div style="display:flex; align-items:center; gap:12px;">
        <a href="{{ route('admin.trainers.index') }}"
           style="background:#1e293b; border:1px solid #334155; color:#94a3b8; padding:8px 16px; border-radius:8px; font-size:13px; font-weight:600; text-decoration:none; display:flex; align-items:center; gap:6px;">
            ← Back to Trainer Board
        </a>
    </div>

    {{-- Profile Hero --}}
    <div style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); padding: 32px; border-radius: 16px; border: 1px solid #334155; display:flex; align-items:center; gap:24px; flex-wrap:wrap;">
        <div style="width:72px; height:72px; border-radius:50%; background:linear-gradient(135deg,#1d4ed8,#0ea5e9); display:flex; align-items:center; justify-content:center; font-size:28px; font-weight:800; color:#fff; flex-shrink:0;">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <div style="flex:1; min-width:0;">
            <h1 style="font-size:22px; font-weight:800; color:#f8fafc; margin:0;">{{ $user->name }}</h1>
            <div style="font-size:13px; color:#38bdf8; margin-top:4px;">{{ $user->profile?->job_title ?? 'Trainer' }}</div>
            <div style="font-size:12px; color:#64748b; margin-top:2px;">Registered {{ $user->created_at->format('d M Y') }} · {{ $user->created_at->diffForHumans() }}</div>
        </div>
        {{-- Status Badge --}}
        @php
            $statusStyles = [
                'pending'  => 'background:rgba(251,191,36,0.15); color:#fbbf24; border:1px solid rgba(251,191,36,0.3);',
                'approved' => 'background:rgba(16,185,129,0.15); color:#34d399; border:1px solid rgba(16,185,129,0.3);',
                'rejected' => 'background:rgba(239,68,68,0.12); color:#f87171; border:1px solid rgba(239,68,68,0.3);',
                'suspended'=> 'background:rgba(148,163,184,0.12); color:#94a3b8; border:1px solid #334155;',
            ];
            $ss = $statusStyles[$user->status] ?? 'background:#1e293b; color:#94a3b8; border:1px solid #334155;';
        @endphp
        <div style="{{ $ss }} padding:8px 20px; border-radius:20px; font-size:12px; font-weight:800; letter-spacing:0.8px; text-transform:uppercase;">
            {{ $user->status }}
        </div>
    </div>

    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:24px; flex-wrap:wrap;">

        {{-- ===== Basic Information ===== --}}
        <div style="background:#1e293b; border-radius:14px; border:1px solid #334155; overflow:hidden;">
            <div style="padding:16px 24px; border-bottom:1px solid #334155; font-size:14px; font-weight:700; color:#f8fafc; display:flex; align-items:center; gap:8px;">
                📋 Basic Information
            </div>
            <div style="padding:20px 24px; display:flex; flex-direction:column; gap:14px;">
                @php
                    $row = function(string $label, string $value, string $color = '#e2e8f0') {
                        return "<div style='display:flex; flex-direction:column; gap:3px;'>
                            <div style='font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px;'>$label</div>
                            <div style='font-size:14px; color:$color;'>$value</div>
                        </div>";
                    };
                @endphp
                {!! $row('Full Name', e($user->name)) !!}
                {!! $row('Email Address', e($user->email), '#38bdf8') !!}
                {!! $row('Phone', e($user->phone ?? '—')) !!}
                {!! $row('Department', e($user->department?->name ?? '—')) !!}
                {!! $row('Role', 'Trainer') !!}
                {!! $row('Registration Date', e($user->created_at->format('d M Y, h:i A'))) !!}
                @if($user->approved_at)
                    {!! $row('Approved On', e($user->approved_at->format('d M Y')), '#34d399') !!}
                @endif
                @if($user->rejected_at)
                    {!! $row('Rejected On', e($user->rejected_at->format('d M Y')), '#f87171') !!}
                @endif
            </div>
        </div>

        {{-- ===== Profile Details ===== --}}
        <div style="background:#1e293b; border-radius:14px; border:1px solid #334155; overflow:hidden;">
            <div style="padding:16px 24px; border-bottom:1px solid #334155; font-size:14px; font-weight:700; color:#f8fafc; display:flex; align-items:center; gap:8px;">
                👤 Profile Details
            </div>
            <div style="padding:20px 24px; display:flex; flex-direction:column; gap:14px;">
                {!! $row('Job Title', e($user->profile?->job_title ?? '—')) !!}
                {!! $row('Location', e($user->profile?->location ?? '—')) !!}
                {!! $row('Emergency Unit', e($user->profile?->emergency_unit ?? '—')) !!}
                <div style="display:flex; flex-direction:column; gap:3px;">
                    <div style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px;">Bio</div>
                    <div style="font-size:13px; color:#cbd5e1; line-height:1.6;">{{ $user->profile?->bio ?? 'No bio provided.' }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Qualifications ===== --}}
    <div style="background:#1e293b; border-radius:14px; border:1px solid #334155; overflow:hidden;">
        <div style="padding:16px 24px; border-bottom:1px solid #334155; font-size:14px; font-weight:700; color:#f8fafc; display:flex; align-items:center; gap:8px;">
            🎓 Qualifications
        </div>
        <div style="padding:20px 24px;">
            @if($user->qualifications->isEmpty())
                <p style="color:#64748b; font-size:13px; font-style:italic;">No qualifications recorded.</p>
            @else
                <div style="display:flex; flex-direction:column; gap:12px;">
                    @foreach($user->qualifications as $qual)
                        <div style="background:#0f172a; border:1px solid #334155; border-radius:10px; padding:14px 18px;">
                            <div style="font-size:14px; font-weight:700; color:#f8fafc;">{{ $qual->degree }}</div>
                            <div style="font-size:13px; color:#38bdf8; margin-top:3px;">{{ $qual->institution }}</div>
                            @if($qual->field_of_study)
                                <div style="font-size:12px; color:#94a3b8; margin-top:2px;">Field: {{ $qual->field_of_study }}</div>
                            @endif
                            @if($qual->graduation_year)
                                <div style="font-size:12px; color:#64748b; margin-top:2px;">Graduated: {{ $qual->graduation_year }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ===== Professional Experience ===== --}}
    <div style="background:#1e293b; border-radius:14px; border:1px solid #334155; overflow:hidden;">
        <div style="padding:16px 24px; border-bottom:1px solid #334155; font-size:14px; font-weight:700; color:#f8fafc; display:flex; align-items:center; gap:8px;">
            💼 Professional Experience
        </div>
        <div style="padding:20px 24px;">
            @if($user->experiences->isEmpty())
                <p style="color:#64748b; font-size:13px; font-style:italic;">No experience entries recorded.</p>
            @else
                <div style="display:flex; flex-direction:column; gap:12px;">
                    @foreach($user->experiences as $exp)
                        <div style="background:#0f172a; border:1px solid #334155; border-radius:10px; padding:14px 18px; position:relative;">
                            <div style="font-size:14px; font-weight:700; color:#f8fafc;">{{ $exp->position }}</div>
                            <div style="font-size:13px; color:#38bdf8; margin-top:3px;">{{ $exp->organization }}</div>
                            <div style="font-size:12px; color:#64748b; margin-top:4px;">
                                {{ \Carbon\Carbon::parse($exp->start_date)->format('M Y') }} —
                                {{ $exp->is_current ? 'Present' : ($exp->end_date ? \Carbon\Carbon::parse($exp->end_date)->format('M Y') : '—') }}
                            </div>
                            @if($exp->description)
                                <div style="font-size:12px; color:#94a3b8; margin-top:6px; line-height:1.5;">{{ $exp->description }}</div>
                            @endif
                            @if($exp->is_current)
                                <span style="position:absolute; top:14px; right:14px; background:rgba(16,185,129,0.15); color:#34d399; font-size:10px; font-weight:700; padding:3px 8px; border-radius:10px;">CURRENT</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ===== Authored Courses ===== --}}
    <div style="background:#1e293b; border-radius:14px; border:1px solid #334155; overflow:hidden;">
        <div style="padding:16px 24px; border-bottom:1px solid #334155; font-size:14px; font-weight:700; color:#f8fafc; display:flex; align-items:center; justify-content:space-between;">
            <span>📚 Authored Courses</span>
            <span style="font-size:13px; color:#64748b; font-weight:400;">{{ $user->authoredCourses->count() }} total</span>
        </div>
        <div style="padding:20px 24px;">
            @if($user->authoredCourses->isEmpty())
                <p style="color:#64748b; font-size:13px; font-style:italic;">No courses authored yet.</p>
            @else
                <div style="display:flex; flex-direction:column; gap:8px;">
                    @foreach($user->authoredCourses as $course)
                        <div style="display:flex; align-items:center; justify-content:space-between; background:#0f172a; border:1px solid #334155; border-radius:8px; padding:12px 16px;">
                            <span style="color:#e2e8f0; font-size:13px; font-weight:600;">{{ $course->title }}</span>
                            <span style="font-size:11px; font-weight:700; padding:3px 10px; border-radius:10px;
                                {{ $course->status === 'published' ? 'background:rgba(16,185,129,0.15); color:#34d399;' : 'background:rgba(148,163,184,0.1); color:#94a3b8;' }}">
                                {{ strtoupper($course->status) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ===== Approval Actions ===== --}}
    @if($user->status === 'pending')
        <div style="background:#1e293b; border:1px solid #334155; border-radius:14px; padding:24px;">
            <h3 style="font-size:15px; font-weight:700; color:#f8fafc; margin:0 0 16px;">Review Decision</h3>
            <div style="display:flex; gap:12px; flex-wrap:wrap;">
                <form action="{{ route('admin.trainers.approve', $user) }}" method="POST"
                      onsubmit="return confirm('Approve this trainer? An approval email will be sent to {{ addslashes($user->email) }}.')">
                    @csrf
                    <button type="submit"
                            style="background:rgba(16,185,129,0.15); color:#34d399; border:1px solid rgba(16,185,129,0.4); padding:12px 28px; border-radius:10px; font-size:14px; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:8px;">
                        ✓ Approve Trainer
                    </button>
                </form>
                <button type="button"
                        onclick="openDetailRejectModal({{ $user->id }}, '{{ addslashes($user->name) }}')"
                        style="background:rgba(239,68,68,0.12); color:#f87171; border:1px solid rgba(239,68,68,0.35); padding:12px 28px; border-radius:10px; font-size:14px; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:8px;">
                    ✕ Reject Application
                </button>
            </div>
        </div>
    @endif
</div>

{{-- Reject Modal --}}
<div id="detailRejectModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#1e293b; border:1px solid #334155; border-radius:16px; padding:32px; max-width:480px; width:90%;">
        <h3 style="font-size:17px; font-weight:800; color:#f8fafc; margin:0 0 6px;">Reject Trainer Application</h3>
        <p id="detailRejectName" style="font-size:13px; color:#94a3b8; margin:0 0 20px;"></p>
        <form id="detailRejectForm" method="POST">
            @csrf
            <div style="margin-bottom:16px;">
                <label style="font-size:13px; font-weight:600; color:#94a3b8; display:block; margin-bottom:8px;">
                    Rejection Reason <span style="color:#64748b; font-weight:400;">(optional — internal record only)</span>
                </label>
                <textarea name="rejection_reason" rows="3"
                          style="width:100%; background:#0f172a; border:1px solid #334155; border-radius:8px; color:#e2e8f0; padding:12px; font-size:13px; resize:vertical; outline:none;"
                          placeholder="e.g. Insufficient qualifications, incomplete application..."></textarea>
            </div>
            <div style="display:flex; gap:12px; justify-content:flex-end;">
                <button type="button" onclick="document.getElementById('detailRejectModal').style.display='none'"
                        style="background:transparent; color:#94a3b8; border:1px solid #334155; padding:10px 20px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer;">
                    Cancel
                </button>
                <button type="submit"
                        style="background:rgba(239,68,68,0.2); color:#f87171; border:1px solid rgba(239,68,68,0.5); padding:10px 24px; border-radius:8px; font-size:13px; font-weight:700; cursor:pointer;">
                    ✕ Confirm Rejection
                </button>
            </div>
        </form>
    </div>
</div>
<script>
function openDetailRejectModal(id, name) {
    document.getElementById('detailRejectName').textContent = 'Rejecting application from: ' + name;
    document.getElementById('detailRejectForm').action = '/admin/trainers/' + id + '/reject';
    document.getElementById('detailRejectModal').style.display = 'flex';
}
</script>
@endsection

@extends('layouts.admin')

@section('title', 'Trainer Approval Requests — Capacity Connect')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    {{-- Flash Messages --}}
    @if(session('success'))
        <div style="background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.5); color: #34d399; padding: 14px 20px; border-radius: 10px; font-size: 14px; font-weight: 600; display:flex; align-items:center; gap:10px;">
            <span style="font-size:18px;">✅</span> {{ session('success') }}
        </div>
    @endif
    @if(session('warning'))
        <div style="background: rgba(251,191,36,0.12); border: 1px solid rgba(251,191,36,0.5); color: #fbbf24; padding: 14px 20px; border-radius: 10px; font-size: 14px; font-weight: 600; display:flex; align-items:flex-start; gap:10px;">
            <span style="font-size:18px; flex-shrink:0;">⚠️</span>
            <span>{{ session('warning') }}</span>
        </div>
    @endif
    @if(session('info'))
        <div style="background: rgba(56,189,248,0.12); border: 1px solid rgba(56,189,248,0.5); color: #38bdf8; padding: 14px 20px; border-radius: 10px; font-size: 14px; font-weight: 600; display:flex; align-items:center; gap:10px;">
            <span style="font-size:18px;">ℹ️</span> {{ session('info') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.5); color: #f87171; padding: 14px 20px; border-radius: 10px; font-size: 14px; font-weight: 600; display:flex; align-items:center; gap:10px;">
            <span style="font-size:18px;">⚠️</span> {{ session('error') }}
        </div>
    @endif

    {{-- Page Header --}}
    <div style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); padding: 28px 32px; border-radius: 16px; border: 1px solid #334155;">
        <div style="display:flex; align-items:center; gap:14px; margin-bottom:6px;">
            <div style="background: rgba(251,191,36,0.15); border: 1px solid rgba(251,191,36,0.3); border-radius:10px; padding:10px 14px; font-size:22px;">⏳</div>
            <div>
                <h1 style="font-size: 22px; font-weight: 800; color: #f8fafc; margin:0;">Trainer Approval Requests</h1>
                <p style="font-size: 13px; color: #94a3b8; margin:4px 0 0;">Review pending trainer registrations and approve or reject applications.</p>
            </div>
        </div>
        <div style="display:flex; gap:20px; margin-top:18px;">
            <div style="background:#0f172a; border:1px solid #334155; border-radius:10px; padding:12px 20px; text-align:center; min-width:100px;">
                <div style="font-size:26px; font-weight:800; color:#fbbf24;">{{ $pendingTrainers->count() }}</div>
                <div style="font-size:11px; color:#94a3b8; margin-top:2px;">PENDING</div>
            </div>
            <div style="background:#0f172a; border:1px solid #334155; border-radius:10px; padding:12px 20px; text-align:center; min-width:100px;">
                <div style="font-size:26px; font-weight:800; color:#34d399;">{{ $approvedTrainers->total() }}</div>
                <div style="font-size:11px; color:#94a3b8; margin-top:2px;">APPROVED</div>
            </div>
            <div style="background:#0f172a; border:1px solid #334155; border-radius:10px; padding:12px 20px; text-align:center; min-width:100px;">
                <div style="font-size:26px; font-weight:800; color:#f87171;">{{ $rejectedCount }}</div>
                <div style="font-size:11px; color:#94a3b8; margin-top:2px;">REJECTED</div>
            </div>
        </div>
    </div>

    {{-- ========== PENDING APPLICATIONS ========== --}}
    <div style="background: #1e293b; border-radius: 16px; border: 1px solid #334155; overflow:hidden;">
        <div style="padding: 20px 28px; border-bottom: 1px solid #334155; display:flex; align-items:center; gap:10px;">
            <span style="font-size:18px;">⏳</span>
            <h2 style="font-size: 17px; font-weight: 700; color: #f8fafc; margin:0;">Pending Applications</h2>
            @if($pendingTrainers->count() > 0)
                <span style="background:rgba(251,191,36,0.2); color:#fbbf24; font-size:11px; font-weight:700; padding:3px 10px; border-radius:20px; margin-left:4px;">{{ $pendingTrainers->count() }} awaiting review</span>
            @endif
        </div>

        @if($pendingTrainers->isEmpty())
            <div style="padding: 40px; text-align:center; color:#64748b;">
                <div style="font-size:36px; margin-bottom:10px;">🎉</div>
                <div style="font-size:14px;">No pending trainer applications. All caught up!</div>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width:100%; border-collapse:collapse; font-size:13px;">
                    <thead>
                        <tr style="background:#0f172a; color:#64748b; font-size:11px; font-weight:700; letter-spacing:0.5px; text-transform:uppercase;">
                            <th style="padding:12px 16px; text-align:left;">Name</th>
                            <th style="padding:12px 16px; text-align:left;">Email</th>
                            <th style="padding:12px 16px; text-align:left;">Department</th>
                            <th style="padding:12px 16px; text-align:left;">Registration Date</th>
                            <th style="padding:12px 16px; text-align:left;">Qualification</th>
                            <th style="padding:12px 16px; text-align:left;">Experience</th>
                            <th style="padding:12px 16px; text-align:left;">Status</th>
                            <th style="padding:12px 16px; text-align:center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingTrainers as $trainer)
                            <tr style="border-top: 1px solid #1e293b; transition: background 0.15s;" onmouseover="this.style.background='#0f172a'" onmouseout="this.style.background='transparent'">
                                <td style="padding:14px 16px;">
                                    <div style="font-weight:700; color:#f8fafc;">{{ $trainer->name }}</div>
                                    <div style="font-size:11px; color:#64748b; margin-top:2px;">{{ $trainer->profile?->job_title ?? 'Trainer' }}</div>
                                </td>
                                <td style="padding:14px 16px; color:#38bdf8;">{{ $trainer->email }}</td>
                                <td style="padding:14px 16px; color:#cbd5e1;">{{ $trainer->department?->name ?? '—' }}</td>
                                <td style="padding:14px 16px; color:#94a3b8;">
                                    <div>{{ $trainer->created_at->format('d M Y') }}</div>
                                    <div style="font-size:11px; color:#475569;">{{ $trainer->created_at->diffForHumans() }}</div>
                                </td>
                                <td style="padding:14px 16px; color:#94a3b8;">
                                    @if($trainer->qualifications->isNotEmpty())
                                        <div style="color:#e2e8f0; font-weight:600;">{{ $trainer->qualifications->first()->degree }}</div>
                                        <div style="font-size:11px; color:#64748b;">{{ $trainer->qualifications->first()->institution }}</div>
                                    @else
                                        <span style="color:#475569; font-style:italic;">Not provided</span>
                                    @endif
                                </td>
                                <td style="padding:14px 16px; color:#94a3b8;">
                                    @if($trainer->experiences->isNotEmpty())
                                        <div style="color:#e2e8f0; font-weight:600;">{{ $trainer->experiences->first()->position }}</div>
                                        <div style="font-size:11px; color:#64748b;">{{ $trainer->experiences->first()->organization }}</div>
                                    @else
                                        <span style="color:#475569; font-style:italic;">Not provided</span>
                                    @endif
                                </td>
                                <td style="padding:14px 16px;">
                                    <span style="background:rgba(251,191,36,0.15); color:#fbbf24; font-size:11px; font-weight:700; padding:4px 10px; border-radius:20px; letter-spacing:0.5px;">PENDING</span>
                                </td>
                                <td style="padding:14px 16px; text-align:center;">
                                    <div style="display:flex; gap:8px; justify-content:center; flex-wrap:wrap;">
                                        {{-- VIEW --}}
                                        <a href="{{ route('admin.trainers.show', $trainer->id) }}"
                                           style="background:rgba(56,189,248,0.15); color:#38bdf8; border:1px solid rgba(56,189,248,0.3); padding:6px 14px; border-radius:6px; font-size:12px; font-weight:700; text-decoration:none; white-space:nowrap;">
                                            👁 View
                                        </a>
                                        {{-- APPROVE --}}
                                        <form action="{{ route('admin.trainers.approve', $trainer->id) }}" method="POST" style="display:inline;"
                                              onsubmit="return confirm('Approve trainer {{ addslashes($trainer->name) }}? An approval email will be sent.')">
                                            @csrf
                                            <button type="submit"
                                                    style="background:rgba(16,185,129,0.15); color:#34d399; border:1px solid rgba(16,185,129,0.4); padding:6px 14px; border-radius:6px; font-size:12px; font-weight:700; cursor:pointer; white-space:nowrap;">
                                                ✓ Approve
                                            </button>
                                        </form>
                                        {{-- REJECT (triggers modal) --}}
                                        <button type="button"
                                                onclick="openRejectModal({{ $trainer->id }}, '{{ addslashes($trainer->name) }}')"
                                                style="background:rgba(239,68,68,0.12); color:#f87171; border:1px solid rgba(239,68,68,0.35); padding:6px 14px; border-radius:6px; font-size:12px; font-weight:700; cursor:pointer; white-space:nowrap;">
                                            ✕ Reject
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- ========== APPROVED TRAINERS ========== --}}
    <div style="background: #1e293b; border-radius: 16px; border: 1px solid #334155; overflow:hidden;">
        <div style="padding: 20px 28px; border-bottom: 1px solid #334155; display:flex; align-items:center; gap:10px;">
            <span style="font-size:18px;">🎓</span>
            <h2 style="font-size: 17px; font-weight: 700; color: #f8fafc; margin:0;">Approved Instructors</h2>
        </div>

        <div style="overflow-x: auto;">
            <table style="width:100%; border-collapse:collapse; font-size:13px;">
                <thead>
                    <tr style="background:#0f172a; color:#64748b; font-size:11px; font-weight:700; letter-spacing:0.5px; text-transform:uppercase;">
                        <th style="padding:12px 16px; text-align:left;">Name</th>
                        <th style="padding:12px 16px; text-align:left;">Email</th>
                        <th style="padding:12px 16px; text-align:left;">Department</th>
                        <th style="padding:12px 16px; text-align:left;">Approved On</th>
                        <th style="padding:12px 16px; text-align:left;">Courses</th>
                        <th style="padding:12px 16px; text-align:left;">Status</th>
                        <th style="padding:12px 16px; text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($approvedTrainers as $at)
                        <tr style="border-top: 1px solid #0f172a;" onmouseover="this.style.background='#0f172a'" onmouseout="this.style.background='transparent'">
                            <td style="padding:14px 16px; font-weight:700; color:#f8fafc;">{{ $at->name }}</td>
                            <td style="padding:14px 16px; color:#38bdf8;">{{ $at->email }}</td>
                            <td style="padding:14px 16px; color:#cbd5e1;">{{ $at->department?->name ?? '—' }}</td>
                            <td style="padding:14px 16px; color:#94a3b8;">{{ $at->approved_at?->format('d M Y') ?? '—' }}</td>
                            <td style="padding:14px 16px; color:#38bdf8; font-weight:700;">{{ $at->authoredCourses->count() }}</td>
                            <td style="padding:14px 16px;">
                                <span style="background:rgba(16,185,129,0.15); color:#34d399; font-size:11px; font-weight:700; padding:4px 10px; border-radius:20px; letter-spacing:0.5px;">APPROVED</span>
                            </td>
                            <td style="padding:14px 16px; text-align:center;">
                                <a href="{{ route('admin.trainers.show', $at->id) }}"
                                   style="background:rgba(56,189,248,0.15); color:#38bdf8; border:1px solid rgba(56,189,248,0.3); padding:6px 14px; border-radius:6px; font-size:12px; font-weight:700; text-decoration:none;">
                                    👁 View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding:30px; text-align:center; color:#64748b; font-size:13px;">No approved trainers yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding:16px 28px; border-top:1px solid #0f172a;">
            {{ $approvedTrainers->links() }}
        </div>
    </div>
</div>

{{-- ========== REJECT MODAL ========== --}}
<div id="rejectModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#1e293b; border:1px solid #334155; border-radius:16px; padding:32px; max-width:480px; width:90%; box-shadow:0 25px 60px rgba(0,0,0,0.5);">
        <h3 style="font-size:18px; font-weight:800; color:#f8fafc; margin:0 0 6px;">Reject Trainer Application</h3>
        <p id="rejectModalName" style="font-size:13px; color:#94a3b8; margin:0 0 20px;"></p>

        <form id="rejectForm" method="POST">
            @csrf
            @method('POST')
            <div style="margin-bottom:16px;">
                <label style="font-size:13px; font-weight:600; color:#94a3b8; display:block; margin-bottom:8px;">
                    Rejection Reason <span style="color:#64748b; font-weight:400;">(optional — not sent to trainer)</span>
                </label>
                <textarea name="rejection_reason" rows="3"
                          style="width:100%; background:#0f172a; border:1px solid #334155; border-radius:8px; color:#e2e8f0; padding:12px; font-size:13px; resize:vertical; outline:none;"
                          placeholder="e.g. Incomplete qualifications, duplicate application..."></textarea>
            </div>
            <div style="display:flex; gap:12px; justify-content:flex-end; margin-top:20px;">
                <button type="button" onclick="closeRejectModal()"
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
function openRejectModal(id, name) {
    document.getElementById('rejectModalName').textContent = 'You are rejecting the application from: ' + name;
    document.getElementById('rejectForm').action = '/admin/trainers/' + id + '/reject';
    const modal = document.getElementById('rejectModal');
    modal.style.display = 'flex';
}
function closeRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
}
document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) closeRejectModal();
});
</script>
@endsection

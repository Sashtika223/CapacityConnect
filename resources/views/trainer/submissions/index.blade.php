@extends('layouts.trainer')

@section('title', 'Practical Submissions Review')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">
    <!-- Top Header -->
    <div style="background: #1e293b; padding: 24px; border-radius: 14px; border: 1px solid #334155; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 24px; font-weight: 700; color: #f8fafc; margin: 0 0 4px 0;">Practical Assessment Submissions</h1>
            <p style="font-size: 14px; color: #94a3b8; margin: 0;">Review, grade, and provide qualitative feedback on trainee scenario responses.</p>
        </div>
    </div>

    <!-- Submissions Table -->
    <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 24px;">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
                <thead>
                    <tr style="border-bottom: 1px solid #334155; color: #94a3b8;">
                        <th style="padding: 12px;">Trainee Name</th>
                        <th style="padding: 12px;">Practical Scenario</th>
                        <th style="padding: 12px;">Course Title</th>
                        <th style="padding: 12px;">Submitted Date</th>
                        <th style="padding: 12px;">Status</th>
                        <th style="padding: 12px; text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submissions as $sub)
                        <tr style="border-bottom: 1px solid #0f172a; color: #e2e8f0;">
                            <td style="padding: 12px; font-weight: 700;">
                                <div>{{ $sub->user?->name }}</div>
                                <div style="font-size: 11px; color: #64748b;">{{ $sub->user?->department?->name }}</div>
                            </td>
                            <td style="padding: 12px; color: #38bdf8; font-weight: 600;">
                                {{ $sub->practicalAssessment?->title }}
                            </td>
                            <td style="padding: 12px; color: #cbd5e1;">
                                {{ $sub->practicalAssessment?->course?->title }}
                            </td>
                            <td style="padding: 12px; color: #94a3b8;">
                                {{ $sub->created_at->format('M d, Y H:i') }}
                            </td>
                            <td style="padding: 12px;">
                                @if($sub->status === 'passed')
                                    <span class="badge" style="background: rgba(16,185,129,0.2); color: #34d399;">PASSED ({{ $sub->score }} pts)</span>
                                @elseif($sub->status === 'submitted')
                                    <span class="badge" style="background: rgba(245,158,11,0.2); color: #f59e0b;">PENDING REVIEW</span>
                                @else
                                    <span class="badge" style="background: rgba(239,68,68,0.2); color: #f87171;">{{ strtoupper($sub->status) }}</span>
                                @endif
                            </td>
                            <td style="padding: 12px; text-align: right;">
                                <a href="{{ route('trainer.submissions.show', $sub->id) }}" class="btn btn-primary" style="background: #2563eb; color: #fff; text-decoration: none; padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: 700;">
                                    Review & Grade ➔
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 24px; text-align: center; color: #64748b;">No practical submissions recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 12px;">
            {{ $submissions->links() }}
        </div>
    </div>
</div>
@endsection

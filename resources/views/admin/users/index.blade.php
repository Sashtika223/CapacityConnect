@extends('layouts.admin')

@section('title', 'User Governance & Roles')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">
    <!-- Top Header -->
    <div style="background: #1e293b; padding: 24px; border-radius: 14px; border: 1px solid #334155; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 24px; font-weight: 700; color: #f8fafc; margin: 0 0 4px 0;">User Management & Account Governance</h1>
            <p style="font-size: 14px; color: #94a3b8; margin: 0;">Manage platform personnel, active account status toggles, and role assignments.</p>
        </div>

        <div style="display: flex; gap: 12px;">
            <a href="{{ route('admin.trainers.index') }}" class="btn btn-secondary" style="background: #334155; color: #fff; text-decoration: none; padding: 10px 18px; border-radius: 8px; font-weight: 600; font-size: 13px;">
                Trainer Approvals Board 🎓
            </a>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <form action="{{ route('admin.users.index') }}" method="GET" style="background: #1e293b; padding: 16px; border-radius: 12px; border: 1px solid #334155; display: flex; gap: 16px; align-items: center; flex-wrap: wrap;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email address..." style="flex: 1; min-width: 240px; background: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 10px 14px; color: #fff; font-size: 14px;">

        <select name="role" style="background: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 10px 14px; color: #fff; font-size: 14px;">
            <option value="all">All Roles</option>
            <option value="admin" {{ $role === 'admin' ? 'selected' : '' }}>Administrators</option>
            <option value="trainer" {{ $role === 'trainer' ? 'selected' : '' }}>Trainers</option>
            <option value="trainee" {{ $role === 'trainee' ? 'selected' : '' }}>Trainees</option>
        </select>

        <select name="status" style="background: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 10px 14px; color: #fff; font-size: 14px;">
            <option value="all">All Statuses</option>
            <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="suspended" {{ $status === 'suspended' ? 'selected' : '' }}>Suspended</option>
        </select>

        <button type="submit" class="btn btn-primary" style="background: #2563eb; color: #fff; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 700; font-size: 14px; cursor: pointer;">
            Filter Users
        </button>
    </form>

    <!-- Users Table -->
    <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 24px;">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
                <thead>
                    <tr style="border-bottom: 1px solid #334155; color: #94a3b8;">
                        <th style="padding: 12px;">User Profile</th>
                        <th style="padding: 12px;">Department</th>
                        <th style="padding: 12px;">Role</th>
                        <th style="padding: 12px;">Status</th>
                        <th style="padding: 12px;">Joined Date</th>
                        <th style="padding: 12px; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $usr)
                        <tr style="border-bottom: 1px solid #0f172a; color: #e2e8f0;">
                            <td style="padding: 12px; font-weight: 700;">
                                <div style="color: #f8fafc;">{{ $usr->name }}</div>
                                <div style="font-size: 11px; color: #64748b;">{{ $usr->email }}</div>
                            </td>
                            <td style="padding: 12px; color: #cbd5e1;">{{ $usr->department?->name ?? 'Unassigned' }}</td>
                            <td style="padding: 12px;">
                                <span class="badge" style="background: {{ $usr->role === 'admin' ? 'rgba(139,92,246,0.2)' : ($usr->role === 'trainer' ? 'rgba(59,130,246,0.2)' : 'rgba(148,163,184,0.2)') }}; color: {{ $usr->role === 'admin' ? '#a78bfa' : ($usr->role === 'trainer' ? '#60a5fa' : '#cbd5e1') }};">
                                    {{ strtoupper($usr->role) }}
                                </span>
                            </td>
                            <td style="padding: 12px;">
                                @if($usr->status === 'active')
                                    <span class="badge" style="background: rgba(16,185,129,0.2); color: #34d399;">ACTIVE</span>
                                @elseif($usr->status === 'pending')
                                    <span class="badge" style="background: rgba(245,158,11,0.2); color: #f59e0b;">PENDING</span>
                                @else
                                    <span class="badge" style="background: rgba(239,68,68,0.2); color: #f87171;">SUSPENDED</span>
                                @endif
                            </td>
                            <td style="padding: 12px; color: #94a3b8;">{{ $usr->created_at->format('M d, Y') }}</td>
                            <td style="padding: 12px; text-align: right;">
                                @if(!($usr->isAdmin() && $usr->id === 1))
                                    <form action="{{ route('admin.users.toggle', $usr->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-secondary" style="background: {{ $usr->status === 'active' ? 'rgba(239,68,68,0.15)' : 'rgba(16,185,129,0.15)' }}; color: {{ $usr->status === 'active' ? '#f87171' : '#34d399' }}; border: none; padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight: 700; cursor: pointer;">
                                            {{ $usr->status === 'active' ? 'Suspend Account' : 'Activate Account' }}
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 24px; text-align: center; color: #64748b;">No matching users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 12px;">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection

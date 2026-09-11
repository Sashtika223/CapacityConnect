@extends('layouts.admin')

@section('title', 'System Audit Logs')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">
    <!-- Top Header -->
    <div style="background: #1e293b; padding: 24px; border-radius: 14px; border: 1px solid #334155; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 24px; font-weight: 700; color: #f8fafc; margin: 0 0 4px 0;">📜 System Security & Action Audit Logs</h1>
            <p style="font-size: 14px; color: #94a3b8; margin: 0;">Immutable action logging tracking authentication, course approvals, assessment grading, and role mutations.</p>
        </div>
    </div>

    <!-- Search Form -->
    <form action="{{ route('admin.audit-logs') }}" method="GET" style="background: #1e293b; padding: 16px; border-radius: 12px; border: 1px solid #334155; display: flex; gap: 14px;">
        <input type="text" name="search" value="{{ $search }}" placeholder="Filter by user name, action string, or IP address..." style="flex: 1; background: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 10px 14px; color: #fff; font-size: 14px;">
        <button type="submit" class="btn btn-primary" style="background: #2563eb; color: #fff; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 700; font-size: 14px; cursor: pointer;">
            Search Logs
        </button>
    </form>

    <!-- Logs Table -->
    <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 24px;">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
                <thead>
                    <tr style="border-bottom: 1px solid #334155; color: #94a3b8;">
                        <th style="padding: 12px;">Timestamp</th>
                        <th style="padding: 12px;">Actor User</th>
                        <th style="padding: 12px;">Action Event</th>
                        <th style="padding: 12px;">IP Address</th>
                        <th style="padding: 12px;">Payload Snapshot</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr style="border-bottom: 1px solid #0f172a; color: #e2e8f0;">
                            <td style="padding: 12px; color: #94a3b8; font-family: monospace;">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                            <td style="padding: 12px; font-weight: 700; color: #f8fafc;">
                                {{ $log->user?->name ?? 'System Process' }}
                            </td>
                            <td style="padding: 12px;">
                                <span class="badge" style="background: rgba(59,130,246,0.15); color: #60a5fa; font-family: monospace; font-size: 11px;">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td style="padding: 12px; font-family: monospace; color: #cbd5e1;">{{ $log->ip_address }}</td>
                            <td style="padding: 12px; font-family: monospace; font-size: 11px; color: #64748b; max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ json_encode($log->payload ?? $log->new_values) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding: 24px; text-align: center; color: #64748b;">No audit logs matched your query.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 12px;">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection

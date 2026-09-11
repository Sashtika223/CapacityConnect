@extends('layouts.admin')

@section('title', 'Certificate Governance & Expiry Monitoring')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">
    <!-- Header -->
    <div style="background: #1e293b; padding: 24px; border-radius: 14px; border: 1px solid #334155; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <span class="badge" style="background: rgba(16,185,129,0.2); color: #34d399; border: 1px solid rgba(16,185,129,0.4); margin-bottom: 6px; display: inline-block;">
                📜 Credential Governance
            </span>
            <h1 style="font-size: 24px; font-weight: 800; color: #f8fafc; margin: 0 0 4px 0;">Certificate Management & Deadlines</h1>
            <p style="font-size: 14px; color: #94a3b8; margin: 0;">Monitor issued certificates, track trainer validation deadlines, and administer revocations.</p>
        </div>
    </div>

    <!-- Stats Bar -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
        <a href="{{ route('admin.certificates.index') }}" style="text-decoration: none; background: #1e293b; padding: 18px; border-radius: 12px; border: 1px solid #334155;">
            <span style="font-size: 12px; color: #94a3b8;">Total Issued</span>
            <div style="font-size: 26px; font-weight: 700; color: #f8fafc; margin-top: 4px;">{{ $stats['total'] }}</div>
        </a>
        <a href="{{ route('admin.certificates.index', ['status' => 'valid']) }}" style="text-decoration: none; background: #1e293b; padding: 18px; border-radius: 12px; border: 1px solid #334155;">
            <span style="font-size: 12px; color: #94a3b8;">Active & Valid</span>
            <div style="font-size: 26px; font-weight: 700; color: #34d399; margin-top: 4px;">{{ $stats['valid'] }}</div>
        </a>
        <a href="{{ route('admin.certificates.index', ['status' => 'expiring_soon']) }}" style="text-decoration: none; background: #1e293b; padding: 18px; border-radius: 12px; border: 1px solid #334155;">
            <span style="font-size: 12px; color: #94a3b8;">Trainer Expiring Soon</span>
            <div style="font-size: 26px; font-weight: 700; color: #f59e0b; margin-top: 4px;">{{ $stats['expiring_soon'] }}</div>
        </a>
        <a href="{{ route('admin.certificates.index', ['status' => 'expired']) }}" style="text-decoration: none; background: #1e293b; padding: 18px; border-radius: 12px; border: 1px solid #334155;">
            <span style="font-size: 12px; color: #94a3b8;">Expired (Requires Renewal)</span>
            <div style="font-size: 26px; font-weight: 700; color: #f87171; margin-top: 4px;">{{ $stats['expired'] }}</div>
        </a>
    </div>

    <!-- Table Card -->
    <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 24px; overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
            <thead>
                <tr style="border-bottom: 1px solid #334155; color: #94a3b8; text-align: left;">
                    <th style="padding: 12px;">Certificate Number</th>
                    <th style="padding: 12px;">Recipient</th>
                    <th style="padding: 12px;">Role</th>
                    <th style="padding: 12px;">Course</th>
                    <th style="padding: 12px;">Issued</th>
                    <th style="padding: 12px;">Validity / Expiry</th>
                    <th style="padding: 12px;">Status</th>
                    <th style="padding: 12px; text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($certificates as $cert)
                    <tr style="border-bottom: 1px solid #334155; color: #e2e8f0;">
                        <td style="padding: 12px; font-family: monospace; font-weight: 700; color: #38bdf8;">
                            {{ $cert->certificate_number }}
                        </td>
                        <td style="padding: 12px; font-weight: 600;">{{ $cert->user?->name }}</td>
                        <td style="padding: 12px;">
                            <span class="badge" style="background: {{ $cert->user?->isTrainer() ? 'rgba(59,130,246,0.2)' : 'rgba(16,185,129,0.2)' }}; color: {{ $cert->user?->isTrainer() ? '#60a5fa' : '#34d399' }}; font-size: 10px;">
                                {{ strtoupper($cert->user?->role ?? 'user') }}
                            </span>
                        </td>
                        <td style="padding: 12px;">{{ $cert->course?->title }}</td>
                        <td style="padding: 12px;">{{ $cert->issue_date->format('M d, Y') }}</td>
                        <td style="padding: 12px;">
                            @if($cert->user?->isTrainee() || $cert->expiry_date === null)
                                <span style="color: #34d399;">Lifetime</span>
                            @else
                                <span style="color: {{ $cert->isExpired() ? '#f87171' : '#cbd5e1' }};">
                                    {{ $cert->expiry_date->format('M d, Y') }}
                                </span>
                            @endif
                        </td>
                        <td style="padding: 12px;">
                            <span class="badge" style="background: {{ $cert->isValid() ? 'rgba(16,185,129,0.2)' : 'rgba(239,68,68,0.2)' }}; color: {{ $cert->isValid() ? '#34d399' : '#f87171' }}; font-weight: 700;">
                                {{ strtoupper($cert->status) }}
                            </span>
                        </td>
                        <td style="padding: 12px; text-align: right;">
                            <a href="{{ route('certificate.verify', ['code' => $cert->certificate_number]) }}" target="_blank" style="color: #38bdf8; text-decoration: none; font-weight: 600; margin-right: 8px;">
                                Verify ↗
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="padding: 24px; text-align: center; color: #64748b;">No certificates found matching criteria.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 16px;">
            {{ $certificates->links() }}
        </div>
    </div>
</div>
@endsection

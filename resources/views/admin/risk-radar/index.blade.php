@extends('layouts.admin')

@section('title', 'Competency Risk Radar')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">
    <!-- Top Header -->
    <div style="background: #1e293b; padding: 24px; border-radius: 14px; border: 1px solid #334155; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <span class="badge" style="background: rgba(239,68,68,0.2); color: #f87171; border: 1px solid rgba(239,68,68,0.4); margin-bottom: 6px; display: inline-block;">
                🚨 Automated Operational Risk Radar
            </span>
            <h1 style="font-size: 24px; font-weight: 800; color: #f8fafc; margin: 0 0 4px 0;">Competency Risk Radar</h1>
            <p style="font-size: 14px; color: #94a3b8; margin: 0;">Rule-based risk detection identifying workforce vulnerability, expired disaster certifications, and operational skill deficits.</p>
        </div>

        <div style="background: #0f172a; border: 1px solid #334155; padding: 14px 22px; border-radius: 12px; text-align: center;">
            <div style="font-size: 11px; color: #94a3b8; text-transform: uppercase;">Overall System Risk</div>
            <div style="font-size: 24px; font-weight: 800; color: {{ $riskAnalysis['overall_status'] === 'High Risk' ? '#f87171' : '#34d399' }};">
                {{ strtoupper($riskAnalysis['overall_status']) }}
            </div>
        </div>
    </div>

    <!-- High Risk Alert Banners -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
        <div style="background: #1e293b; border-radius: 12px; border: 1px solid #334155; padding: 20px;">
            <div style="font-size: 12px; color: #94a3b8; font-weight: 600;">High-Risk Departments</div>
            <div style="font-size: 32px; font-weight: 800; color: #f87171; margin-top: 4px;">{{ $riskAnalysis['high_risk_department_count'] }}</div>
            <span style="font-size: 11px; color: #f87171;">Action required immediately</span>
        </div>

        <div style="background: #1e293b; border-radius: 12px; border: 1px solid #334155; padding: 20px;">
            <div style="font-size: 12px; color: #94a3b8; font-weight: 600;">Critical Skill Deficits</div>
            <div style="font-size: 32px; font-weight: 800; color: #f59e0b; margin-top: 4px;">{{ count($riskAnalysis['critical_deficits']) }}</div>
            <span style="font-size: 11px; color: #f59e0b;">Target competency gaps</span>
        </div>

        <div style="background: #1e293b; border-radius: 12px; border: 1px solid #334155; padding: 20px;">
            <div style="font-size: 12px; color: #94a3b8; font-weight: 600;">Expired / Expiring Certs</div>
            <div style="font-size: 32px; font-weight: 800; color: #38bdf8; margin-top: 4px;">{{ $riskAnalysis['expiring_certificates_count'] }}</div>
            <span style="font-size: 11px; color: #38bdf8;">Requires recertification</span>
        </div>
    </div>

    <!-- Department Risk Breakdown & Mitigations -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <!-- Left: Department Risk Assessment -->
        <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 24px;">
            <h3 style="font-size: 18px; font-weight: 700; color: #f8fafc; margin: 0 0 20px 0;">
                🏢 Department-by-Department Risk Matrix
            </h3>

            <div style="display: flex; flex-direction: column; gap: 16px;">
                @foreach($riskAnalysis['department_risks'] as $deptRisk)
                    <div style="background: #0f172a; padding: 18px; border-radius: 12px; border: 1px solid {{ $deptRisk['risk_level'] === 'High Risk' ? '#ef4444' : ($deptRisk['risk_level'] === 'Medium Risk' ? '#f59e0b' : '#334155') }};">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <div>
                                <h4 style="font-size: 16px; font-weight: 700; color: #f8fafc; margin: 0;">{{ $deptRisk['department_name'] }}</h4>
                                <span style="font-size: 12px; color: #94a3b8;">Personnel Count: {{ $deptRisk['user_count'] }} Trainees</span>
                            </div>
                            <span class="badge" style="background: {{ $deptRisk['risk_level'] === 'High Risk' ? 'rgba(239,68,68,0.2)' : ($deptRisk['risk_level'] === 'Medium Risk' ? 'rgba(245,158,11,0.2)' : 'rgba(16,185,129,0.2)') }}; color: {{ $deptRisk['risk_level'] === 'High Risk' ? '#f87171' : ($deptRisk['risk_level'] === 'Medium Risk' ? '#f59e0b' : '#34d399') }}; font-weight: 800;">
                                {{ strtoupper($deptRisk['risk_level']) }}
                            </span>
                        </div>

                        <!-- Risk Bar -->
                        <div style="width: 100%; background: #1e293b; height: 8px; border-radius: 4px; overflow: hidden; margin-bottom: 12px;">
                            <div style="width: {{ $deptRisk['risk_score'] }}%; background: {{ $deptRisk['risk_level'] === 'High Risk' ? '#ef4444' : ($deptRisk['risk_level'] === 'Medium Risk' ? '#f59e0b' : '#10b981') }}; height: 100%;"></div>
                        </div>

                        <div style="font-size: 12px; color: #cbd5e1; line-height: 1.5;">
                            💡 <strong>Rule-based Reason:</strong> {{ $deptRisk['reason'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Right: Action Plan & Direct Capacity Trigger -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 24px;">
                <h3 style="font-size: 16px; font-weight: 700; color: #f8fafc; margin: 0 0 16px 0;">
                    🛡 Recommended Mitigation Actions
                </h3>

                <div style="display: flex; flex-direction: column; gap: 12px;">
                    @foreach($riskAnalysis['mitigation_steps'] as $mStep)
                        <div style="background: #0f172a; padding: 14px; border-radius: 10px; border: 1px solid #334155; font-size: 13px; color: #cbd5e1;">
                            <div style="font-weight: 700; color: #38bdf8; margin-bottom: 4px;">{{ $mStep['title'] }}</div>
                            <div>{{ $mStep['description'] }}</div>
                            <a href="{{ route('admin.capacity-simulator') }}" style="font-size: 11px; color: #60a5fa; text-decoration: none; font-weight: 700; display: inline-block; margin-top: 8px;">Run Capacity Simulator ➔</a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

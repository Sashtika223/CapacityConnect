@extends('layouts.trainee')

@section('title', 'Disaster & Emergency Simulation Scenarios')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">
    <!-- Top Header -->
    <div style="background: #1e293b; padding: 24px; border-radius: 14px; border: 1px solid #334155; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 24px; font-weight: 700; color: #f8fafc; margin: 0 0 6px 0;">🎮 Emergency Simulation Sandbox</h1>
            <p style="font-size: 14px; color: #94a3b8; margin: 0;">Interactive branching decision-tree scenarios for real-time disaster management and crisis response.</p>
        </div>
    </div>

    <!-- Scenarios List -->
    <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 24px;">
        <h3 style="font-size: 18px; font-weight: 700; color: #f8fafc; margin: 0 0 16px 0;">
            🔥 Active Simulation Scenarios
        </h3>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px;">
            @forelse($scenarios as $scen)
                <div style="background: #0f172a; border-radius: 12px; border: 1px solid #334155; padding: 20px; display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                            <span class="badge" style="background: rgba(239,68,68,0.15); color: #f87171; font-size: 11px; text-transform: uppercase; font-weight: 700;">
                                {{ $scen->hazard_type }}
                            </span>
                            <span style="font-size: 11px; color: #94a3b8;">Max Points: {{ $scen->max_score }}</span>
                        </div>

                        <h4 style="font-size: 16px; font-weight: 700; color: #f8fafc; margin: 0 0 8px 0;">
                            {{ $scen->title }}
                        </h4>
                        <p style="font-size: 13px; color: #94a3b8; margin: 0 0 16px 0; line-height: 1.4;">
                            {{ $scen->description }}
                        </p>

                        @if($scen->targetCompetency)
                            <div style="font-size: 11px; color: #38bdf8; margin-bottom: 16px;">
                                ⚡ Evaluates: <strong>{{ $scen->targetCompetency->name }}</strong>
                            </div>
                        @endif
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 14px; border-top: 1px solid #1e293b;">
                        <span style="font-size: 12px; color: #64748b;">Difficulty: {{ ucfirst($scen->difficulty) }}</span>
                        <a href="{{ route('trainee.simulations.show', $scen->id) }}" class="btn btn-primary" style="background: linear-gradient(135deg, #2563eb, #1d4ed8); color: #fff; text-decoration: none; padding: 8px 16px; border-radius: 6px; font-size: 13px; font-weight: 700;">
                            Enter Simulation ▶
                        </a>
                    </div>
                </div>
            @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #64748b; background: #0f172a; border-radius: 10px;">
                    No simulation scenarios published currently.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Attempt History -->
    <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 24px;">
        <h3 style="font-size: 18px; font-weight: 700; color: #f8fafc; margin: 0 0 16px 0;">
            📜 Your Simulation Logs
        </h3>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
                <thead>
                    <tr style="border-bottom: 1px solid #334155; color: #94a3b8;">
                        <th style="padding: 12px;">Scenario Name</th>
                        <th style="padding: 12px;">Final Score</th>
                        <th style="padding: 12px;">Status</th>
                        <th style="padding: 12px;">Executed At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($userAttempts as $att)
                        <tr style="border-bottom: 1px solid #0f172a; color: #e2e8f0;">
                            <td style="padding: 12px; font-weight: 600;">{{ $att->scenario?->title }}</td>
                            <td style="padding: 12px; font-weight: 700; color: #38bdf8;">{{ $att->total_score }} / {{ $att->scenario?->max_score }}</td>
                            <td style="padding: 12px;">
                                <span class="badge" style="background: {{ $att->status === 'completed' ? 'rgba(16,185,129,0.2)' : 'rgba(59,130,246,0.2)' }}; color: {{ $att->status === 'completed' ? '#34d399' : '#60a5fa' }};">
                                    {{ strtoupper($att->status) }}
                                </span>
                            </td>
                            <td style="padding: 12px; color: #94a3b8;">{{ $att->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="padding: 20px; text-align: center; color: #64748b;">No simulation logs recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

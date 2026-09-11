@extends('layouts.admin')

@section('title', 'Capacity Simulator')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">
    <!-- Top Header -->
    <div style="background: #1e293b; padding: 24px; border-radius: 14px; border: 1px solid #334155; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 24px; font-weight: 700; color: #f8fafc; margin: 0 0 4px 0;">📊 Workforce Capacity Simulator</h1>
            <p style="font-size: 14px; color: #94a3b8; margin: 0;">Predictive planning tool calculating workforce training timelines, trainer load, and projected competency readiness.</p>
        </div>
    </div>

    <!-- Simulator Form & Projected Results Layout -->
    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 24px;">
        <!-- Left: Simulation Control Parameters Form -->
        <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 24px;">
            <h3 style="font-size: 18px; font-weight: 700; color: #f8fafc; margin: 0 0 16px 0;">
                ⚙️ Simulation Parameters
            </h3>

            <form action="{{ route('admin.capacity-simulator') }}" method="GET" style="display: flex; flex-direction: column; gap: 16px;">
                <div>
                    <label style="font-size: 13px; font-weight: 600; color: #cbd5e1; display: block; margin-bottom: 6px;">Target Department</label>
                    <select name="department_id" style="width: 100%; background: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 10px; color: #fff; font-size: 13px;">
                        <option value="">All Organizational Personnel</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ $params['department_id'] == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size: 13px; font-weight: 600; color: #cbd5e1; display: block; margin-bottom: 6px;">Target Competency Goal</label>
                    <select name="competency_id" style="width: 100%; background: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 10px; color: #fff; font-size: 13px;">
                        @foreach($competencies as $comp)
                            <option value="{{ $comp->id }}" {{ $params['competency_id'] == $comp->id ? 'selected' : '' }}>{{ $comp->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size: 13px; font-weight: 600; color: #cbd5e1; display: block; margin-bottom: 6px;">Target Workforce Headcount</label>
                    <input type="number" name="workforce_count" value="{{ $params['workforce_count'] }}" min="10" max="1000" style="width: 100%; background: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 10px; color: #fff; font-size: 14px;">
                </div>

                <div>
                    <label style="font-size: 13px; font-weight: 600; color: #cbd5e1; display: block; margin-bottom: 6px;">Batch Training Size</label>
                    <input type="number" name="batch_size" value="{{ $params['batch_size'] }}" min="5" max="100" style="width: 100%; background: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 10px; color: #fff; font-size: 14px;">
                </div>

                <div>
                    <label style="font-size: 13px; font-weight: 600; color: #cbd5e1; display: block; margin-bottom: 6px;">Expected Completion Rate (%)</label>
                    <input type="number" name="completion_rate" value="{{ $params['completion_rate'] }}" min="50" max="100" style="width: 100%; background: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 10px; color: #fff; font-size: 14px;">
                </div>

                <button type="submit" class="btn btn-primary" style="background: #2563eb; color: #fff; border: none; padding: 12px; border-radius: 8px; font-weight: 700; font-size: 14px; cursor: pointer; margin-top: 6px;">
                    Run Predictive Simulation 🚀
                </button>
            </form>
        </div>

        <!-- Right: Simulation Output Projection -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            <div class="card card-glass" style="background: linear-gradient(135deg, rgba(15,39,68,0.95), rgba(30,58,138,0.9)); padding: 28px; border-radius: 16px; border: 1px solid rgba(255,255,255,0.1); color: #fff;">
                <h3 style="font-size: 20px; font-weight: 800; margin: 0 0 16px 0; color: #ffffff;">
                    📈 Simulated Capacity Projection
                </h3>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px;">
                    <div style="background: rgba(0,0,0,0.3); padding: 16px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1);">
                        <div style="font-size: 11px; color: #94a3b8; text-transform: uppercase;">Estimated Timeline</div>
                        <div style="font-size: 26px; font-weight: 800; color: #38bdf8;">
                            {{ $simulationResult['estimated_weeks'] }} Weeks
                        </div>
                    </div>

                    <div style="background: rgba(0,0,0,0.3); padding: 16px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1);">
                        <div style="font-size: 11px; color: #94a3b8; text-transform: uppercase;">Projected Qualified</div>
                        <div style="font-size: 26px; font-weight: 800; color: #34d399;">
                            {{ $simulationResult['projected_ready_headcount'] }} Personnel
                        </div>
                    </div>

                    <div style="background: rgba(0,0,0,0.3); padding: 16px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1);">
                        <div style="font-size: 11px; color: #94a3b8; text-transform: uppercase;">Required Batches</div>
                        <div style="font-size: 26px; font-weight: 800; color: #f59e0b;">
                            {{ $simulationResult['total_batches'] }} Batches
                        </div>
                    </div>
                </div>

                <div style="margin-top: 24px; padding: 16px; background: rgba(0,0,0,0.25); border-radius: 10px; font-size: 13px; color: #cbd5e1; line-height: 1.5; border: 1px solid rgba(255,255,255,0.05);">
                    💡 <strong>Operational Advice:</strong> This is an estimate based on {{ $simulationResult['outputs']['trainers_available'] }} available trainer(s), {{ $simulationResult['parameters']['batch_size'] }} learners per batch, and a {{ $simulationResult['parameters']['completion_rate'] }}% completion assumption.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

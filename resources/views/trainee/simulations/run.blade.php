@extends('layouts.trainee')

@section('title', 'Simulation Runner — ' . $scenario->title)

@section('content')
<div style="max-width: 900px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px;">
    <!-- Simulation Stage Header -->
    <div style="background: linear-gradient(135deg, #0f172a, #1e293b); padding: 24px; border-radius: 14px; border: 1px solid #334155; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <span class="badge" style="background: rgba(239,68,68,0.2); color: #f87171; font-weight: 700; font-size: 11px; margin-bottom: 4px; display: inline-block;">
                🚨 {{ strtoupper($scenario->hazard_type) }} SIMULATION ACTIVE
            </span>
            <h1 style="font-size: 22px; font-weight: 800; color: #ffffff; margin: 0;">{{ $scenario->title }}</h1>
        </div>

        <div style="background: #0f172a; border: 1px solid #334155; padding: 12px 20px; border-radius: 10px; text-align: center;">
            <div style="font-size: 11px; color: #94a3b8; text-transform: uppercase;">Current Score</div>
            <div style="font-size: 24px; font-weight: 800; color: #38bdf8;">{{ $attempt->total_score }} PTS</div>
        </div>
    </div>

    <!-- Scenario Decision Card -->
    <div class="card" style="background: #1e293b; border-radius: 16px; border: 1px solid #334155; padding: 32px; box-shadow: 0 10px 25px rgba(0,0,0,0.3);">
        @if($currentNode && isset($currentNode['title']))
            <h2 style="font-size: 20px; font-weight: 700; color: #f8fafc; margin: 0 0 16px 0;">
                📍 {{ $currentNode['title'] }}
            </h2>

            <div style="background: #0f172a; padding: 20px; border-radius: 12px; border: 1px solid #334155; color: #cbd5e1; font-size: 15px; line-height: 1.6; margin-bottom: 28px;">
                {{ $currentNode['narrative'] ?? 'Assess the crisis parameters and choose your operational decision.' }}
            </div>

            @if(isset($currentNode['choices']) && count($currentNode['choices']) > 0 && $attempt->status !== 'completed')
                <h3 style="font-size: 15px; font-weight: 700; color: #f8fafc; margin: 0 0 14px 0;">
                    Choose Your Strategic Command Action:
                </h3>

                <div style="display: flex; flex-direction: column; gap: 14px;">
                    @foreach($currentNode['choices'] as $choice)
                        <form action="{{ route('trainee.simulations.decide', ['scenario' => $scenario->id, 'attempt' => $attempt->id]) }}" method="POST">
                            @csrf
                            <input type="hidden" name="choice_id" value="{{ $choice['id'] }}">
                            <button type="submit" style="width: 100%; text-align: left; background: #0f172a; border: 1px solid #3b82f6; padding: 18px 22px; border-radius: 12px; color: #f8fafc; cursor: pointer; transition: all 0.2s ease; display: flex; justify-content: space-between; align-items: center;" onmouseover="this.style.background='#1e3a8a'" onmouseout="this.style.background='#0f172a'">
                                <span style="font-size: 14px; font-weight: 600; line-height: 1.4;">{{ $choice['text'] }}</span>
                                <span style="font-size: 18px; color: #38bdf8; font-weight: 800;">➔</span>
                            </button>
                        </form>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 24px; background: rgba(16,185,129,0.1); border-radius: 12px; border: 1px solid #059669; color: #34d399;">
                    <div style="font-size: 40px; margin-bottom: 8px;">🏁</div>
                    <h3 style="font-size: 18px; font-weight: 700; margin: 0 0 8px 0;">Simulation Scenario Concluded</h3>
                    <p style="font-size: 14px; color: #cbd5e1; margin: 0 0 16px 0;">Final Score: <strong>{{ $attempt->total_score }} Points</strong></p>
                    <a href="{{ route('trainee.simulations.index') }}" class="btn btn-primary" style="background: #2563eb; color: #fff; text-decoration: none; padding: 10px 20px; border-radius: 8px; font-weight: 700; font-size: 13px;">Return to Simulations</a>
                </div>
            @endif
        @else
            <div style="text-align: center; padding: 24px; color: #94a3b8;">
                Simulation node unavailable or scenario completed.
                <a href="{{ route('trainee.simulations.index') }}" style="color: #38bdf8; margin-left: 8px;">Return Home</a>
            </div>
        @endif
    </div>
</div>
@endsection

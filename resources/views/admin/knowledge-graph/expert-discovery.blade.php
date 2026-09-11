@extends('layouts.admin')

@section('title', 'Hidden Expert Discovery')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">
    <!-- Top Header -->
    <div style="background: #1e293b; padding: 24px; border-radius: 14px; border: 1px solid #334155; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 24px; font-weight: 700; color: #f8fafc; margin: 0 0 4px 0;">🔍 Hidden Expert Discovery Query Engine</h1>
            <p style="font-size: 14px; color: #94a3b8; margin: 0;">Query personnel by verified competency levels, evaluation history, and academic qualifications with transparent score ranking.</p>
        </div>
    </div>

    <!-- Search Form -->
    <form action="{{ route('admin.expert-discovery') }}" method="GET" style="background: #1e293b; padding: 20px; border-radius: 14px; border: 1px solid #334155; display: flex; gap: 16px; align-items: center; flex-wrap: wrap;">
        <input type="text" name="query" value="{{ $query }}" placeholder="Search by skill keyword, topic, or personnel name..." style="flex: 1; min-width: 260px; background: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 12px 16px; color: #fff; font-size: 14px;">

        <select name="competency_id" style="background: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 12px 16px; color: #fff; font-size: 14px;">
            <option value="">Filter by Specific Competency...</option>
            @foreach($competencies as $comp)
                <option value="{{ $comp->id }}" {{ $competencyId == $comp->id ? 'selected' : '' }}>{{ $comp->title }}</option>
            @endforeach
        </select>

        <button type="submit" class="btn btn-primary" style="background: #2563eb; color: #fff; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 700; font-size: 14px; cursor: pointer;">
            Execute Discovery Search 🔍
        </button>
    </form>

    <!-- Ranked Experts Results -->
    <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 24px;">
        <h3 style="font-size: 18px; font-weight: 700; color: #f8fafc; margin: 0 0 16px 0;">
            🏆 Ranked Personnel Expertise Results
        </h3>

        <div style="display: flex; flex-direction: column; gap: 14px;">
            @forelse($results as $rank => $expert)
                <div style="background: #0f172a; padding: 18px; border-radius: 12px; border: 1px solid #334155; display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 16px;">
                        <div style="width: 44px; height: 44px; background: #1e293b; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: 800; color: #f59e0b; border: 2px solid #f59e0b;">
                            #{{ $rank + 1 }}
                        </div>
                        <div>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <h4 style="font-size: 16px; font-weight: 700; color: #f8fafc; margin: 0;">{{ $expert->name }}</h4>
                                <span class="badge" style="background: rgba(59,130,246,0.2); color: #60a5fa; font-size: 11px;">{{ strtoupper($expert->role) }}</span>
                            </div>
                            <div style="font-size: 12px; color: #94a3b8; margin-top: 2px;">
                                {{ $expert->email }} • {{ $expert->department?->name ?? 'Disaster Personnel' }}
                            </div>
                        </div>
                    </div>

                    <div style="text-align: right; display: flex; align-items: center; gap: 24px;">
                        <div style="font-size: 12px; color: #cbd5e1; text-align: left;">
                            <div>⚡ Verified Competencies: <strong>{{ $expert->userCompetencies->count() }}</strong></div>
                            <div>🎓 Qualifications: <strong>{{ $expert->qualifications->count() }}</strong></div>
                        </div>

                        <div style="background: #1e293b; border: 1px solid #3b82f6; padding: 10px 18px; border-radius: 10px; text-align: center;">
                            <div style="font-size: 10px; color: #94a3b8; text-transform: uppercase;">Expert Index</div>
                            <div style="font-size: 22px; font-weight: 800; color: #34d399;">{{ $expert->expert_score }} / 100</div>
                        </div>
                    </div>
                </div>
            @empty
                <div style="text-align: center; padding: 36px; color: #64748b; background: #0f172a; border-radius: 10px;">
                    No personnel match the specified query parameters.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

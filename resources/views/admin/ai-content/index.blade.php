@extends('layouts.admin')

@section('title', 'AI Content Generation Studio')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">
    <!-- Top Header -->
    <div style="background: #1e293b; padding: 24px; border-radius: 14px; border: 1px solid #334155; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 24px; font-weight: 700; color: #f8fafc; margin: 0 0 4px 0;">🤖 AI Content Studio & Editorial Governance</h1>
            <p style="font-size: 14px; color: #94a3b8; margin: 0;">Prompt-driven generation of Course syllabi, MCQs, and lesson summaries with Human-in-the-Loop approval oversight.</p>
        </div>
    </div>

    <!-- Generator & Editorial Queue Grid -->
    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 24px;">
        <!-- Left: Prompt & Generation Trigger Form -->
        <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 24px;">
            <h3 style="font-size: 18px; font-weight: 700; color: #f8fafc; margin: 0 0 16px 0;">
                ✨ Prompt Content Generator
            </h3>

            <form action="{{ route('admin.ai-content.generate') }}" method="POST" style="display: flex; flex-direction: column; gap: 14px;">
                @csrf

                <div>
                    <label style="font-size: 13px; font-weight: 600; color: #cbd5e1; display: block; margin-bottom: 6px;">Topic / Emergency Focus</label>
                    <input type="text" name="topic" placeholder="e.g. Seismic Structural Inspection" required style="width: 100%; background: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 10px; color: #fff; font-size: 13px;">
                </div>

                <div>
                    <label style="font-size: 13px; font-weight: 600; color: #cbd5e1; display: block; margin-bottom: 6px;">Generation Artifact Type</label>
                    <select name="type" required style="width: 100%; background: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 10px; color: #fff; font-size: 13px;">
                        <option value="course_description">Course Description & Objectives</option>
                        <option value="mcqs">MCQ Question Bank</option>
                        <option value="lesson_summary">Lesson Summary & Takeaways</option>
                        <option value="practice_questions">Practice Scenario Questions</option>
                    </select>
                </div>

                <div>
                    <label style="font-size: 13px; font-weight: 600; color: #cbd5e1; display: block; margin-bottom: 6px;">Target Difficulty</label>
                    <select name="difficulty" required style="width: 100%; background: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 10px; color: #fff; font-size: 13px;">
                        <option value="Beginner">Beginner</option>
                        <option value="Intermediate" selected>Intermediate</option>
                        <option value="Advanced">Advanced</option>
                        <option value="Expert">Expert</option>
                    </select>
                </div>

                <div>
                    <label style="font-size: 13px; font-weight: 600; color: #cbd5e1; display: block; margin-bottom: 6px;">Target Competency (Optional)</label>
                    <select name="competency_id" style="width: 100%; background: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 10px; color: #fff; font-size: 13px;">
                        <option value="">Select Competency...</option>
                        @foreach($competencies as $comp)
                            <option value="{{ $comp->id }}">{{ $comp->title }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #7c3aed, #2563eb); color: #fff; border: none; padding: 12px; border-radius: 8px; font-weight: 700; font-size: 14px; cursor: pointer;">
                    Generate Draft Content 🤖
                </button>
            </form>
        </div>

        <!-- Right: Editorial Governance Board -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 24px;">
                <h3 style="font-size: 18px; font-weight: 700; color: #f8fafc; margin: 0 0 16px 0;">
                    📝 Editorial Review Queue
                </h3>

                <div style="display: flex; flex-direction: column; gap: 16px;">
                    @forelse($generations as $gen)
                        <div style="background: #0f172a; padding: 18px; border-radius: 12px; border: 1px solid #334155;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                                <div>
                                    <span class="badge" style="background: rgba(124,58,237,0.2); color: #c084fc; font-size: 11px;">{{ strtoupper($gen->type) }}</span>
                                    <h4 style="font-size: 15px; font-weight: 700; color: #f8fafc; margin: 4px 0 0 0;">{{ $gen->topic }}</h4>
                                </div>
                                <span class="badge" style="background: {{ $gen->status === 'approved' || $gen->status === 'published' ? 'rgba(16,185,129,0.2)' : 'rgba(245,158,11,0.2)' }}; color: {{ $gen->status === 'approved' || $gen->status === 'published' ? '#34d399' : '#f59e0b' }};">
                                    {{ strtoupper($gen->status) }}
                                </span>
                            </div>

                            <form action="{{ route('admin.ai-content.review', $gen->id) }}" method="POST" style="display: flex; flex-direction: column; gap: 10px;">
                                @csrf
                                <textarea name="generated_content" rows="4" style="background: #1e293b; border: 1px solid #334155; border-radius: 6px; padding: 10px; color: #cbd5e1; font-size: 13px; line-height: 1.5;">{{ $gen->generated_content }}</textarea>

                                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                                    <button type="submit" name="action" value="reject" class="btn btn-secondary" style="background: rgba(239,68,68,0.2); color: #f87171; border: 1px solid #ef4444; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 700;">
                                        Reject Draft
                                    </button>
                                    <button type="submit" name="action" value="approve" class="btn btn-primary" style="background: #10b981; color: #fff; border: none; padding: 6px 16px; border-radius: 6px; font-size: 12px; font-weight: 700;">
                                        Approve Content ✓
                                    </button>
                                </div>
                            </form>
                        </div>
                    @empty
                        <p style="font-size: 12px; color: #64748b; margin: 0;">No AI generations recorded.</p>
                    @endforelse
                </div>
                <div style="margin-top: 12px;">
                    {{ $generations->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

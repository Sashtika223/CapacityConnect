@extends('layouts.trainer')

@section('title', 'Author New Course')

@section('content')
<div style="max-width: 900px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px;">
    <!-- Top Header -->
    <div style="background: #1e293b; padding: 24px; border-radius: 14px; border: 1px solid #334155;">
        <h1 style="font-size: 24px; font-weight: 700; color: #f8fafc; margin: 0 0 4px 0;">Author New Capacity Course</h1>
        <p style="font-size: 14px; color: #94a3b8; margin: 0;">Define core course parameters, target emergency competencies, and learning objectives.</p>
    </div>

    <!-- Create Form -->
    <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 28px;">
        <form action="{{ route('trainer.courses.store') }}" method="POST" style="display: flex; flex-direction: column; gap: 20px;">
            @csrf

            <div>
                <label style="font-size: 14px; font-weight: 600; color: #cbd5e1; display: block; margin-bottom: 6px;">Course Title</label>
                <input type="text" name="title" value="{{ old('title') }}" placeholder="e.g. Advanced Flood Rescue & Shelter Logistics" required style="width: 100%; background: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 12px 16px; color: #fff; font-size: 15px;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
                <div>
                    <label style="font-size: 13px; font-weight: 600; color: #cbd5e1; display: block; margin-bottom: 6px;">Target Department</label>
                    <select name="department_id" style="width: 100%; background: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 10px 14px; color: #fff; font-size: 14px;">
                        <option value="">Select Department...</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size: 13px; font-weight: 600; color: #cbd5e1; display: block; margin-bottom: 6px;">Category</label>
                    <input type="text" name="category" value="{{ old('category', 'Disaster Response') }}" required style="width: 100%; background: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 10px 14px; color: #fff; font-size: 14px;">
                </div>

                <div>
                    <label style="font-size: 13px; font-weight: 600; color: #cbd5e1; display: block; margin-bottom: 6px;">Difficulty Level</label>
                    <select name="difficulty" required style="width: 100%; background: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 10px 14px; color: #fff; font-size: 14px;">
                        <option value="Beginner">Beginner</option>
                        <option value="Intermediate" selected>Intermediate</option>
                        <option value="Advanced">Advanced</option>
                        <option value="Expert">Expert</option>
                    </select>
                </div>
            </div>

            <div>
                <label style="font-size: 13px; font-weight: 600; color: #cbd5e1; display: block; margin-bottom: 6px;">Duration (Estimated Hours)</label>
                <input type="number" name="duration_hours" value="{{ old('duration_hours', 12) }}" required style="width: 180px; background: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 10px 14px; color: #fff; font-size: 14px;">
            </div>

            <div>
                <label style="font-size: 13px; font-weight: 600; color: #cbd5e1; display: block; margin-bottom: 6px;">Short Overview (Catalog summary)</label>
                <textarea name="short_description" rows="2" required style="width: 100%; background: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 10px 14px; color: #fff; font-size: 14px;">{{ old('short_description') }}</textarea>
            </div>

            <div>
                <label style="font-size: 13px; font-weight: 600; color: #cbd5e1; display: block; margin-bottom: 6px;">Full Syllabus Description</label>
                <textarea name="full_description" rows="5" required style="width: 100%; background: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 10px 14px; color: #fff; font-size: 14px;">{{ old('full_description') }}</textarea>
            </div>

            <div>
                <label style="font-size: 13px; font-weight: 600; color: #cbd5e1; display: block; margin-bottom: 6px;">Learning Objectives (One per line)</label>
                <textarea name="learning_objectives" rows="3" placeholder="Understand Incident Command System (ICS)&#10;Execute rapid damage assessment&#10;Deploy emergency communication protocols" style="width: 100%; background: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 10px 14px; color: #fff; font-size: 14px;">{{ old('learning_objectives') }}</textarea>
            </div>

            <!-- Target Competencies Checkbox List -->
            <div>
                <label style="font-size: 14px; font-weight: 700; color: #f8fafc; display: block; margin-bottom: 10px;">⚡ Evaluated Competencies Covered</label>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 10px; background: #0f172a; padding: 16px; border-radius: 10px; border: 1px solid #334155;">
                    @foreach($competencies as $comp)
                        <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #cbd5e1; cursor: pointer;">
                            <input type="checkbox" name="competency_ids[]" value="{{ $comp->id }}" style="accent-color: #2563eb;">
                            <span>{{ $comp->title }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 10px;">
                <a href="{{ route('trainer.courses.index') }}" style="color: #94a3b8; text-decoration: none; padding: 12px 20px; font-size: 14px; font-weight: 600;">Cancel</a>
                <button type="submit" class="btn btn-primary" style="background: #2563eb; color: #fff; border: none; padding: 12px 28px; border-radius: 8px; font-weight: 700; font-size: 14px; cursor: pointer;">
                    Create Course & Open Curriculum Builder ➔
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

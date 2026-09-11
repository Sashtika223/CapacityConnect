@extends('layouts.public')

@section('title', 'Course Catalog — Capacity Connect')

@section('content')
<div class="app-container" style="padding-top: 40px; padding-bottom: 60px;">
    <!-- Catalog Header -->
    <div style="margin-bottom: 32px;">
        <span class="badge badge-blue" style="margin-bottom: 8px;">Accredited Programs</span>
        <h1 style="font-size: 32px; font-weight: 800; color: var(--cc-primary-dark); margin-bottom: 8px;">State Certified Course Catalog</h1>
        <p style="color: var(--cc-text-muted); font-size: 15px;">Search and enroll in certified disaster preparedness, early warning, tactical rescue, and health logistics programs.</p>
    </div>

    <!-- Filters & Search Bar -->
    <div class="card" style="padding: 20px; margin-bottom: 32px; background: #ffffff;">
        <form action="{{ route('courses.index') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)) 120px; gap: 16px; align-items: flex-end;">
            <div>
                <label class="form-label">Search Keywords</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="e.g. Cyclone, Flood, Command...">
            </div>

            <div>
                <label class="form-label">Department</label>
                <select name="department" class="form-select">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">Difficulty Level</label>
                <select name="difficulty" class="form-select">
                    <option value="">All Levels</option>
                    <option value="Beginner" {{ request('difficulty') === 'Beginner' ? 'selected' : '' }}>Beginner</option>
                    <option value="Intermediate" {{ request('difficulty') === 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
                    <option value="Advanced" {{ request('difficulty') === 'Advanced' ? 'selected' : '' }}>Advanced</option>
                    <option value="Expert" {{ request('difficulty') === 'Expert' ? 'selected' : '' }}>Expert</option>
                </select>
            </div>

            <div>
                <label class="form-label">Target Competency</label>
                <select name="competency" class="form-select">
                    <option value="">All Competencies</option>
                    @foreach($competencies as $comp)
                        <option value="{{ $comp->id }}" {{ request('competency') == $comp->id ? 'selected' : '' }}>{{ $comp->title }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Filter</button>
            </div>
        </form>
    </div>

    <!-- Course Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px; margin-bottom: 40px;">
        @forelse($courses as $course)
            <div class="card" style="display: flex; flex-direction: column; margin-bottom: 0;">
                <div style="height: 190px; overflow: hidden; background: #1e293b; position: relative;">
                    <img src="{{ $course->thumbnail ?? 'https://images.unsplash.com/photo-1527482797697-8795b05a13fe?auto=format&fit=crop&w=800&q=80' }}" alt="{{ $course->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                    <div style="position: absolute; top: 12px; right: 12px; display: flex; gap: 6px;">
                        <span class="badge badge-blue">{{ $course->difficulty }}</span>
                        <span class="badge badge-slate">{{ $course->duration_hours }} hrs</span>
                    </div>
                </div>
                <div style="padding: 24px; flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <p style="font-size: 11px; text-transform: uppercase; color: var(--cc-accent); font-weight: 700; margin-bottom: 6px;">{{ $course->department?->name ?? $course->category }}</p>
                        <h3 style="font-size: 17px; font-weight: 700; margin-bottom: 8px; line-height: 1.4;">
                            <a href="{{ route('courses.show', $course->slug) }}" style="color: var(--cc-primary-dark);">{{ $course->title }}</a>
                        </h3>
                        <p style="font-size: 13px; color: var(--cc-text-muted); line-height: 1.5; margin-bottom: 16px;">{{ Str::limit($course->short_description, 130) }}</p>

                        <!-- Competencies Tags -->
                        <div style="display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 16px;">
                            @foreach($course->competencies->take(2) as $c)
                                <span class="badge badge-emerald">{{ $c->title }}</span>
                            @endforeach
                        </div>
                    </div>

                    <div style="border-top: 1px solid var(--cc-border); padding-top: 16px; display: flex; justify-content: space-between; align-items: center;">
                        <div style="font-size: 12px; color: var(--cc-text-muted);">
                            Lead Trainer:<br>
                            <strong style="color: var(--cc-text-main);">{{ $course->trainer->name }}</strong>
                        </div>
                        <a href="{{ route('courses.show', $course->slug) }}" class="btn btn-primary btn-sm">View Curriculum</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="card" style="grid-column: 1 / -1; padding: 60px 24px; text-align: center;">
                <div style="font-size: 40px; margin-bottom: 12px;">🔍</div>
                <h3 style="font-size: 18px; margin-bottom: 8px;">No courses matching your filter criteria</h3>
                <p style="color: var(--cc-text-muted); font-size: 14px; margin-bottom: 20px;">Try adjusting your keyword query, category, or selected department.</p>
                <a href="{{ route('courses.index') }}" class="btn btn-secondary">Reset Filters</a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div>
        {{ $courses->links() }}
    </div>
</div>
@endsection

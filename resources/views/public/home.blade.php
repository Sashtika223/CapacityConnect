@extends('layouts.public')

@section('title', 'Capacity Connect — Digital Capacity Building & Competency Platform')

@section('content')
<!-- Hero Section -->
<section class="public-hero">
    <div class="hero-content">
        <div>
            <div class="hero-badge">
                <span style="display: inline-block; width: 8px; height: 8px; background: #60a5fa; border-radius: 50%;"></span>
                Institutional Capacity Building & Resilience
            </div>
            <h1 class="hero-title">Empowering Resilient <span>Workforce Readiness</span> & Competency.</h1>
            <p class="hero-subtitle">Unified digital learning platform connecting emergency response personnel, certified trainers, and state administrators with data-driven competency tracking, interactive disaster simulations, and cryptographic verification.</p>
            <div class="hero-actions">
                <a href="{{ route('courses.index') }}" class="btn btn-primary btn-lg">Explore Certified Courses</a>
                <a href="{{ route('certificate.verify') }}" class="btn btn-secondary btn-lg" style="background: rgba(255,255,255,0.1); color: #fff; border-color: rgba(255,255,255,0.25);">Verify Certificate</a>
            </div>
        </div>

        <div style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.15); border-radius: 16px; padding: 24px; backdrop-filter: blur(10px);">
            <h3 style="color: #fff; font-size: 16px; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                <span style="color: #60a5fa;">●</span> Real-Time State Capacity Metrics
            </h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div style="background: rgba(15, 23, 42, 0.6); padding: 16px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.08);">
                    <p style="font-size: 11px; text-transform: uppercase; color: #94a3b8;">Active Trainees</p>
                    <p style="font-size: 28px; font-weight: 800; color: #38bdf8; margin-top: 4px;">{{ $stats['trainees_count'] }}</p>
                </div>
                <div style="background: rgba(15, 23, 42, 0.6); padding: 16px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.08);">
                    <p style="font-size: 11px; text-transform: uppercase; color: #94a3b8;">Certified Instructors</p>
                    <p style="font-size: 28px; font-weight: 800; color: #34d399; margin-top: 4px;">{{ $stats['trainers_count'] }}</p>
                </div>
                <div style="background: rgba(15, 23, 42, 0.6); padding: 16px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.08);">
                    <p style="font-size: 11px; text-transform: uppercase; color: #94a3b8;">Approved Modules</p>
                    <p style="font-size: 28px; font-weight: 800; color: #fbbf24; margin-top: 4px;">{{ $stats['courses_count'] }}</p>
                </div>
                <div style="background: rgba(15, 23, 42, 0.6); padding: 16px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.08);">
                    <p style="font-size: 11px; text-transform: uppercase; color: #94a3b8;">Valid Certifications</p>
                    <p style="font-size: 28px; font-weight: 800; color: #a78bfa; margin-top: 4px;">{{ $stats['certificates_issued'] }}</p>
                </div>
            </div>

            <div style="margin-top: 20px; padding: 12px; background: rgba(37, 99, 235, 0.15); border-radius: 8px; font-size: 12px; color: #bfdbfe;">
                ⚡ <strong>Emergency Alert:</strong> Pre-monsoon Swiftwater Navigation and Cyclone Shelter certifications are active.
            </div>
        </div>
    </div>
</section>

<!-- Core Pillars -->
<section style="padding: 70px 24px; max-width: 1280px; margin: 0 auto;">
    <div style="text-align: center; max-width: 720px; margin: 0 auto 50px;">
        <span class="badge badge-blue" style="margin-bottom: 12px;">Unified Architecture</span>
        <h2 style="font-size: 32px; margin-bottom: 12px;">Engineered for Emergency Operations</h2>
        <p style="color: var(--cc-text-muted); font-size: 15px;">Standardized learning frameworks combining server-side assessment engines, branching disaster simulations, and continuous risk monitoring.</p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
        <div class="card" style="padding: 24px;">
            <div class="stat-icon blue" style="margin-bottom: 16px;">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 style="font-size: 18px; margin-bottom: 8px;">Competency Digital Twin</h3>
            <p style="font-size: 14px; color: var(--cc-text-muted); line-height: 1.6;">Continuous synthesis of verified skills, assessment results, field drill evaluations, and supervisor ratings mapped across 5 proficiency tiers.</p>
        </div>

        <div class="card" style="padding: 24px;">
            <div class="stat-icon emerald" style="margin-bottom: 16px;">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 style="font-size: 18px; margin-bottom: 8px;">Disaster Decision Trees</h3>
            <p style="font-size: 14px; color: var(--cc-text-muted); line-height: 1.6;">Real-time interactive decision simulations for super cyclones, night flash floods, and critical hospital power contingencies with live consequences.</p>
        </div>

        <div class="card" style="padding: 24px;">
            <div class="stat-icon amber" style="margin-bottom: 16px;">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 style="font-size: 18px; margin-bottom: 8px;">Competency Risk Radar</h3>
            <p style="font-size: 14px; color: var(--cc-text-muted); line-height: 1.6;">Transparent rule-based detection of organizational deficits, expiring certificates, and training gaps across municipal emergency departments.</p>
        </div>

        <div class="card" style="padding: 24px;">
            <div class="stat-icon purple" style="margin-bottom: 16px;">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <h3 style="font-size: 18px; margin-bottom: 8px;">Verifiable Certificates</h3>
            <p style="font-size: 14px; color: var(--cc-text-muted); line-height: 1.6;">Cryptographically sealed certificates with public SHA-256 verification endpoints to ensure credentials cannot be forged.</p>
        </div>
    </div>
</section>

<!-- Featured Courses -->
<section style="background: var(--cc-bg-subtle); padding: 70px 24px; border-top: 1px solid var(--cc-border); border-bottom: 1px solid var(--cc-border);">
    <div style="max-width: 1280px; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 36px; flex-wrap: wrap; gap: 16px;">
            <div>
                <span class="badge badge-emerald" style="margin-bottom: 8px;">Official Curriculums</span>
                <h2 style="font-size: 28px;">Featured Certified Courses</h2>
                <p style="color: var(--cc-text-muted); font-size: 14px; margin-top: 4px;">Rigorous competency-backed training modules authorized by state directors.</p>
            </div>
            <a href="{{ route('courses.index') }}" class="btn btn-secondary">View All Courses →</a>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px;">
            @forelse($featuredCourses as $course)
                <div class="card" style="display: flex; flex-direction: column; margin-bottom: 0;">
                    <div style="height: 180px; overflow: hidden; background: #1e293b; position: relative;">
                        <img src="{{ $course->thumbnail ?? 'https://images.unsplash.com/photo-1527482797697-8795b05a13fe?auto=format&fit=crop&w=800&q=80' }}" alt="{{ $course->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                        <div style="position: absolute; top: 12px; right: 12px; display: flex; gap: 6px;">
                            <span class="badge badge-blue">{{ $course->difficulty }}</span>
                            <span class="badge badge-slate">{{ $course->duration_hours }}h</span>
                        </div>
                    </div>
                    <div style="padding: 20px; flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <p style="font-size: 11px; text-transform: uppercase; color: var(--cc-accent); font-weight: 700; margin-bottom: 6px;">{{ $course->category }}</p>
                            <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 8px; line-height: 1.4;">
                                <a href="{{ route('courses.show', $course->slug) }}" style="color: var(--cc-primary-dark);">{{ $course->title }}</a>
                            </h3>
                            <p style="font-size: 13px; color: var(--cc-text-muted); line-height: 1.5; margin-bottom: 16px;">{{ Str::limit($course->short_description, 110) }}</p>
                        </div>
                        
                        <div style="border-top: 1px solid var(--cc-border); padding-top: 14px; display: flex; justify-content: space-between; align-items: center;">
                            <div style="font-size: 12px; color: var(--cc-text-muted);">
                                Instructor: <strong>{{ $course->trainer->name }}</strong>
                            </div>
                            <a href="{{ route('courses.show', $course->slug) }}" class="btn btn-primary btn-sm">Course Details</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card" style="grid-column: 1 / -1; padding: 40px; text-align: center;">
                    <p style="color: var(--cc-text-muted);">No published courses available at the moment.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Call to Action Banner -->
<section style="padding: 80px 24px; max-width: 1280px; margin: 0 auto; text-align: center;">
    <div style="background: linear-gradient(135deg, #0f2744 0%, #1e3a8a 100%); color: #ffffff; padding: 60px 32px; border-radius: 20px; position: relative; overflow: hidden;">
        <h2 style="color: #ffffff; font-size: 32px; margin-bottom: 16px;">Ready to Elevate Organizational Capacity?</h2>
        <p style="color: #cbd5e1; font-size: 16px; max-width: 650px; margin: 0 auto 32px; line-height: 1.6;">Register emergency officers, track verified competencies across departments, and ensure full operational readiness before state emergencies occur.</p>
        <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
            <a href="{{ route('register') }}" class="btn btn-primary btn-lg" style="background: #ffffff; color: var(--cc-primary-dark);">Register as Trainee</a>
            <a href="{{ route('login') }}" class="btn btn-secondary btn-lg" style="background: transparent; color: #ffffff; border-color: rgba(255,255,255,0.4);">Access Staff Portal</a>
        </div>
    </div>
</section>
@endsection

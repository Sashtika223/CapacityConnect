@extends('layouts.trainee')

@section('title', 'My Certificates & Verified Capacity Credentials')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">
    <!-- Top Header -->
    <div style="background: #1e293b; padding: 24px; border-radius: 14px; border: 1px solid #334155; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 24px; font-weight: 700; color: #f8fafc; margin: 0 0 6px 0;">🏆 Cryptographic Capacity Certificates</h1>
            <p style="font-size: 14px; color: #94a3b8; margin: 0;">Verified emergency capacity qualifications backed by digital signatures and public hash verification.</p>
        </div>
    </div>

    <!-- Certificates Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px;">
        @forelse($certificates as $cert)
            <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; overflow: hidden; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                <div style="background: linear-gradient(135deg, #0f2744, #1e3a8a); padding: 20px; border-bottom: 1px solid #334155;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <span class="badge" style="background: rgba(16,185,129,0.2); color: #34d399; font-weight: 700; font-size: 11px;">
                            ✓ {{ strtoupper($cert->status) }}
                        </span>
                        <span style="font-size: 11px; color: #94a3b8; font-family: monospace;">CODE: {{ $cert->certificate_code }}</span>
                    </div>

                    <h3 style="font-size: 17px; font-weight: 700; color: #ffffff; margin: 0 0 4px 0; line-height: 1.3;">
                        {{ $cert->course?->title }}
                    </h3>
                    <div style="font-size: 12px; color: #cbd5e1;">Issued to: {{ Auth::user()->name }}</div>
                </div>

                <div style="padding: 20px; font-size: 12px; color: #94a3b8; display: flex; flex-direction: column; gap: 8px;">
                    <div>📅 Issue Date: <strong style="color: #cbd5e1;">{{ $cert->issue_date ? $cert->issue_date->format('F d, Y') : 'Active' }}</strong></div>
                    <div>⏳ Expiry Date: <strong style="color: #cbd5e1;">{{ $cert->expiry_date ? $cert->expiry_date->format('F d, Y') : 'Lifetime Validity' }}</strong></div>
                    <div style="font-family: monospace; font-size: 10px; color: #64748b; word-break: break-all; margin-top: 4px;">
                        HASH: {{ substr($cert->verification_hash ?? md5($cert->certificate_code), 0, 32) }}...
                    </div>
                </div>

                <div style="padding: 16px; background: #0f172a; border-top: 1px solid #334155; display: flex; justify-content: space-between; align-items: center;">
                    <a href="{{ route('certificate.verify', $cert->certificate_code) }}" target="_blank" style="font-size: 12px; color: #38bdf8; text-decoration: none; font-weight: 600;">
                        Public Verification URL 🔗
                    </a>
                    <a href="{{ route('trainee.certificates.show', $cert->id) }}" class="btn btn-primary" style="background: #2563eb; color: #fff; text-decoration: none; padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                        View Certificate 📜
                    </a>
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 50px 20px; background: #1e293b; border-radius: 14px; border: 1px dashed #334155; color: #94a3b8;">
                <div style="font-size: 40px; margin-bottom: 8px;">🏆</div>
                <h3 style="font-size: 18px; color: #f8fafc; margin: 0 0 8px 0;">No Certificates Earned Yet</h3>
                <p style="font-size: 13px; max-width: 450px; margin: 0 auto 16px auto;">Complete 100% of your course curriculum lessons and pass required MCQ assessments to generate your cryptographic certificates.</p>
                <a href="{{ route('trainee.learning.index') }}" class="btn btn-primary" style="background: #2563eb; color: #fff; text-decoration: none; padding: 8px 16px; border-radius: 6px; font-size: 13px; font-weight: 600;">Go to My Courses</a>
            </div>
        @endforelse
    </div>
</div>
@endsection

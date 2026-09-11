@extends('layouts.public')

@section('title', 'Cryptographic Certificate Verification — Capacity Connect')

@section('content')
<div class="app-container" style="max-width: 960px; padding-top: 40px; padding-bottom: 70px;">
    <!-- Page Header -->
    <div style="text-align: center; margin-bottom: 32px;">
        <span class="badge badge-emerald" style="margin-bottom: 8px; display: inline-block;">
            🛡️ Official Verification & Credential Governance Portal
        </span>
        <h1 style="font-size: 30px; font-weight: 800; color: var(--cc-primary-dark); margin-bottom: 8px;">
            Cryptographic Certificate Verification
        </h1>
        <p style="color: var(--cc-text-muted); font-size: 15px; max-width: 650px; margin: 0 auto;">
            Verify the authenticity and validity of State Disaster Management qualifications. Trainee credentials carry permanent lifetime validity, while Trainer certifications require active validation and recertification.
        </p>
    </div>

    <!-- Verification Search Card -->
    <div class="card" style="padding: 28px; margin-bottom: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.06);">
        <form action="{{ route('certificate.verify') }}" method="GET" style="display: flex; gap: 12px; flex-wrap: wrap;">
            <input type="text" name="code" value="{{ $verificationCode }}" class="form-control" style="flex: 1; min-width: 280px; padding: 12px 16px; font-size: 15px;" placeholder="Enter Certificate Code (e.g. CC-CERT-CYCLONE-2026-001 or SHA-256 Hash)" required>
            <button type="submit" class="btn btn-primary" style="padding: 12px 28px; font-weight: 700;">Verify Authenticity ➔</button>
        </form>
    </div>

    <!-- SAMPLE CERTIFICATE CODES FOR QUICK TESTING -->
    <div class="card" style="padding: 22px 26px; margin-bottom: 32px; background: #0f172a; border: 1px solid #334155; border-radius: 14px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 8px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span style="font-size: 18px;">💡</span>
                <h4 style="font-size: 14px; font-weight: 700; color: #f8fafc; margin: 0;">
                    Sample Certificate Codes for Instant Verification Testing:
                </h4>
            </div>
            <span style="font-size: 11px; color: #94a3b8;">Click any chip below to test verification</span>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 12px;">
            <!-- Valid Trainee 1 -->
            <a href="{{ route('certificate.verify', ['code' => 'CC-CERT-CYCLONE-2026-001']) }}" style="text-decoration: none; display: block; background: #1e293b; padding: 12px 16px; border-radius: 8px; border: 1px solid rgba(16,185,129,0.3); transition: all 0.2s;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                    <span style="font-family: var(--font-mono); font-size: 12px; font-weight: 700; color: #34d399;">CC-CERT-CYCLONE-2026-001</span>
                    <span style="background: rgba(16,185,129,0.2); color: #34d399; font-size: 10px; font-weight: 800; padding: 2px 6px; border-radius: 4px;">TRAINEE (LIFETIME VALID)</span>
                </div>
                <div style="font-size: 11px; color: #cbd5e1;">Coastal Cyclone Disaster Mitigation • Verified Authentic</div>
            </a>

            <!-- Valid Trainee 2 -->
            <a href="{{ route('certificate.verify', ['code' => 'CC-CERT-SWIFTWATER-2026-042']) }}" style="text-decoration: none; display: block; background: #1e293b; padding: 12px 16px; border-radius: 8px; border: 1px solid rgba(16,185,129,0.3); transition: all 0.2s;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                    <span style="font-family: var(--font-mono); font-size: 12px; font-weight: 700; color: #34d399;">CC-CERT-SWIFTWATER-2026-042</span>
                    <span style="background: rgba(16,185,129,0.2); color: #34d399; font-size: 10px; font-weight: 800; padding: 2px 6px; border-radius: 4px;">TRAINEE (LIFETIME VALID)</span>
                </div>
                <div style="font-size: 11px; color: #cbd5e1;">Swiftwater Rescue & Urban Flood • Verified Authentic</div>
            </a>

            <!-- Valid Trainer Master -->
            <a href="{{ route('certificate.verify', ['code' => 'CC-TRN-MASTER-2026-101']) }}" style="text-decoration: none; display: block; background: #1e293b; padding: 12px 16px; border-radius: 8px; border: 1px solid rgba(59,130,246,0.3); transition: all 0.2s;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                    <span style="font-family: var(--font-mono); font-size: 12px; font-weight: 700; color: #60a5fa;">CC-TRN-MASTER-2026-101</span>
                    <span style="background: rgba(59,130,246,0.2); color: #60a5fa; font-size: 10px; font-weight: 800; padding: 2px 6px; border-radius: 4px;">TRAINER (ACTIVE)</span>
                </div>
                <div style="font-size: 11px; color: #cbd5e1;">Lead Meteorological Trainer • Authorized to Instruct</div>
            </a>

            <!-- Expired Trainer (Needs MCQ renewal) -->
            <a href="{{ route('certificate.verify', ['code' => 'CC-TRN-EXPIRED-2025-088']) }}" style="text-decoration: none; display: block; background: #1e293b; padding: 12px 16px; border-radius: 8px; border: 1px solid rgba(239,68,68,0.4); transition: all 0.2s;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                    <span style="font-family: var(--font-mono); font-size: 12px; font-weight: 700; color: #f87171;">CC-TRN-EXPIRED-2025-088</span>
                    <span style="background: rgba(239,68,68,0.2); color: #f87171; font-size: 10px; font-weight: 800; padding: 2px 6px; border-radius: 4px;">TRAINER (EXPIRED)</span>
                </div>
                <div style="font-size: 11px; color: #fca5a5;">Beyond validation date • Requires MCQ Recertification ➔</div>
            </a>
        </div>
    </div>

    @if($searched)
        @if($certificate)
            @php
                $isTrainer = $certificate->isTrainerCertificate();
                $isValid = $certificate->isValid();
                $isExpired = $certificate->isExpired();
            @endphp

            <div class="card" style="border: 2px solid {{ $isValid ? 'var(--cc-emerald)' : 'var(--cc-rose)' }}; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.08);">
                <!-- Header Status Bar -->
                <div style="background: {{ $isValid ? '#ecfdf5' : '#fff1f2' }}; padding: 20px 24px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--cc-border); flex-wrap: wrap; gap: 12px;">
                    <div style="display: flex; align-items: center; gap: 14px;">
                        <span style="font-size: 32px;">{{ $isValid ? ($isTrainer ? '🎓' : '🛡️') : '⛔' }}</span>
                        <div>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <h3 style="font-size: 18px; font-weight: 800; color: {{ $isValid ? '#065f46' : '#9f1239' }}; margin: 0;">
                                    @if($isTrainer)
                                        {{ $isValid ? 'Authorized Master Trainer Certification' : 'Trainer Certification Expired — Instructional Authorization Suspended' }}
                                    @else
                                        Verified Official Trainee Credential (Permanent Validity)
                                    @endif
                                </h3>
                                <span class="badge" style="background: {{ $isTrainer ? '#3b82f6' : '#10b981' }}; color: #fff; font-size: 10px; font-weight: 700;">
                                    {{ $isTrainer ? 'INSTRUCTOR CREDENTIAL' : 'TRAINEE QUALIFICATION' }}
                                </span>
                            </div>
                            <p style="font-size: 12px; color: var(--cc-text-muted); margin: 3px 0 0 0;">
                                Certificate Number: <strong style="color: var(--cc-text-main);">{{ $certificate->certificate_number }}</strong>
                            </p>
                        </div>
                    </div>

                    <span class="badge {{ $isValid ? 'badge-emerald' : 'badge-rose' }}" style="font-size: 13px; font-weight: 800; padding: 6px 14px;">
                        @if($isTrainer)
                            {{ $isValid ? 'ACTIVE & VALID' : 'EXPIRED (RECERTIFICATION REQUIRED)' }}
                        @else
                            AUTHENTIC & VALID (LIFETIME)
                        @endif
                    </span>
                </div>

                <div class="card-body" style="padding: 28px;">
                    <!-- TRAINER EXPIRED BANNER & RECERTIFICATION PROMPT -->
                    @if($isTrainer && $isExpired)
                        <div style="background: #4c0519; border: 1px solid #f43f5e; border-radius: 12px; padding: 20px 24px; margin-bottom: 24px; color: #ffe4e6;">
                            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                                <div>
                                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                                        <span style="font-size: 20px;">⚠️</span>
                                        <h4 style="font-size: 16px; font-weight: 800; color: #ffffff; margin: 0;">
                                            Trainer Validation Date Lapsed — Assessment & Renewal Required
                                        </h4>
                                    </div>
                                    <p style="font-size: 13px; color: #fecdd3; margin: 0; max-width: 650px; line-height: 1.5;">
                                        The validation date for this instructor certificate (<strong>{{ $certificate->expiry_date?->format('d M Y') }}</strong>) has lapsed. Under State Disaster Management regulations, the trainer <strong>cannot conduct training sessions or use this certification</strong> until completing the 2026 real-time SOP updates and passing the mandatory MCQ assessment.
                                    </p>
                                </div>

                                <a href="{{ route('trainer.recertify', ['certificate_number' => $certificate->certificate_number]) }}" class="btn" style="background: #f43f5e; color: #ffffff; font-weight: 800; padding: 12px 22px; border-radius: 8px; text-decoration: none; font-size: 14px; box-shadow: 0 4px 15px rgba(244,63,94,0.4); display: inline-flex; align-items: center; gap: 6px;">
                                    📝 Attend Recertification Exam ➔
                                </a>
                            </div>
                        </div>
                    @elseif(!$isTrainer)
                        <!-- TRAINEE LIFETIME VALIDITY INFO -->
                        <div style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 12px; padding: 16px 20px; margin-bottom: 24px; color: #065f46;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span style="font-size: 22px;">✅</span>
                                <div>
                                    <h4 style="font-size: 14px; font-weight: 700; margin: 0 0 2px 0; color: #064e3b;">
                                        Verified Permanent Trainee Credential
                                    </h4>
                                    <p style="font-size: 12px; margin: 0; color: #047857;">
                                        Trainee emergency qualifications have <strong>permanent lifetime validity</strong> and do not expire. This credential is fully verified on the state disaster capacity registry based on the unique certificate code.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Certificate Metadata Grid -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 24px;">
                        <div style="background: var(--cc-bg-subtle); padding: 16px; border-radius: 8px;">
                            <p style="font-size: 11px; text-transform: uppercase; color: var(--cc-text-muted); font-weight: 700;">
                                {{ $isTrainer ? 'Accredited Instructor' : 'Certified Trainee' }}
                            </p>
                            <p style="font-size: 16px; font-weight: 800; color: var(--cc-text-main); margin-top: 4px;">Credential holder verified</p>
                            <p style="font-size: 12px; color: var(--cc-text-muted);">Personal details are withheld for privacy.</p>
                        </div>

                        <div style="background: var(--cc-bg-subtle); padding: 16px; border-radius: 8px;">
                            <p style="font-size: 11px; text-transform: uppercase; color: var(--cc-text-muted); font-weight: 700;">Accredited Course / Domain</p>
                            <p style="font-size: 16px; font-weight: 800; color: var(--cc-text-main); margin-top: 4px;">{{ $certificate->course->title }}</p>
                            <p style="font-size: 12px; color: var(--cc-text-muted);">Authoring Authority: {{ $certificate->course->trainer->name }}</p>
                        </div>

                        <div style="background: var(--cc-bg-subtle); padding: 16px; border-radius: 8px;">
                            <p style="font-size: 11px; text-transform: uppercase; color: var(--cc-text-muted); font-weight: 700;">Issuance & Validity Window</p>
                            <p style="font-size: 13px; font-weight: 600; color: var(--cc-text-main); margin-top: 4px;">
                                Issued: <strong>{{ $certificate->issue_date->format('d M Y') }}</strong>
                            </p>
                            <p style="font-size: 13px; font-weight: 700; color: {{ $isValid ? '#059669' : '#dc2626' }}; margin-top: 2px;">
                                Validity:
                                @if($isTrainer)
                                    Expires {{ $certificate->expiry_date ? $certificate->expiry_date->format('d M Y') : 'N/A' }}
                                    @if($isExpired)
                                        <span style="font-size: 11px; color: #dc2626;">(EXPIRED)</span>
                                    @else
                                        <span style="font-size: 11px; color: #059669;">(ACTIVE)</span>
                                    @endif
                                @else
                                    <span style="color: #059669;">Permanent / Lifetime (No Expiry)</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Cryptographic Hash -->
                    <div style="background: #0f172a; padding: 16px; border-radius: 8px; font-family: var(--font-mono); font-size: 11px; color: #94a3b8; word-break: break-all; border: 1px solid #334155;">
                        <span style="color: #38bdf8; font-weight: 700;">SHA-256 Tamper-Evident Hash:</span> {{ $certificate->verification_code }}
                    </div>
                </div>
            </div>
        @else
            <div class="card" style="padding: 40px; text-align: center; border: 1px dashed var(--cc-rose); border-radius: 14px;">
                <div style="font-size: 40px; margin-bottom: 12px;">❌</div>
                <h3 style="font-size: 18px; font-weight: 700; color: var(--cc-rose); margin-bottom: 6px;">Certificate Record Not Found</h3>
                <p style="font-size: 14px; color: var(--cc-text-muted); max-width: 500px; margin: 0 auto 16px auto;">
                    The certificate code or hash "<strong>{{ $verificationCode }}</strong>" does not match any valid record in the database.
                </p>
                <p style="font-size: 12px; color: var(--cc-text-muted);">
                    Please try one of the sample codes listed in the test box above.
                </p>
            </div>
        @endif
    @endif
</div>
@endsection

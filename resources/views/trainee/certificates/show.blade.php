@extends('layouts.trainee')

@section('title', 'Certificate — ' . $certificate->certificate_number)

@section('content')
<div style="max-width: 900px; margin: 0 auto; display: flex; flex-direction: column; gap: 20px;">
    <!-- Print / Actions Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; background: #1e293b; padding: 16px 24px; border-radius: 12px; border: 1px solid #334155;">
        <a href="{{ route('trainee.certificates.index') }}" style="color: #94a3b8; text-decoration: none; font-size: 13px; font-weight: 600;">
            ← Back to All Certificates
        </a>

        <div style="display: flex; gap: 12px;">
            <a href="{{ route('trainee.certificates.download', $certificate) }}"
               style="background: rgba(255,255,255,0.12); color: #ffffff !important; text-decoration: none;
                      border: 1px solid #475569; padding: 8px 16px; border-radius: 6px;
                      font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                📥 Download Certificate
            </a>
            <button onclick="window.print()"
                    style="background: rgba(255,255,255,0.12); color: #ffffff; border: 1px solid #475569;
                           padding: 8px 16px; border-radius: 6px; font-size: 13px; font-weight: 600;
                           cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                🖨 Print / Export PDF
            </button>
            <a href="{{ route('certificate.verify', $certificate->verification_code) }}" target="_blank"
               style="background: #2563eb; color: #ffffff !important; text-decoration: none;
                      padding: 8px 16px; border-radius: 6px; font-size: 13px; font-weight: 600;
                      display: inline-flex; align-items: center; gap: 6px;">
                Public Verification Link 🔗
            </a>
        </div>
    </div>

    <!-- Official Printable Certificate Document Frame -->
    <div id="printable-certificate" style="background: #ffffff; color: #0f172a; border-radius: 16px; padding: 48px; border: 12px solid #0f2744; position: relative; box-shadow: 0 15px 40px rgba(0,0,0,0.5); font-family: 'Inter', sans-serif;">
        <!-- Background Crest & Watermark -->
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); opacity: 0.04; font-size: 260px; pointer-events: none; font-weight: 900; color: #0f2744;">
            CC
        </div>

        <!-- Header Seals -->
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #cbd5e1; padding-bottom: 20px; margin-bottom: 32px;">
            <div style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 48px; height: 48px; background: #0f2744; color: #fff; font-weight: 800; font-size: 20px; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                    CC
                </div>
                <div>
                    <h3 style="font-size: 18px; font-weight: 800; color: #0f2744; margin: 0; letter-spacing: 1px;">CAPACITY CONNECT</h3>
                    <span style="font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 600;">National Emergency Capacity Building Board</span>
                </div>
            </div>

            <div style="text-align: right;">
                <span class="badge" style="background: #dcfce7; color: #166534; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 700;">
                    VERIFIED AUTHENTIC
                </span>
            </div>
        </div>

        <!-- Certificate Title -->
        <div style="text-align: center; margin-bottom: 36px;">
            <span style="font-size: 13px; font-weight: 700; color: #2563eb; text-transform: uppercase; letter-spacing: 2px; display: block; margin-bottom: 8px;">
                OFFICIAL CERTIFICATE OF COMPETENCY
            </span>
            <h1 style="font-size: 32px; font-weight: 900; color: #0f2744; margin: 0 0 16px 0; font-family: serif;">
                Certificate of Completion
            </h1>
            <p style="font-size: 14px; color: #475569; margin: 0;">This is to certify that</p>
        </div>

        <!-- Trainee Name -->
        <div style="text-align: center; margin-bottom: 36px;">
            <h2 style="font-size: 30px; font-weight: 800; color: #1e3a8a; margin: 0 0 10px 0; border-bottom: 2px solid #3b82f6; display: inline-block; padding: 0 32px 8px 32px;">
                {{ $certificate->user?->name }}
            </h2>
            <p style="font-size: 15px; color: #334155; max-width: 650px; margin: 16px auto 0 auto; line-height: 1.6;">
                has successfully fulfilled all curriculum requirements, practical simulations, and server-side competency evaluations for the certified course:
            </p>
            <h3 style="font-size: 22px; font-weight: 800; color: #0f2744; margin: 16px 0 0 0;">
                {{ $certificate->course?->title }}
            </h3>
        </div>

        <!-- Signatures & Metadata Footer -->
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-top: 48px; border-top: 2px solid #cbd5e1; padding-top: 24px;">
            <div style="text-align: left;">
                <div style="font-family: cursive; font-size: 22px; color: #1e3a8a; font-weight: 700; margin-bottom: 4px;">
                    {{ $certificate->course?->trainer?->name ?? 'Chief Capacity Officer' }}
                </div>
                <div style="font-size: 12px; font-weight: 700; color: #0f2744;">Master Trainer & Evaluator</div>
                <div style="font-size: 11px; color: #64748b;">Capacity Building Division</div>
            </div>

            <div style="text-align: center; background: #f8fafc; padding: 12px 20px; border-radius: 8px; border: 1px solid #e2e8f0;">
                <div style="font-size: 11px; color: #64748b;">Certificate Unique Code</div>
                <div style="font-size: 14px; font-weight: 800; color: #0f2744; font-family: monospace;">{{ $certificate->certificate_number ?? $certificate->certificate_code }}</div>
                <div style="font-size: 10px; color: #94a3b8; margin-top: 4px;">Issued: {{ $certificate->issue_date ? $certificate->issue_date->format('M d, Y') : 'Active' }}{{ $certificate->expiry_date ? ' • Expires: ' . $certificate->expiry_date->format('M d, Y') : ' • Permanent Validity' }}</div>
            </div>

            <div style="text-align: right;">
                <div style="width: 70px; height: 70px; background: #0f2744; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #f59e0b; font-weight: 900; font-size: 24px; margin-left: auto; margin-bottom: 6px; box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
                    SEAL
                </div>
                <div style="font-size: 10px; color: #64748b; font-family: monospace;">CRYPTOGRAPHICALLY VERIFIED</div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('title', 'System & Security Settings')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px; max-width: 800px;">
    <!-- Header -->
    <div style="background: #1e293b; padding: 24px; border-radius: 14px; border: 1px solid #334155;">
        <span class="badge" style="background: rgba(59,130,246,0.2); color: #60a5fa; border: 1px solid rgba(59,130,246,0.4); margin-bottom: 6px; display: inline-block;">
            ⚙️ System Administration
        </span>
        <h1 style="font-size: 24px; font-weight: 800; color: #f8fafc; margin: 0 0 4px 0;">Platform & Security Governance</h1>
        <p style="font-size: 14px; color: #94a3b8; margin: 0;">Configure global two-factor authentication mandates, AI model studio settings, and trainer governance policies.</p>
    </div>

    <!-- Settings Form -->
    <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 28px;">
        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf

            <!-- Section 1: Security -->
            <div style="margin-bottom: 28px;">
                <h3 style="font-size: 16px; font-weight: 700; color: #f8fafc; margin: 0 0 16px 0; border-bottom: 1px solid #334155; padding-bottom: 8px;">
                    🛡️ Authentication & Security Controls
                </h3>

                <div style="display: flex; flex-direction: column; gap: 14px;">
                    <label style="display: flex; align-items: center; justify-content: space-between; background: #0f172a; padding: 14px 18px; border-radius: 10px; border: 1px solid #334155; cursor: pointer;">
                        <div>
                            <div style="font-size: 14px; font-weight: 700; color: #f8fafc;">Mandatory Two-Factor Authentication (2FA / OTP)</div>
                            <div style="font-size: 12px; color: #94a3b8;">Require all admin and trainer logins to pass one-time passcode verification.</div>
                        </div>
                        <input type="checkbox" name="require_2fa" value="1" {{ $settings['require_2fa'] ? 'checked' : '' }} style="width: 20px; height: 20px;">
                    </label>

                    <label style="display: flex; align-items: center; justify-content: space-between; background: #0f172a; padding: 14px 18px; border-radius: 10px; border: 1px solid #334155; cursor: pointer;">
                        <div>
                            <div style="font-size: 14px; font-weight: 700; color: #f8fafc;">Automated Trainer Registration Approval</div>
                            <div style="font-size: 12px; color: #94a3b8;">When disabled, newly registered trainers remain in pending status until Admin approval.</div>
                        </div>
                        <input type="checkbox" name="trainer_auto_approval" value="1" {{ $settings['trainer_auto_approval'] ? 'checked' : '' }} style="width: 20px; height: 20px;">
                    </label>
                </div>
            </div>

            <!-- Section 2: AI Settings -->
            <div style="margin-bottom: 28px;">
                <h3 style="font-size: 16px; font-weight: 700; color: #f8fafc; margin: 0 0 16px 0; border-bottom: 1px solid #334155; padding-bottom: 8px;">
                    🤖 AI Assistant & Simulation Engines
                </h3>

                <div style="display: flex; flex-direction: column; gap: 14px;">
                    <label style="display: flex; align-items: center; justify-content: space-between; background: #0f172a; padding: 14px 18px; border-radius: 10px; border: 1px solid #334155; cursor: pointer;">
                        <div>
                            <div style="font-size: 14px; font-weight: 700; color: #f8fafc;">Enable AI Assistant & Simulation Generator</div>
                            <div style="font-size: 12px; color: #94a3b8;">Allow trainees and trainers to leverage the Capacity AI Studio.</div>
                        </div>
                        <input type="checkbox" name="ai_enabled" value="1" {{ $settings['ai_enabled'] ? 'checked' : '' }} style="width: 20px; height: 20px;">
                    </label>

                    <div style="background: #0f172a; padding: 14px 18px; border-radius: 10px; border: 1px solid #334155;">
                        <label style="display: block; font-size: 12px; color: #94a3b8; margin-bottom: 6px;">Active LLM Inference Model</label>
                        <select name="ai_model" class="form-control" style="width: 100%; padding: 10px; background: #1e293b; border: 1px solid #334155; border-radius: 8px; color: #fff;">
                            <option value="Gemini 2.5 Flash / Claude 3.5 Sonnet" selected>Gemini 2.5 Flash / Claude 3.5 Sonnet (Enterprise Optimized)</option>
                            <option value="GPT-4o / Azure OpenAI">GPT-4o / Azure OpenAI</option>
                            <option value="Local DeepSeek R1 Disaster Model">Local DeepSeek R1 Disaster Model (Air-gapped)</option>
                        </select>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="background: #2563eb; color: #fff; padding: 12px 24px; border-radius: 8px; font-weight: 700; border: none; cursor: pointer;">
                Save Governance Configuration ➔
            </button>
        </form>
    </div>
</div>
@endsection

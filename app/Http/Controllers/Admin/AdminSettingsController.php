<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSettingsController extends Controller
{
    public function index(): View
    {
        $settings = [
            'app_name' => config('app.name', 'Capacity Connect'),
            'require_2fa' => session('admin_setting_require_2fa', false),
            'trainer_auto_approval' => session('admin_setting_trainer_auto_approval', false),
            'ai_enabled' => session('admin_setting_ai_enabled', true),
            'ai_model' => session('admin_setting_ai_model', 'Gemini 2.5 Flash / Claude 3.5 Sonnet'),
            'maintenance_mode' => app()->isDownForMaintenance(),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $require2fa = $request->boolean('require_2fa');
        $trainerAuto = $request->boolean('trainer_auto_approval');
        $aiEnabled = $request->boolean('ai_enabled');
        $aiModel = $request->input('ai_model', 'Gemini 2.5 Flash / Claude 3.5 Sonnet');

        session([
            'admin_setting_require_2fa' => $require2fa,
            'admin_setting_trainer_auto_approval' => $trainerAuto,
            'admin_setting_ai_enabled' => $aiEnabled,
            'admin_setting_ai_model' => $aiModel,
        ]);

        AuditLogService::log('settings.updated', auth()->user(), null, [
            'require_2fa' => $require2fa,
            'trainer_auto_approval' => $trainerAuto,
            'ai_enabled' => $aiEnabled,
        ]);

        return back()->with('success', 'System governance settings updated successfully!');
    }
}
